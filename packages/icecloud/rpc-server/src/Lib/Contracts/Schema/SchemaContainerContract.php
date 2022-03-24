<?php


namespace IceCloud\RpcServer\Lib\Contracts\Schema;


use IceCloud\RpcServer\Lib\Schema\ArgumentsSchema;
use IceCloud\RpcServer\Lib\Schema\ResultSchema;

interface SchemaContainerContract
{
    public function argumentsSchema(ArgumentsSchema $arguments);
    public function resultSchema(ResultSchema $result);
}
