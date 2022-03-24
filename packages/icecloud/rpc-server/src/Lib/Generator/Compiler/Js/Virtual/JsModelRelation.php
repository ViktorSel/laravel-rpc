<?php


namespace IceCloud\RpcServer\Lib\Generator\Compiler\Js\Virtual;


use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsInputModelFile;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\Virtual\JsModelProperty;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ObjectDefinition;

class JsModelRelation
{
    private $arrayable;
    private $model;
    private $property;

    public function __construct(JsModelProperty $property, JsInputModelFile $model, bool $arrayable)
    {
        $this->property = $property;
        $this->model = $model;
        $this->arrayable = $arrayable;
    }

    /**
     * @return JsModelProperty
     */
    public function getProperty(): JsModelProperty
    {
        return $this->property;
    }

    /**
     * @return JsInputModelFile
     */
    public function getModel(): JsInputModelFile
    {
        return $this->model;
    }

    /**
     * @return bool
     */
    public function isArrayable(): bool
    {
        return $this->arrayable;
    }
}
