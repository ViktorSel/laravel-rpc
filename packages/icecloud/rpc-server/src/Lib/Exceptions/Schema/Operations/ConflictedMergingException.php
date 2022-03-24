<?php


namespace IceCloud\RpcServer\Lib\Exceptions\Schema\Operations;


use IceCloud\RpcServer\Lib\Contracts\Schema\ObjectBlueprint;
use IceCloud\RpcServer\Lib\Contracts\Schema\SchemaContainerContract;
use IceCloud\RpcServer\Lib\Exceptions\Schema\SchemaException;
use Throwable;

class ConflictedMergingException extends SchemaException
{
    public function __construct(string $name)
    {
        parent::__construct("Конфликт слияния. Свойство '{$name}' уже определено в объекте назначения.");
    }
}
