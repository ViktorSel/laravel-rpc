<?php


namespace IceCloud\RpcServer\Lib\Mock;


use IceCloud\RpcServer\Lib\Procedure;
use IceCloud\RpcServer\Lib\Request;
use IceCloud\RpcServer\Lib\Schema\ArgumentsSchema;
use IceCloud\RpcServer\Lib\Schema\ResultSchema;

class TestProcedure extends Procedure
{
    private $name;
    private $argumentsSchema;
    private $resultSchema;
    private $handler;

    public function __construct(string $name, ?callable $handler = null, ?callable $argumentsSchema = null, ?callable $resultSchema = null)
    {
        $this->name = $name;
        $this->argumentsSchema=$argumentsSchema;
        $this->resultSchema = $resultSchema;
        $this->handler = $handler;
    }

    public function description(): string
    {
        return "";
    }

    public function hasDetailedValidationErrors(): bool
    {
        return true;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function handle(Request $request) {
        return is_callable($callable = $this->handler)
            ? $callable($request)
            : null;
    }

    public function argumentsSchema(ArgumentsSchema $arguments) {
        is_callable($callable = $this->argumentsSchema) && $callable($arguments);
    }

    public function resultSchema(ResultSchema $result) {
        is_callable($callable = $this->resultSchema) && $callable($result);
    }
}
