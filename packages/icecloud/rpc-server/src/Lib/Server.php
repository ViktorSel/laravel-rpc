<?php


namespace IceCloud\RpcServer\Lib;

use IceCloud\RpcServer\Lib\Contracts\RequestHolderContract;
use IceCloud\RpcServer\Lib\Exceptions\Handling\BreakException;
use IceCloud\RpcServer\Lib\Exceptions\Handling\HandlingException;
use IceCloud\RpcServer\Lib\Exceptions\Server\InvalidProcedureInstanceException;
use IceCloud\RpcServer\Lib\Exceptions\Server\ProcedureNameConflictException;
use IceCloud\RpcServer\Lib\Procedure;
use IceCloud\RpcServer\Lib\Response;
use IceCloud\RpcServer\Lib\Responses\ErrorResponse;
use IceCloud\RpcServer\Lib\Responses\Errors\InternalErrorResponse;
use IceCloud\RpcServer\Lib\Responses\Errors\InvalidRequestResponse;
use IceCloud\RpcServer\Lib\Responses\Errors\MethodNotFoundResponse;
use IceCloud\RpcServer\Lib\Responses\Errors\ParsingErrorResponse;
use IceCloud\RpcServer\Lib\Responses\SuccessfulResponse;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionException;
use Symfony\Component\Finder\Finder;

/**
 * Абстрактный класс сервера, для которого необходимо делать реализации.
 * Это сделано для того, чтобы локализовать и персонифицировать эндпоинты к разным конструктам серверов.
 * Например, Rpc для фронта и Rpc для админки - это два разных скоупа.
 *
 * @package IceCloud\Rpc\Server
 * @author a.kazakov
 *
 */
abstract class Server implements RequestHolderContract
{
    private array $procedures = [];

    /**
     * Карта пространств имен. Задача простая - группировать процедуры по неквалифицированному пути, например
     *
     * MyService.* => массив всех процедур сервиса
     * MyService.V1 => массив всех процедур первой версии сервиса
     * etc
     *
     * @var array
     */
    private array $namespaceMap = [];

    const PROTOCOL_VERSION_1_0 = "1.0";
    const PROTOCOL_VERSION_2_0 = "2.0";

    private string $protocolVersion;

    private static ?Request $request = null;

    /**
     * Получить текущий инстанс реквеста, если выполняется команда RPC
     * @return Request|null
     */
    public static function request(): ?Request
    {
        return self::$request;
    }

    public function getRequest(): Request
    {
        return self::$request;
    }

    public function __construct(string $protocolVersion = self::PROTOCOL_VERSION_2_0)
    {
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * Версия протокола. Больше не использовать.
     * @return string
     * @deprecated
     * @removed
     */
    public function getVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * Версия протокола
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * Получить все процедуры сервера
     * @return Procedure[]
     */
    public function getProcedures(): array
    {
        return $this->procedures;
    }

    /**
     * Загрузка процедур из пути (Конфиг rpc-server.proceduresPath).
     * Имена будут построены в соответствии со спецификацией namespaces - Процедура App\\Rpc\\Procedures\\MyService\\V1\\Common\\SetVar
     * будет преобразована в имя MyService.V1.Common.SetVar
     * @return Server
     * @deprecated
     * @throws ReflectionException
     * @throws BindingResolutionException|ProcedureNameConflictException
     */
    public function load(): Server
    {
        return $this->loadFromPath(
            config('rpc-server.proceduresPath'),
            app()->getNamespace(),
            app_path()
        );
    }

    /**
     * Загрузка из неконфигурируемого пути
     * @param string $path
     * @return $this
     * @throws BindingResolutionException
     * @throws InvalidProcedureInstanceException
     * @throws ProcedureNameConflictException
     * @throws ReflectionException
     */
    public function loadFromPath(string $path, string $rootNamespace, string $rootPath): Server {
        foreach ((new Finder)->in($path)->files() as $file) {

            $class = $rootNamespace . str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($file->getRealPath(), realpath($rootPath) . DIRECTORY_SEPARATOR)
                );

            if ($this->hasProcedureClass($class)) {
                $this->addProcedure($class);
            }
        }

        return $this;
    }

    /**
     * Является ли класс Rpc процедурой
     * @param string $class
     * @return bool
     * @throws ReflectionException
     */
    public function hasProcedureClass(string $class): bool
    {
        return is_subclass_of($class, Procedure::class) && !(new \ReflectionClass($class))->isAbstract();
    }

    /**
     * Добавить класс процедуры
     * @param string|Procedure $classOrInstance
     * @return Procedure
     * @throws BindingResolutionException|ProcedureNameConflictException|InvalidProcedureInstanceException
     */
    public function addProcedure($classOrInstance): Procedure
    {
        $procedure = is_string($classOrInstance)
            ? app()->make($classOrInstance)
            : $classOrInstance; /* @var $procedure Procedure */

        if (!$procedure instanceof Procedure) {
            throw new InvalidProcedureInstanceException($procedure);
        }

        if ($this->hasProcedureExists($procedure->name()) || $this->hasNamespaceExists($procedure->name())) {
            throw new ProcedureNameConflictException($procedure->name());
        }

        $procedure->boot($this);
        $this->pushProcedureToMap($procedure);
        $this->procedures[$procedure->name()] = $procedure;

        return $procedure;
    }

    private function pushProcedureToMap(Procedure $procedure)
    {
        $path = explode('.', $procedure->name());
        array_pop($path); // Имя процедуры выкидываем

        $walk = [];
        foreach ($path as $item) {
            $walk []= $item;
            $namespace = implode('.', $walk);
            if (!array_key_exists($namespace, $this->namespaceMap)) {
                $this->namespaceMap[$namespace] = [];
            }
            $this->namespaceMap[$namespace][] = $procedure;
        }
    }

    /**
     * Найти процедуру по имени
     * @param string $name
     * @return Procedure|null
     */
    public function findProcedureByStrictName(string $name): ?Procedure
    {
        return $this->procedures[$name] ?? null;
    }

    /**
     * Найти все процедуры из пространства имен
     *
     * @param string $namespace
     * @return array|null
     */
    public function findProceduresByNamespace(string $namespace): ?array
    {
        return $this->namespaceMap[$namespace] ?? null;
    }

    /**
     * Существует ли пространство имен
     * @param string $namespace
     * @return bool
     */
    public function hasNamespaceExists(string $namespace): bool
    {
        return array_key_exists($namespace, $this->namespaceMap);
    }

    /**
     * Существует ли процедура
     * @param string $name
     * @return bool
     */
    public function hasProcedureExists(string $name): bool
    {
        return array_key_exists($name, $this->procedures);
    }

    /**
     * Получить путь к папке с кешированными схемами валидации процедур
     * @return string
     */
    public function getSchemaCachePath(): string
    {
        return config('rpc-server.schemaCachePath');
    }

    /**
     * Обработчик
     * @param HttpRequest $httpRequest
     * @return Response[]|Response|null
     */
    public function handle(HttpRequest $httpRequest)
    {
        Constraint::boot();

        // Обработка битого JSON
        if (!$httpRequest->isJson()) {
            return new ParsingErrorResponse($this->protocolVersion);
        }

        $data = $httpRequest->json()->all();

        // Если получили ассоциативный массив, значит вызов единичный
        if (Arr::isAssoc($data)) {
            return $this->handleOnce($data, $httpRequest);
        }

        return $this->handleBatch($data, $httpRequest);
    }

    /**
     * На входе структура вызова
     * @param array $data
     * @param HttpRequest $request
     * @return array|null
     */
    protected function handleOnce(array $data, \Illuminate\Http\Request $request) : ?array
    {
        $response = $this->callProcedure($data, $request);

        return $response instanceof Response
            ? $response->toArray()
            : null;
    }

    /**
     * На входе массив структур вызовов
     * @param array $data
     * @param HttpRequest $request
     * @return array
     */
    protected function handleBatch(array $data, \Illuminate\Http\Request $request) : array
    {
        $responses = [];
        foreach ($data as $parameters) {
            $response = $this->callProcedure($parameters, $request);

            if ($response instanceof Response) {
                $responses[] = $response->toArray();
            }
        }
        return $responses;
    }

    /**
     * Вызов процедуры. Подразумевается, что request
     * правильный и ошибки парсинга отсутствуют.
     *
     * @param array $parameters
     * @param HttpRequest $httpRequest
     * @return Response
     */
    private function callProcedure(array $parameters, \Illuminate\Http\Request $httpRequest) : ?Response
    {
        // Проверка на консистент тела
        $request = new Request($this->protocolVersion, $parameters, $httpRequest);

        self::$request = $request;

        if ($request->hasFailed()) {
            return new InvalidRequestResponse($this->protocolVersion);
        }

        $procedure = $this->findProcedureByStrictName(
            $request->getMethod()
        );

        if ($procedure === null) {
            return new MethodNotFoundResponse($request->getId(), $this->protocolVersion);
        }

        try {
            $response = new SuccessfulResponse(
                $request->getId(),
                $this->protocolVersion,
                $procedure->call($request)
            );

        } catch (BreakException $exception) {
            // Прерывание
            return null;
        } catch (HandlingException $exception) {

            $response = new ErrorResponse(
                $request->getId(),
                $this->protocolVersion,
                $exception->getCode(),
                $exception->getMessage(),
                $exception->getData()
            );

        } catch (\Throwable $exception) {

            $response = new InternalErrorResponse(
                $request->getId(),
                $this->protocolVersion,
                config('app.debug')
                    ? $exception->getMessage()
                    : null
            );

            report($exception);
        }

        return !$request->hasNotificationCall()
            ? $response
            : null;
    }
}
