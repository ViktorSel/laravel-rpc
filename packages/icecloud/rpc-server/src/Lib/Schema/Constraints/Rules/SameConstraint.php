<?php


namespace IceCloud\RpcServer\Lib\Schema\Constraints\Rules;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\ValuedConstraint;
use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\WithLaravelMessageConstraint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConstraintConfigurationException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\MinConstraint;

class SameConstraint extends Constraint implements ValuedConstraint, WithLaravelMessageConstraint
{
    private $value;

    /**
     * MaxConstraint constructor.
     * @param float|integer $value
     * @param string|null $message
     */
    public function __construct($value, ?string $message = null)
    {
        $this->value = $value;
        parent::__construct($message);
    }

    function key(): string
    {
        return 'same';
    }

    public function assert(ConstraintsMap $constraints)
    {
//        $minConstraint = $constraints->find(MinConstraint::class);
//        if ($minConstraint !== null && $minConstraint->getValue() > $this->value) {
//            throw new ConstraintConfigurationException("Минимальное значение не может быть больше максимального");
//        }
    }

    public function getValue()
    {
        return $this->value;
    }

    public function toLaravelValidation() : ?string
    {
        return 'same:'.$this->value;
    }

    public function toLaravelMessageAnchor(): string
    {
        return 'same';
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
