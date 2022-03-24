<?php


namespace IceCloud\RpcServer\Lib\Schema\Definitions\Scalar;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\TypeDefinitionConstraint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConstraintAlreadyAppliedException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\DefaultConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\EnumConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\MaxConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\MinConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\NullableConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RequiredConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Types\FloatConstraint;
use IceCloud\RpcServer\Lib\Schema\Definitions\ScalarDefinition;

/**
 * Определение числа с плавающей точкой
 *
 * @package IceCloud\RpcServer\Lib\Schema\Definitions\Scalar
 * @author a.kazakov
 */
class FloatDefinition extends ScalarDefinition
{

    /**
     * @inheritDoc
     */
    protected function setupTypeConstraint(): TypeDefinitionConstraint
    {
        return new FloatConstraint();
    }

    /**
     * Минимальное значение
     * @param float $value
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function min(float $value, ?string $message = null): FloatDefinition
    {
        $this->constraints->add(
            new MinConstraint($value, $message)
        );
        return $this;
    }

    /**
     * Максимальное значение
     * @param float $value
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function max(float $value, ?string $message = null): FloatDefinition
    {
        $this->constraints->add(
            new MaxConstraint($value, $message)
        );
        return $this;
    }

    /**
     * Соответствие набору
     * @param float[] $values
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function enum(array $values, ?string $message = null): FloatDefinition
    {
        $this->constraints->add(
            new EnumConstraint($values, $message)
        );
        return $this;
    }

    /**
     * Значение по умолчанию
     * @param float|null $value
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function default(?float $value): FloatDefinition
    {
        $this->constraints->add(
            new DefaultConstraint($value)
        );
        return $this;
    }

    /**
     * Обязателен и не пуст
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function required(?string $message = null): FloatDefinition
    {
        $this->constraints->add(
            new RequiredConstraint($message)
        );
        return $this;
    }

    /**
     * Допустимо null значение
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function nullable(): FloatDefinition
    {
        $this->constraints->add(
            new NullableConstraint()
        );
        return $this;
    }

    public function exampleData()
    {
        $min = ($c = $this->getConstraints()->find(MinConstraint::class)) && $c instanceof MinConstraint
            ? $c->getValue()
            : PHP_INT_MIN;

        $max = ($c = $this->getConstraints()->find(MaxConstraint::class)) && $c instanceof MaxConstraint
            ? $c->getValue()
            : PHP_INT_MAX;

        return rand($min, $max) / $max;
    }
}
