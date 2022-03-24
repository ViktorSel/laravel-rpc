<?php


namespace IceCloud\RpcServer\Lib\Schema\Constraints\Rules;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\ValuedConstraint;
use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\WithLaravelMessageConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;
use Illuminate\Validation\Rules\In;

class EnumConstraint extends Constraint implements ValuedConstraint, WithLaravelMessageConstraint
{
    private $value;

    public function __construct(array $value, ?string $message = null)
    {
        $this->value = $value;
        parent::__construct($message);
    }


    function key(): string
    {
        return 'enum';
    }

    public function assert(ConstraintsMap $constraints)
    {
    }

    public function getValue()
    {
        return $this->value;
    }

    function toLaravelValidation() : ?string
    {
        return new In($this->value);
    }

    public function toLaravelMessageAnchor(): string
    {
        return 'in';
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function print(): string
    {
        return $this->key() . (is_string($this->value) ? $this->value : implode($this->value));
    }
}
