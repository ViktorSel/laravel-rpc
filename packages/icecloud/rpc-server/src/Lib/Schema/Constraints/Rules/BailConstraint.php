<?php


namespace IceCloud\RpcServer\Lib\Schema\Constraints\Rules;


use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConflictConstraintsException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RequiredConstraint;

class BailConstraint extends Constraint
{
    protected $priority = self::ULTRA_PRIORITY;

    function key(): string
    {
        return 'bail';
    }

    public function assert(ConstraintsMap $constraints)
    {
        if ($constraints->existsWithoutSelf([BailConstraint::class] , $this)) {
            throw new ConflictConstraintsException($this, [
                BailConstraint::class
            ]);
        }
    }

    public function toLaravelValidation() : ?string
    {
        return 'bail';
    }

    function print(): string
    {
        return $this->key();
    }
}
