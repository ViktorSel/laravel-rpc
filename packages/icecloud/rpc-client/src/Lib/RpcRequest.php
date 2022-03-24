<?php


namespace IceCloud\RpcClient\Lib;


use IceCloud\RpcClient\Lib\Exceptions\Detailed\InvalidResponseException;
use IceCloud\RpcClient\Lib\Responses\RpcResponse;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;

/**
 * Класс запроса
 *
 * ID запроса формируется на основе hrtime. Т.е. работать это будет на x64 и >=php7.4
 *
 * @package IceCloud\RpcClient\Lib
 * @author a.kazakov <a.kazakov@iceberg.ru>
 *
 *
 */
class RpcRequest implements Arrayable
{
    public static int $incremental = 0;

    const ATTR_VERSION = 'jsonrpc';
    const ATTR_METHOD = 'method';
    const ATTR_ARGUMENTS = 'params';
    const ATTR_ID = 'id';
    const ATTR_META = 'meta';


    private RpcClient $client;
    private string $method;
    private array $arguments = [];
    private array $meta = [];
    private ?int $id;

    public function getId() : ?int
    {
        return $this->id;
    }

    public function isNotificationCall(): bool
    {
        return $this->id === null;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->client->getVersion();
    }

    /**
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    public function __construct(RpcClient $client, string $method, array $arguments, array $meta = [], bool $asNotification = false)
    {
        $this->client = $client;
        $this->method = $method;
        $this->arguments = $arguments;
        $this->meta = $meta;
        $this->id = !$asNotification
            ? (int)hrtime(true)
            : null;
    }

    public function setArguments(array $arguments): self
    {
        $this->arguments = $arguments;
        return $this;
    }

    public function toArray(): array
    {
        return [
            self::ATTR_VERSION => $this->client->getVersion(),
            self::ATTR_METHOD => $this->method,
            self::ATTR_ARGUMENTS => $this->arguments,
            self::ATTR_META => $this->meta,
            self::ATTR_ID => $this->id
        ];
    }
}
