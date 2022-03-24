<?php


namespace IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints;


use IceCloud\RpcServer\Lib\Exceptions\RpcException;
use IceCloud\RpcServer\Lib\Exceptions\Schema\SchemaException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use Throwable;

class ConstraintAlreadyAppliedException extends SchemaException
{
    public function __construct(Constraint $constraint)
    {
        parent::__construct("Ограничение '{$constraint->key()}' уже применено");
    }
}
