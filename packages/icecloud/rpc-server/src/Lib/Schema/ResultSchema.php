<?php


namespace IceCloud\RpcServer\Lib\Schema;


use IceCloud\RpcServer\Lib\Contracts\Schema\ValueDefinition;
use IceCloud\RpcServer\Lib\Procedure;
use IceCloud\RpcServer\Lib\Schema\Definitions\BaseDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ArrayDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ObjectDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\BoolDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\FloatDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\IntDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\StringDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\ScalarDefinition;

/**
 * Класс схемы результата процедуры
 *
 * @package IceCloud\RpcServer\Lib\Schema
 * @author a.kazakov
 */
class ResultSchema
{
    protected ?ValueDefinition $definition = null;

    public function getDefinition(): ?ValueDefinition
    {
        return $this->definition;
    }

    public function float(): FloatDefinition
    {
        return $this->definition = new FloatDefinition();
    }

    public function int(): IntDefinition
    {
        return $this->definition = new IntDefinition();
    }

    public function bool(): BoolDefinition
    {
        return $this->definition = new BoolDefinition();
    }

    public function string(): StringDefinition
    {
        return $this->definition = new StringDefinition();
    }

    public function object(callable $schema): ObjectDefinition
    {
        return $this->definition = new ObjectDefinition($schema);
    }

    public function array(callable $of): ArrayDefinition
    {
        return $this->definition = new ArrayDefinition($of);
    }

    public function exampleData()
    {
        return $this->definition ? $this->getDefinition()->exampleData() : null;
    }
}
