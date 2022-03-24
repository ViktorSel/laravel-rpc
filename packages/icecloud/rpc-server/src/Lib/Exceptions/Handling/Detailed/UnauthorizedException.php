<?php


namespace IceCloud\RpcServer\Lib\Exceptions\Handling\Detailed;


use IceCloud\RpcServer\Lib\Exceptions\Handling\HandlingException;
use IceCloud\RpcServer\Lib\Response;

class UnauthorizedException extends HandlingException
{
    public function __construct(?string $message = null, ?array $data = null)
    {
        parent::__construct(Response::NOT_AUTHORIZED_401, $message ?? 'Unauthorized', $data);
    }
}
