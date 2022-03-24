<?php

namespace IceCloud\RpcServer\Lib\Schema\Constraints\Rules;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\WithLaravelMessageConstraint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConflictConstraintsException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;
use Illuminate\Validation\Rules\Unique;

class CustomConstraint extends Constraint
{
    private $validator;

    public function __construct(callable $validator)
    {
        $this->validator = $validator;
        parent::__construct();
    }

    function key(): string
    {
        return 'custom';
    }

    function print(): string
    {
        return (new \ReflectionFunction($this->validator))->__toString();
    }

    public function assert(ConstraintsMap $constraints)
    {
        $excepted = $constraints->existsWithoutSelf([CustomConstraint::class], $this);
        if ($excepted) {
            throw new ConflictConstraintsException($this, [
                CustomConstraint::class
            ]);
        }
    }

    function toLaravelValidation() : callable
    {
        return $this->validator;
    }

    public function toLaravelMessageAnchor(): ?string
    {
        return null;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
