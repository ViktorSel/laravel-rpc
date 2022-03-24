<?php


namespace IceCloud\RpcServer\Lib\Exceptions\Server;


use IceCloud\RpcServer\Lib\Exceptions\RpcException;
use Throwable;

class ProcedureNameConflictException extends RpcException
{
    public function __construct(string $name)
    {
        parent::__construct("Имя '{$name}' уже используется.");
    }
}
