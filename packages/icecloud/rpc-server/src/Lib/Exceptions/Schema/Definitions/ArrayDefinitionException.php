<?php


namespace IceCloud\RpcServer\Lib\Exceptions\Schema\Definitions;


use IceCloud\RpcServer\Lib\Contracts\Schema\ValueDefinition;
use IceCloud\RpcServer\Lib\Exceptions\RpcException;
use IceCloud\RpcServer\Lib\Exceptions\Schema\SchemaException;
use Throwable;

class ArrayDefinitionException extends SchemaException
{
    public function __construct()
    {
        parent::__construct("Некорректное определения типа элемента массива. Определение должно быть экземпляром " . ValueDefinition::class);
    }
}
