<?php


namespace IceCloud\RpcServer\Lib;


use IceCloud\RpcServer\Lib\Models\CompiledValidationInstructions;
use IceCloud\RpcServer\Lib\Schema\Workers\DefaultValuesFiller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;

/**
 * Class Request
 * @package IceCloud\RpcServer
 * @author a.kazakov
 */
class Request
{
    const ATTR_VERSION = 'jsonrpc';
    const ATTR_METHOD = 'method';
    const ATTR_PARAMS = 'params';
    const ATTR_ID = 'id';
    const ATTR_META = 'meta';

    protected $id;
    protected $params;
    protected $method;
    protected array $meta;
    protected $httpRequest;

    protected bool $failed = false;

    /**
     * Request constructor.
     * @param string $version Версия, в которой требуется проверка структуры реквеста
     * @param array $body Тело запроса
     * @param \Illuminate\Http\Request $httpRequest
     */
    public function __construct(string $version, array $body, \Illuminate\Http\Request $httpRequest)
    {
        $this->httpRequest = $httpRequest;
        $validator = Validator::make($body, [
            self::ATTR_VERSION => ["required", new In([$version])],
            self::ATTR_PARAMS => ["array"],
            self::ATTR_METHOD => ["required", "string", "max:255"],
            self::ATTR_ID => ["nullable", "integer"],
            self::ATTR_META => ['nullable', 'array']
        ]);

        if ($validator->fails()) {
            $this->failed = true;
        } else {
            $this->method = $body[self::ATTR_METHOD];
            $this->params = $body[self::ATTR_PARAMS] ?? [];
            $this->id = $body[self::ATTR_ID] ?? null;
            $this->meta = $body[self::ATTR_META] ?? [];
        }
    }

    /**
     * Get Original http request
     * @return \Illuminate\Http\Request
     */
    public function getHttpRequest(): \Illuminate\Http\Request
    {
        return $this->httpRequest;
    }

    /**
     * Получить имя метода
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Получить аргументы вызова
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Получить ID вызова
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Прошла ли структура проверку
     * @return bool
     */
    public function hasFailed(): bool
    {
        return $this->failed;
    }

    /**
     * Получить метаданные запроса
     *
     * @param string|null $path
     * @param null $default
     * @return array|\ArrayAccess|mixed
     */
    public function meta(?string $path = null, $default = null)
    {
        return Arr::get($this->meta, $path, $default);
    }

    /**
     * Получить аргумент. Должно поддерживаться получение аргумента по пути в формате "dot separated".
     * @param null|string $name
     * @param null $default
     * @return mixed
     */
    public function input(?string $name = null, $default = null)
    {
        return Arr::get($this->params, $name, $default);
    }

    /**
     * Вызов в формате нотификации, когда отвечать клиенту не нужно
     * @return bool
     */
    public function hasNotificationCall(): bool
    {
        return $this->id === null;
    }

    /**
     * Указан ли параметр
     * @param string $name
     * @return bool
     */
    public function present(string $name): bool
    {
        return Arr::has($this->params, $name);
    }

    public function applyDefaultValues(CompiledValidationInstructions $instructions) {
        (new DefaultValuesFiller($instructions))
            ->fill($this->params);
    }
}
