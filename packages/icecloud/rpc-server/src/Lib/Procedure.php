<?php


namespace IceCloud\RpcServer\Lib;


use IceCloud\RpcServer\Lib\Contracts\RequestHolderContract;
use IceCloud\RpcServer\Lib\Contracts\Schema\SchemaContainerContract;
use IceCloud\RpcServer\Lib\Exceptions\Handling\Detailed\InvalidParamsException;
use IceCloud\RpcServer\Lib\Exceptions\RpcException;
use IceCloud\RpcServer\Lib\Models\CompiledValidationInstructions;
use IceCloud\RpcServer\Lib\Schema\ArgumentsSchema;
use IceCloud\RpcServer\Lib\Schema\ResultSchema;
use IceCloud\RpcServer\Lib\Schema\Workers\DefaultValuesFiller;
use IceCloud\RpcServer\Lib\Schema\Workers\SchemaAccumulator;
use IceCloud\RpcServer\Lib\Cache\ModelCache;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Validator;

/**
 * Абстракция RPC процедуры.
 * Спека по протоколу {@link http://devrepo.icecorp.ru/docs/dev/-/blob/master/json-rpc/INDEX.md}
 * Спека по сборке сервера {@link http://devrepo.icecorp.ru/docs/dev/-/blob/master/json-rpc/PHP.md}
 * @package IceCloud\RpcServer\Core
 * @author a.kazakov
 *
 *
 */
abstract class Procedure implements SchemaContainerContract, RequestHolderContract
{
    /**
     * @var Server
     */
    private Server $server;

    /**
     * Инстанс реквеста, доступен только во время выполнения {@link \Icecms\Admin\Rpc\Cpi\Procedure::handle()} или этапа валидации
     * входных параметров (допустимо использовать в custom валидаторах)
     * @var Request|null
     */
    protected ?Request $request = null;

    /**
     * Бут функция, которая вызывается сервером при создании инстанса процедуры
     * @param Server $server
     */
    public function boot(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Описание процедуры, которое будет использовано для генерации swagger и комментариев клиента.
     * @return string
     */
    abstract public function description() : string;

    /**
     * Нужен ли детализированный вывод ошибок (ДДО) - это, как правило,
     * нужно для фронтенда с сервер-сайд валидацией
     *
     * @return bool
     */
    abstract public function hasDetailedValidationErrors(): bool;

    /**
     * Имя процедуры.
     * Должна соответствовать регламенту "каталогизации процедур".
     * Имя категории должно соответствовать имени проекта, например
     *
     * - IceCloud.*
     * - 1C.*
     * - AOS.*
     *
     * Второй уровень должен соответствовать версии блока процедур, например
     *
     * - IceCloud.v1.*
     *
     * Далее категории распространяются по удобству разделения.
     *
     * Примеры имен процедур,
     *
     * - IceCloud.v1.PersonalArea.Client.Info
     * - AOS.v1.AddressesTree.Find
     *
     * @return string
     */
    abstract public function name(): string;

    /**
     * Обработчик процедуры
     * @param Request $request
     * @return mixed
     */
    abstract public function handle(Request $request);

    /**
     * Получить инстанс сервера
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * Мидлвайры процедуры
     * @return array
     */
    public function middlewares(): array
    {
        return [];
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Вызвать процедуру.
     * Подразумевается, что реквест уже проверен на ошибки
     * парсинга и формат вызова.
     * Метод должен реализовывать выполнение пайплайна вызова и
     * валидацию аргументов перед вызовом {@link ProcedureContract::handle()}.
     *
     * @param Request $request
     * @return mixed
     * @throws InvalidParamsException
     * @throws BindingResolutionException
     */
    public function call(Request $request)
    {
        $this->request = $request;
        $middlewares = $this->createMiddlewares();

        foreach ($middlewares as $middleware) {
            $middleware->request = $request;
        }

        $this->prepareAndValidateArguments($request);
        // Прошли валидацию можно идти дальше

        $pipes = array_merge($middlewares, [$this]);

        $pipeline = app(Pipeline::class);
        return $pipeline
            ->send($request)
            ->through($pipes)
            ->thenReturn();
    }


    /**
     * @var ArgumentsSchema|null
     */
    private ?ArgumentsSchema $pipelineArgumentsSchema = null;

    /**
     * Получить схему аргументов всего пайплайна вызова
     * @return ArgumentsSchema
     * @throws BindingResolutionException
     */
    public function getPipelineArgumentsSchema(): ArgumentsSchema
    {
        if ($this->pipelineArgumentsSchema === null) {
            $this->pipelineArgumentsSchema = (new SchemaAccumulator)
                ->pushAll($this->createMiddlewares())
                ->push($this)
                ->buildProcedureArgumentsSchema();
        }
        return $this->pipelineArgumentsSchema;
    }

    /**
     * @var ResultSchema|null
     */
    private ?ResultSchema $pipelineResultSchema = null;

    public function getPipelineResultSchema(): ResultSchema
    {
        if ($this->pipelineResultSchema === null) {
            $this->pipelineResultSchema = (new SchemaAccumulator)
                ->pushAll($this->createMiddlewares())
                ->push($this)
                ->buildProcedureResultSchema();
        }
        return $this->pipelineResultSchema;
    }

    /**
     * Подготовка и проверка аргументов на наличие ошибок.
     * Если будут встречены ошибки - будет брошено исключение {@link InvalidParamsException}
     *
     * @param Request $request
     * @throws InvalidParamsException
     * @throws BindingResolutionException
     */
    private function prepareAndValidateArguments(Request $request) {
        // Кеш автоматической валидации
        $cache = new ModelCache(
            $this->server->getSchemaCachePath(),
            $this
        );

        // Получаем данные для валидатора из кеша
        // TODO Кеш дерьмо и правила дерьмо, нужно ускорять и применять кеш по другому
//        $validationInstructions = $cache->remember(function () {
//            return $this
//                ->getPipelineArgumentsSchema()
//                ->compileValidationInstructions();
//        });
        $validationInstructions = $this
            ->getPipelineArgumentsSchema()
            ->compileValidationInstructions();

        $validator = Validator::make(
            $request->getParams(),
            $validationInstructions->getRules(),
            $validationInstructions->getMessages()
        );


//        dd($validationInstructions);
        // Если все ок ничего не бросаем
        if (!$validator->fails()) {
            $validationInstructions instanceof CompiledValidationInstructions && $request->applyDefaultValues($validationInstructions);
            return;
        }

        // Если нужна детализация ошибок
        if ($this->hasDetailedValidationErrors()) {
            throw new InvalidParamsException(
                $validator->getMessageBag()->first(),
                $validator->getMessageBag()->toArray()
            );
        }

        // В ином случае бросаем стандартное исключение
        throw new InvalidParamsException(
            $validator->getMessageBag()->first()
        );
    }

    /**
     * Создать middlewares из схемы {@link Procedure::middlewares()}
     * @return Middleware[]
     * @throws BindingResolutionException|RpcException
     */
    public function createMiddlewares() : array {
        $middlewares = [];

        foreach ($this->middlewares() as $config) {
            $config = is_array($config) ? $config : [$config];
            $class = array_shift($config);

            $middlewares[] = $middleware = app()->make($class, $config);

            if (!$middleware instanceof Middleware) {
                throw new RpcException(sprintf(
                    "Middleware %s должен быть унаследован от %s",
                    get_class($middleware),
                    Middleware::class
                ));
            }

            $middleware->procedure = $this;
        }
        return $middlewares;
    }

    /**
     * Кеш полного имени
     * @var string|null
     */
    private $fullname;

    /**
     * Получить полное квалифицированное имя
     * @return string
     */
    public function getFullQualifiedName() : string {
        return $this->fullname === null ? $this->fullname = $this->name() : $this->fullname;
    }

    /**
     * Кеш короткого имени
     * @var string|null
     */
    private $shortname;

    /**
     * Получить короткое имя процедуры (без пространства имен)
     * @return string
     */
    public function getEndName() : string {
        if ($this->shortname === null) {
            $chunks = explode('.', $this->getFullQualifiedName());
            $this->shortname = array_pop($chunks);
        }
        return $this->shortname;
    }

    /**
     * Возвращает полное имя процедуры без указания сервиса (рута)
     * @return string
     */
    public function getFullQualifiedNameWithoutService() : string {
        $chunks = explode('.',$this->getFullQualifiedName());
        array_shift($chunks);
        return implode('.', $chunks);
    }

    public function getNameScope() : string
    {
        $chunks = explode('.', $this->getFullQualifiedName());

        if (count($chunks) > 2) {
            $chunks = array_slice($chunks, 2);
        }

        return implode('', array_map(function ($name) {
            return ucfirst($name);
        }, $chunks));
    }

    public function includeModelCacheWithSelfScope(ModelCache $cache) {
        return include $cache->getCacheFilename();
    }
}
