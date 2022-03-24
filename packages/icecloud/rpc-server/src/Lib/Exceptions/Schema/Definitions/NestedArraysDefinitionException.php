<?php


namespace IceCloud\RpcServer\Lib\Exceptions\Schema\Definitions;

use IceCloud\RpcServer\Lib\Exceptions\Schema\SchemaException;

class NestedArraysDefinitionException extends SchemaException
{
    public function __construct()
    {
        parent::__construct("Вложенные массивы запрещены");
    }

}
