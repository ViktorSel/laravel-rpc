<?php


namespace IceCloud\RpcServer\Lib\Schema\Constraints\Types;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\TypeDefinitionConstraint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\MultiplyTypingException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;

class IntegerConstraint extends Constraint implements TypeDefinitionConstraint
{
    protected $priority = self::HIGH_PRIORITY;

    function key(): string
    {
        return 'integer';
    }

    public function assert(ConstraintsMap $constraints)
    {
    }

    function toSwagger(): string
    {
        return 'integer';
    }

    function toLaravelValidation(): string
    {
        return 'integer';
    }

    function print(): string
    {
        return $this->key();
    }
}
