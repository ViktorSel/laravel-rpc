<?php


namespace IceCloud\RpcServer\Lib\Schema\Definitions\Scalar;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\TypeDefinitionConstraint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConstraintAlreadyAppliedException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\DefaultConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\EnumConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\ExistsConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\MaxConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\MinConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\NullableConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RequiredConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RequiredWithConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\UniqueConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Types\IntegerConstraint;
use IceCloud\RpcServer\Lib\Schema\Definitions\ScalarDefinition;

/**
 * Определение целого числа
 * @package IceCloud\RpcServer\Lib\Schema\Definitions\Scalar
 * @author a.kazakov <a.kazakov@iceberg.ru>
 */
class IntDefinition extends ScalarDefinition
{
    protected function setupTypeConstraint(): TypeDefinitionConstraint
    {
        return new IntegerConstraint();
    }

    /**
     * Минимальное значение
     * @param int $value
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function min(int $value, ?string $message = null): IntDefinition
    {
        $this->constraints->add(
            new MinConstraint($value, $message)
        );
        return $this;
    }

    /**
     * Максимальное значение
     * @param int $value
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function max(int $value, ?string $message = null): IntDefinition
    {
        $this->constraints->add(
            new MaxConstraint($value, $message)
        );
        return $this;
    }

    /**
     * Соответствие набору
     * @param int[] $values
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function enum(array $values, ?string $message = null): IntDefinition
    {
        $this->constraints->add(
            new EnumConstraint($values, $message)
        );
        return $this;
    }

    /**
     * Значение по умолчанию
     * @param int|null $value
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function default(?int $value): IntDefinition
    {
        $this->constraints->add(
            new DefaultConstraint($value)
        );
        return $this;
    }

    /**
     * Обязательно и не пусто
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function required(?string $message = null): IntDefinition
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
    public function nullable(): IntDefinition
    {
        $this->constraints->add(
            new NullableConstraint()
        );
        return $this;
    }


    /**
     * Существует ли указанная модель
     *
     * @param string $modelClass Класс eloquent модели
     * @param string $field Поле
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function exists(string $modelClass, string $field, ?string $message = null): IntDefinition
    {
        $this->constraints->add(
            new ExistsConstraint($modelClass, $field, $message)
        );
        return $this;
    }

    /**
     * Не существует ли указанная модель
     *
     * @param string $table
     * @param string $attribute
     * @param string|null $ignoreByInputRef
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function unique(string $table, string $attribute, ?string $ignoreByInputRef = null, ?string $ignoreByColumn = null, ?string $message = null): IntDefinition
    {
        $this->constraints->add(
            new UniqueConstraint($table, $attribute, $ignoreByInputRef, $ignoreByColumn, $message)
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

        return rand($min, $max);
    }
}
