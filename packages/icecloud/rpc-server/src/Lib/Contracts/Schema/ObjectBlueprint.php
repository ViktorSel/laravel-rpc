<?php


namespace IceCloud\RpcServer\Lib\Contracts\Schema;


use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ArrayDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ObjectDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\BoolDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\FloatDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\IntDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\StringDefinition;

interface ObjectBlueprint
{
    public function int(string $name) : IntDefinition;
    public function float(string $name) : FloatDefinition;
    public function bool(string $name) : BoolDefinition;
    public function string(string $name) : StringDefinition;
    public function object(string $name, callable $schema) : ObjectDefinition;
    public function array(string $name, callable $of) : ArrayDefinition;
//    public function arrayOfInt(string $name) : DataDefinitionContract;
//    public function arrayOfFloat(string $name) : DataDefinitionContract;
//    public function arrayOfBool(string $name) : DataDefinitionContract;
//    public function arrayOfString(string $name) : DataDefinitionContract;
//    public function arrayOfArray(string $name) : DataDefinitionContract;
//    public function arrayOfObject(string $name) : DataDefinitionContract;

    /**
     * @return ValueDefinition[]
     */
    public function getProperties() : array;
}
