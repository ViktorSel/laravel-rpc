<?php


namespace IceCloud\RpcServer\Lib\Exceptions\Handling;

use IceCloud\RpcServer\Lib\Exceptions\RpcException;

class HandlingException extends RpcException
{
    protected $data;

    public function __construct(int $code, string $message, ?array $data = null)
    {
        $this->data = $data;
        parent::__construct($message, $code);
    }

    public function getData(): ?array
    {
        return $this->data;
    }
}
