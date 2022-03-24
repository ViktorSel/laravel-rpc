<?php


namespace IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints;


use IceCloud\RpcServer\Lib\Exceptions\Schema\SchemaException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\DefaultConstraint;

/**
 * Class TestDefaultValueException
 * @package IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints
 * @author a.kazakov <a.kazakov@iceberg.ru>\
 */
class TestDefaultValueException extends SchemaException
{
    public function __construct(DefaultConstraint $constraint)
    {
        parent::__construct("Значение по умолчанию '{$constraint->getValue()}' не прошло проверку валидации.");
    }

}
