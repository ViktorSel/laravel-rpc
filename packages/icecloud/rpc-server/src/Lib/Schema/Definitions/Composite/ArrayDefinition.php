<?php


namespace IceCloud\RpcServer\Lib\Schema\Definitions\Composite;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\TypeDefinitionConstraint;
use IceCloud\RpcServer\Lib\Contracts\Schema\ValueDefinition;
use IceCloud\RpcServer\Lib\Exceptions\RpcException;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConstraintAlreadyAppliedException;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Definitions\ArrayDefinitionException;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Definitions\NestedArraysDefinitionException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\MaxConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\MinConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\NullableConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RequiredConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Types\ArrayConstraint;
use IceCloud\RpcServer\Lib\Schema\Definitions\CompositeDefinition;

/**
 * Определение массива
 *
 * @package IceCloud\RpcServer\Lib\Schema\Definitions\Composite
 * @author a.kazakov
 */
class ArrayDefinition extends CompositeDefinition
{
    /**
     * Определение элементов массива
     * @var ValueDefinition
     */
    protected $of;

    /**
     * ArrayDefinition constructor.
     * @param callable $of Функция, которая должна вернуть экземпляр {@link ValueDefinition}
     * @param string|null $name Имя поля
     * @throws ArrayDefinitionException|NestedArraysDefinitionException
     */
    public function __construct(callable $of, ?string $name = null)
    {
        parent::__construct($name);

//        $this->typeConstraint = new ArrayConstraint();

        $definition = $of();

        if (!$definition instanceof ValueDefinition) {
            throw new ArrayDefinitionException();
        }

        if ($definition instanceof ArrayDefinition) {
            throw new NestedArraysDefinitionException();
        }

        $definition->setName('*');
        $definition->setParent($this);

        $this->of = $definition;
    }

    /**
     * Получить определение элементов массива
     * @return ValueDefinition
     */
    public function getOf(): ValueDefinition
    {
        return $this->of;
    }

    /**
     * @inheritDoc
     */
    protected function setupTypeConstraint(): TypeDefinitionConstraint
    {
        return new ArrayConstraint();
    }

    /**
     * Минимальный размер массива
     * @param int $value
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function min(int $value, ?string $message = null): ArrayDefinition
    {
        $this->getConstraints()->add(
            new MinConstraint($value, $message)
        );
        return $this;
    }

    /**
     * Максимальный размер массива
     * @param int $value
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function max(int $value, ?string $message = null): ArrayDefinition
    {
        $this->getConstraints()->add(
            new MaxConstraint($value, $message)
        );
        return $this;
    }

    /**
     * Обязателен и не пуст
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function required(?string $message = null): ArrayDefinition
    {
        $this->constraints->add(
            new RequiredConstraint($message)
        );
        return $this;
    }

    /**
     * Может быть null
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function nullable(): ArrayDefinition
    {
        $this->constraints->add(
            new NullableConstraint()
        );
        return $this;
    }

    protected function hashing(): string
    {
        return md5(serialize(
            [static::class, $this->getConstraints()->print(), $this->getOf()->hash()]
        ));
    }

    public function exampleData()
    {
        return [$this->getOf()->exampleData()];
    }
}
