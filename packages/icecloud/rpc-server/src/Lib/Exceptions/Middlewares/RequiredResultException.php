<?php


namespace IceCloud\RpcServer\Lib\Exceptions\Middlewares;


use IceCloud\RpcServer\Lib\Exceptions\RpcException;
use Throwable;

class RequiredResultException extends RpcException
{
    public function __construct(string $class)
    {
        parent::__construct("Требуется экземпляр {$class} в качестве результата");
    }
}
