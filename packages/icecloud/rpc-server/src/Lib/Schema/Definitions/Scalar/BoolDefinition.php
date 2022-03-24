<?php


namespace IceCloud\RpcServer\Lib\Schema\Definitions\Scalar;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\TypeDefinitionConstraint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConstraintAlreadyAppliedException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\DefaultConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RequiredConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Types\BooleanConstraint;
use IceCloud\RpcServer\Lib\Schema\Definitions\ScalarDefinition;

/**
 * Определение флага
 * @package IceCloud\RpcServer\Lib\Schema\Definitions\Scalar
 * @author a.kazakov
 */
class BoolDefinition extends ScalarDefinition
{
    /**
     * @inheritDoc
     */
    protected function setupTypeConstraint(): TypeDefinitionConstraint
    {
        return new BooleanConstraint();
    }

    /**
     * Значение по умолчанию
     * @param bool $value
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function default(bool $value): BoolDefinition
    {
        $this->constraints->add(
            new DefaultConstraint($value)
        );
        return $this;
    }

    /**
     * Обязателен
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function required(?string $message = null): BoolDefinition {
        $this->constraints->add(
            new RequiredConstraint($message)
        );
        return $this;
    }

    public function exampleData()
    {
        return (bool) rand(0, 1);
    }
}
