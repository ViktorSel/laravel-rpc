<?php


namespace IceCloud\RpcServer\Lib\Generator\Walkers;


use IceCloud\RpcServer\Lib\Contracts\Schema\ObjectBlueprint;
use IceCloud\RpcServer\Lib\Contracts\Schema\ValueDefinition;
use IceCloud\RpcServer\Lib\Procedure;
use IceCloud\RpcServer\Lib\Schema\ArgumentsSchema;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ArrayDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ObjectDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\CompositeDefinition;
use IceCloud\RpcServer\Lib\Schema\Workers\SchemaAccumulator;

class ArgumentsWalker
{
    private ArgumentsSchema $schema;
    /**
     * @var null|callable
     */
    private $filter = null;


    public function __construct(Procedure $procedure)
    {
        $this->schema = $procedure->getPipelineArgumentsSchema();
    }

    /**
     * @return ArgumentsSchema
     */
    public function getSchema(): ArgumentsSchema
    {
        return $this->schema;
    }

    /**
     * @param callable $filter
     * @return ArgumentsWalker
     */
    public function setFilter(callable $filter): self
    {
        $this->filter = $filter;
        return $this;
    }

    protected function processProperty(ValueDefinition $property, callable $iterator, array $pool)
    {
        $filter = $this->filter;

        $iterate = !is_callable($filter) ||
            (is_callable($filter) && $filter($property) === true);

        if ($iterate) {
            $item = $iterator($property, $pool);
            $item && array_push($pool, $item);
        }

        if ($property instanceof ArrayDefinition) {
            $this->processProperty($property->getOf(), $iterator, $pool);
            return;
        }

        if ($property instanceof ObjectBlueprint) {
            $this->walkProperties($property, $iterator, $pool);
        }
    }

    protected function walkProperties(ObjectBlueprint $blueprint, callable $iterator, array $parents)
    {
        foreach ($blueprint->getProperties() as $property) {
            $this->processProperty($property, $iterator, $parents);
        }
    }

    public function run(callable $iterator, array $pool = [])
    {
        $this->walkProperties($this->schema, $iterator, $pool);
    }
}
