<?php


namespace IceCloud\RpcServer\Lib\Schema\Definitions;

use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConstraintAlreadyAppliedException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\CustomConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RequiredWithConstraint;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\IntDefinition;

/**
 * Абстракция скалярного определения
 *
 * @package IceCloud\RpcServer\Lib\Schema\Definitions
 * @author a.kazakov
 */
abstract class ScalarDefinition extends BaseDefinition
{
    protected function hashing(): string
    {
        return md5(serialize(
            [static::class, $this->getConstraints()->print()]
        ));
    }

}
