<?php


namespace IceCloud\RpcServer\Lib\Exceptions\Middlewares;


use IceCloud\RpcServer\Lib\Exceptions\RpcException;
use Throwable;

class RequiredResultDefinitionException extends RpcException
{
    public function __construct(string $requiredDefinitionClass)
    {
        parent::__construct("Требуется класс {$requiredDefinitionClass} определения результата");
    }
}
