<?php


namespace IceCloud\RpcServer\Lib\Generator\Compiler\Js;


use IceCloud\RpcServer\Lib\Contracts\Generator\Js\JsVariable;
use IceCloud\RpcServer\Lib\Contracts\Schema\ObjectBlueprint;
use IceCloud\RpcServer\Lib\Generator\Generator;
use IceCloud\RpcServer\Lib\Generator\JsGenerator;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\Virtual\JsModelProperty;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\Virtual\JsModelRelation;

class JsInputModelFile extends JsClassFile
{
    /**
     * @var JsModelProperty[]
     */
    private array $properties;

    /**
     * @var JsModelRelation[]
     */
    private array $relations = [];

    /**
     * @var string[]
     */
    private array $constants = [];

    /**
     * JsCompiledFormFile constructor.
     * @param JsGenerator $generator
     * @param ObjectBlueprint $blueprint
     * @param string[] $namespace
     * @param string $additionalName
     */
    public function __construct(JsGenerator $generator, ObjectBlueprint $blueprint, array $namespace, string $additionalName)
    {
        foreach ($blueprint->getProperties() as $property) {
            $prop = $this->properties[$property->getName()] = new JsModelProperty($property);
            $this->constants[$property->getName()] = $prop->constant();
        }

        parent::__construct($generator, $namespace, ucfirst($additionalName));
    }

    protected function makeClassName(Generator $generator, string $entityName): string
    {
        return sprintf('Rpc%sInput', ucfirst($entityName));
    }

    /**
     * Свойство по имени
     * @param string $name
     * @return JsModelProperty
     */
    public function getProperty(string $name): JsVariable
    {
        return $this->properties[$name];
    }

    /**
     * @return JsModelRelation[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    public function hasRelation(string $name) : bool
    {
        return array_key_exists($name, $this->relations);
    }

    public function getRelation(string $name): JsModelRelation
    {
        return $this->relations[$name];
    }

    /**
     * Применить отношение
     * @param JsModelRelation $relation
     */
    public function applyRelation(JsModelRelation $relation): void
    {
        $prop = $relation->getProperty();
        $this->relations[$prop->name()] = $relation;
        $this->properties[$prop->name()]->setUse($relation->getModel());
    }

    /**
     * @return array
     */
    public function getConstants(): array
    {
        return $this->constants;
    }

    /**
     * @return JsModelProperty[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
