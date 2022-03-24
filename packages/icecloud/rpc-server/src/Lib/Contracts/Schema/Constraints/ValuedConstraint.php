<?php


namespace IceCloud\RpcServer\Lib\Contracts\Schema\Constraints;


interface ValuedConstraint
{
    /**
     * @return mixed
     */
    public function getValue();
}
