<?php


namespace IceCloud\RpcClient\Lib;


use GuzzleHttp\Client;
use IceCloud\RpcClient\Lib\Exceptions\Detailed\InvalidResponseException;
use IceCloud\RpcClient\Lib\Responses\RpcResponse;
use Illuminate\Support\Facades\Validator;

class RpcClient
{
    const ATTR_VERSION = 'jsonrpc';
    const ATTR_METHOD = 'method';
    const ATTR_ARGUMENTS = 'params';
    const ATTR_ID = 'id';

    private string $entrypoint;
    private string $version;
    private array $meta = [];

    public function __construct(string $entrypoint, string $version)
    {
        $this->version = $version;
        $this->entrypoint = $entrypoint;
    }

    /**
     * @param array $meta
     * @return $this
     */
    public function meta(array $meta)
    {
        $this->meta = array_merge_recursive($this->meta, $meta);
        return $this;
    }

    public function getVersion() : string
    {
        return $this->version;
    }

    public function getEntrypoint(): string
    {
        return $this->entrypoint;
    }

    public function createGuzzleClient(): Client
    {
        return new Client();
    }

    protected function createResponse(RpcRequest $request, $body): RpcResponse
    {
        return new RpcResponse($request, $body);
    }

    public function call(string $method, array $arguments, bool $asNotification = false) : RpcResponse
    {
        $request = new RpcRequest($this, $method, $arguments, $this->meta, $asNotification);

        $httpResponse = $this->createGuzzleClient()->request(
            "POST",
            $this->entrypoint,
            [
                "json" => $request->toArray()
            ]
        );

        if ($httpResponse->getStatusCode() !== 200) {
            throw new InvalidResponseException("Сервер вернул код {$httpResponse->getStatusCode()}. Ожидался код 200.");
        }

        $body = json_decode($httpResponse->getBody()->getContents(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidResponseException("Ответ не содержит JSON контент");
        }

        return $this->createResponse($request, $body);
    }

}
