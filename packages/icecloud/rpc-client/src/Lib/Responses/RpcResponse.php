<?php


namespace IceCloud\RpcClient\Lib\Responses;

use IceCloud\RpcClient\Lib\Exceptions\Detailed\InvalidResponseException;
use IceCloud\RpcClient\Lib\RpcRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;

class RpcResponse
{
    const ATTR_VERSION = "jsonrpc";
    const ATTR_ID = "id";
    const ATTR_ERROR = "error";
    const ATTR_RESULT = "result";

    private string $version;
    private $result;
    protected ?ErrorData $error = null;
    private ?int $id;

    private RpcRequest $request;

    protected function createErrorData(array $error): ErrorData
    {
        return new ErrorData($error);
    }

    public function __construct(RpcRequest $request, array $body)
    {
        $this->request=$request;

        $this->result = $body[self::ATTR_RESULT] ?? null;
        if (array_key_exists(self::ATTR_ERROR, $body)) {
            $this->error = $this->createErrorData($body[self::ATTR_ERROR]);
        }
        $this->id = $body[self::ATTR_ID] ?? null;
        $this->version = $body[self::ATTR_VERSION];
    }

    /**
     * @return int|mixed
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed|string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return ErrorData|null
     */
    public function getError(): ?ErrorData
    {
        return $this->error;
    }

    public function failed(): bool
    {
        return $this->error !== null;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    public function output(?string $path = null, $default = null)
    {
        return Arr::get($this->result, $path, $default);
    }

    public function raw(): array {
        return [
            'id'=>$this->id,
            'result'=>$this->result,
            'error'=>$this->error
        ];
    }

}
