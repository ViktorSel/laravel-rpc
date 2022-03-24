<?php


namespace IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints;


use IceCloud\RpcServer\Lib\Exceptions\RpcException;
use IceCloud\RpcServer\Lib\Exceptions\Schema\SchemaException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use Throwable;

class ConflictConstraintsException extends SchemaException
{
    public function __construct(Constraint $constraint, array $conflicts)
    {
        $names = [];
        foreach ($conflicts as $conflicted) {
            $names []= $conflicted;
        }

        parent::__construct(
            "Неправильная логика. Ограничение '{$constraint->key()}' конфликтует с другими (" .
            implode(', ', $names) .
            "), ранее определенными ограничениями"
        );
    }
}
