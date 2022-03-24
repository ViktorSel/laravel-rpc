<?php


namespace IceCloud\RpcServer\Lib;


use IceCloud\RpcServer\Lib\Contracts\RequestHolderContract;
use IceCloud\RpcServer\Lib\Contracts\Schema\SchemaContainerContract;

abstract class Middleware implements SchemaContainerContract, RequestHolderContract
{
    public ?Procedure $procedure = null;
    public ?Request $request = null;

    public function getRequest(): Request
    {
        return $this->request;
    }

    abstract public function handle(Request $request, \Closure $closure);
}
