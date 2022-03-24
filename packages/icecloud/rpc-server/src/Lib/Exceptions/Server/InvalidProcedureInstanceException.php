<?php


namespace IceCloud\RpcServer\Lib\Exceptions\Server;


use IceCloud\RpcServer\Lib\Exceptions\RpcException;
use Throwable;

class InvalidProcedureInstanceException extends RpcException
{
    public function __construct($instance)
    {
        parent::__construct("Некорректный экземпляр процедуры. Ожидался Procedure, получил " . get_class($instance));
    }
}
