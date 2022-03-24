<?php

namespace IceCloud\RpcServer\Lib\Schema\Constraints\Rules;


use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConflictConstraintsException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;
use IceCloud\RpcServer\Lib\Schema\Rule;

class RuleConstraint extends Constraint
{
    private Rule $rule;

    static array $extenders = [];

    public function __construct(Rule $rule)
    {
        $this->rule = $rule;

        parent::__construct();
    }

    function key(): string
    {
        return get_class($this->rule);
    }

    function print(): string
    {
        return (new \ReflectionClass($this->rule))->__toString();
    }

    public function assert(ConstraintsMap $constraints)
    {
        $excepted = $constraints->keyExistsWithoutSelf($this->key(), $this);
        if ($excepted) {
            throw new ConflictConstraintsException($this, [
                $this->key()
            ]);
        }
    }

    function toLaravelValidation() : Rule
    {
        return $this->rule;
    }

}
