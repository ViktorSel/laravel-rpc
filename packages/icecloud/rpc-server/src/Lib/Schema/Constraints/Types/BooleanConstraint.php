<?php


namespace IceCloud\RpcServer\Lib\Schema\Constraints\Types;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\TypeDefinitionConstraint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\MultiplyTypingException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;

class BooleanConstraint extends Constraint implements TypeDefinitionConstraint
{
    protected $priority = self::HIGH_PRIORITY;

    function key(): string
    {
        return 'boolean';
    }

    public function assert(ConstraintsMap $constraints)
    {
    }

    function toSwagger(): string
    {
        return 'boolean';
    }

    function toLaravelValidation(): string
    {
        return 'boolean';
    }

    function print(): string
    {
        return $this->key();
    }
}
