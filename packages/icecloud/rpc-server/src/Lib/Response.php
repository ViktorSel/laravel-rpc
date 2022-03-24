<?php


namespace IceCloud\RpcServer\Lib;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Абстракция ответа сервера
 *
 * @package IceCloud\RpcServer\Lib
 * @author a.kazakov
 */
abstract class Response implements Arrayable
{
    const ATTR_VERSION = "jsonrpc";
    const ATTR_RESULT = "result";
    const ATTR_ERROR = "error";
    const ATTR_ID = "id";

    const PARSING_ERROR_CODE = -32700;
    const INVALID_REQUEST_CODE = -32600;
    const METHOD_NOT_FOUND_CODE = -32601;
    const INVALID_PARAMS_CODE = -32602;
    const INTERNAL_ERROR_CODE = -32603;

    // RPC PROXY BLOCK
    // клиент не авторизован (нет токена, токен не подошел)
    const NOT_AUTHORIZED_401 = -32200;
    // троттлинг
    const TOO_MANY_REQUESTS_429 = -32201;
    // RPC PROXY BLOCK
    const FORBIDDEN_403 = -32202;
    // RPC PROXY BLOCK

    protected array $body = [];

    /**
     * RpcResponse constructor.
     * @param null|int $id ID вызова
     * @param string $version Версия RPC
     */
    public function __construct(?int $id, string $version)
    {
        $this->body[self::ATTR_ID] = $id;
        $this->body[self::ATTR_VERSION] = $version;
    }

    /**
     * Установить состояние ошибки
     * @param int $code
     * @param string $message
     * @param array|null $data
     */
    protected function setError(int $code, string $message, ?array $data = null)
    {
        $this->body[self::ATTR_ERROR] = [
            "code" => $code,
            "message" => $message,
            "data" => $data
        ];
    }

    /**
     * Установить состояние результата
     * @param $result
     */
    protected function setResult($result)
    {
        $this->body[self::ATTR_RESULT] = $result;
    }

    public function toArray(): array
    {
        return $this->body;
    }
}
