<?php


namespace IceCloud\RpcServer\Lib\Contracts\Schema\Constraints;


interface WithLaravelMessageConstraint
{
    public function toLaravelMessageAnchor(): ?string;
    public function getMessage(): ?string;
}
