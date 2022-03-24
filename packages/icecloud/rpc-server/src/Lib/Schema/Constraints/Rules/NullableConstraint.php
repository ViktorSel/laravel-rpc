<?php


namespace IceCloud\RpcServer\Lib\Schema\Constraints\Rules;


use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConflictConstraintsException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RequiredConstraint;

class NullableConstraint extends Constraint
{
    protected $priority = self::ULTRA_PRIORITY;

    function key(): string
    {
        return 'nullable';
    }

    public function assert(ConstraintsMap $constraints)
    {
        if ($constraints->exists(RequiredConstraint::class)) {
            throw new ConflictConstraintsException($this, [
                RequiredConstraint::class
            ]);
        }
    }

    public function toLaravelValidation() : ?string
    {
        return 'nullable';
    }

    function print(): string
    {
        return $this->key();
    }
}
