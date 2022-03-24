<?php


namespace IceCloud\RpcServer\Lib\Generator\Compiler\Js\Virtual;


use IceCloud\RpcServer\Lib\Contracts\Generator\Js\JsVariable;
use IceCloud\RpcServer\Lib\Contracts\Schema\ValueDefinition;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsInputModelFile;
use IceCloud\RpcServer\Lib\Schema\Constraints\Types\FloatConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Types\IntegerConstraint;
use IceCloud\RpcServer\Lib\Schema\Definitions\BaseDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ArrayDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ObjectDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\CompositeDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\BoolDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\StringDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\ScalarDefinition;
use Illuminate\Support\Str;

class JsModelProperty extends JsVariableWithDefinition implements JsVariable
{
    public function getSetterName(): string
    {
        return 'set' . Str::studly($this->name());
    }

    public function getGetterName(): string
    {
        return 'get' . Str::studly($this->name());
    }

    public function constant(): string
    {
        return $this->constant;
    }
}
