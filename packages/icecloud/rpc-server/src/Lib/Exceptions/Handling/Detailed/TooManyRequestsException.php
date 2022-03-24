<?php


namespace IceCloud\RpcServer\Lib\Exceptions\Handling\Detailed;


use IceCloud\RpcServer\Lib\Exceptions\Handling\HandlingException;
use IceCloud\RpcServer\Lib\Response;

class TooManyRequestsException extends HandlingException
{
    public function __construct(string $message, ?array $data = null)
    {
        parent::__construct(Response::TOO_MANY_REQUESTS_429, $message, $data);
    }
}
