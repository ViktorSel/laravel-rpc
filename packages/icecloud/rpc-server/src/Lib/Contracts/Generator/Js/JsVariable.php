<?php


namespace IceCloud\RpcServer\Lib\Contracts\Generator\Js;


interface JsVariable
{
    public function type() : string;
    public function name() : string;
    public function camel() : string;
    public function comment() : string;
    public function constant() : string;
}
