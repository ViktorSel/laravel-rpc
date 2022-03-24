<?php


namespace IceCloud\RpcServer\Lib\Schema\Constraints\Rules;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\ValuedConstraint;
use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\WithLaravelMessageConstraint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConstraintConfigurationException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\MaxConstraint;

class MinConstraint extends Constraint implements ValuedConstraint, WithLaravelMessageConstraint
{
    protected $value;

    /**
     * MinConstraint constructor.
     * @param float|integer $value
     * @param null|string $message
     */
    public function __construct($value, ?string $message = null)
    {
        $this->value = $value;
        parent::__construct($message);
    }

    function key(): string
    {
        return 'min';
    }

    public function assert(ConstraintsMap $constraints)
    {
        $maxConstraint = $constraints->find(MaxConstraint::class);
        if ($maxConstraint !== null && $maxConstraint->getValue() < $this->value) {
            throw new ConstraintConfigurationException("Минимальное значение не может быть больше максимального");
        }
    }

    public function getValue()
    {
        return $this->value;
    }

    public function toLaravelValidation() : ?string
    {
        return 'min:'.$this->value;
    }

    public function toLaravelMessageAnchor(): string
    {
        return 'min';
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    function print(): string
    {
        return $this->key() . $this->value;
    }
}
