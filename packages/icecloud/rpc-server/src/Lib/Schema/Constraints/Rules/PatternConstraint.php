<?php


namespace IceCloud\RpcServer\Lib\Schema\Constraints\Rules;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\ValuedConstraint;
use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\WithLaravelMessageConstraint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConstraintConfigurationException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\FormatConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Types\StringConstraint;

class PatternConstraint extends Constraint implements ValuedConstraint, WithLaravelMessageConstraint
{
    private $value;

    /**
     * PatternConstraint constructor.
     * @param string $pattern
     * @param string|null $message
     */
    public function __construct(string $pattern, ?string $message = null)
    {
        $this->value = $pattern;
        parent::__construct($message);
    }


    function key(): string
    {
        return 'pattern';
    }

    public function assert(ConstraintsMap $constraints)
    {
        if (!$constraints->exists(StringConstraint::class)) {
            throw new ConstraintConfigurationException("Формат может быть применен только для типа string");
        }

        if ($constraints->exists(FormatConstraint::class)) {
            throw new ConstraintConfigurationException("Регулярное выражение исключает применение формата");
        }

    }

    public function getValue() : string
    {
        return $this->value;
    }

    public function toLaravelValidation() : ?string
    {
        return 'regex:'.$this->value;
    }

    public function toLaravelMessageAnchor(): string
    {
        return 'regex';
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
