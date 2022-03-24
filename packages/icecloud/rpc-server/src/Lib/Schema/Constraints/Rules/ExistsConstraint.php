<?php

namespace IceCloud\RpcServer\Lib\Schema\Constraints\Rules;

use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\WithLaravelMessageConstraint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConflictConstraintsException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;
use Illuminate\Validation\Rules\Exists;

class ExistsConstraint extends Constraint implements WithLaravelMessageConstraint
{
    private string $modelClass;
    private string $field;
    private Exists $rule;

    public function __construct(string $modelClass, string $field, ?string $message = null)
    {
        $this->modelClass = $modelClass;
        $this->field = $field;
        $this->rule = new Exists($this->modelClass, $field);
        parent::__construct($message);
    }

    function key(): string
    {
        return 'exists';
    }

    function print(): string
    {
        return $this->rule->__toString();
    }

    public function assert(\IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap $constraints)
    {
        $excepted = $constraints->exists(UniqueConstraint::class);
        if ($excepted) {
            throw new ConflictConstraintsException($this, [
                UniqueConstraint::class
            ]);
        }
    }

    function toLaravelValidation(): ?string
    {
        return $this->rule->__toString();
    }

    public function toLaravelMessageAnchor(): string
    {
        return $this->key();
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
