<?php


namespace IceCloud\RpcServer\Lib\Schema\Constraints\Types;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\TypeDefinitionConstraint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\MultiplyTypingException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;

class StringConstraint extends Constraint implements TypeDefinitionConstraint
{
    protected $priority = self::HIGH_PRIORITY;

    function key(): string
    {
        return 'string';
    }

    public function assert(ConstraintsMap $constraints)
    {
    }

    function toLaravelValidation() : string
    {
        return 'string';
    }

    function print(): string
    {
        return $this->key();
    }
}
