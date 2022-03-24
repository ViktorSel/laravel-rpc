<?php


namespace IceCloud\RpcServer\Lib\Schema\Workers;

use IceCloud\RpcServer\Lib\Contracts\Schema\SchemaContainerContract;
use IceCloud\RpcServer\Lib\Schema\ArgumentsSchema;
use IceCloud\RpcServer\Lib\Schema\ResultSchema;

/**
 * Абстракция аккумулирования нескольких схем в одну по правилу стека
 *
 * @package IceCloud\RpcServer\Lib\Schema
 * @author a.kazakov
 */
class SchemaAccumulator
{
    /**
     * @var SchemaContainerContract[]
     */
    protected array $containers = [];

    /**
     * Запушить в стек контейнер схемы
     * @param SchemaContainerContract $container
     * @return $this
     */
    public function push(SchemaContainerContract $container) : SchemaAccumulator
    {
        $this->containers[] = $container;
        return $this;
    }

    /**
     * Запушить несколько контейнеров схем
     * @param array $containers
     * @return $this
     */
    public function pushAll(array $containers) : SchemaAccumulator
    {
        foreach ($containers as $container) {
            $this->push($container);
        }
        return $this;
    }

    /**
     * Построить схему аргументов процедуры
     * @return ArgumentsSchema
     */
    public function buildProcedureArgumentsSchema() : ArgumentsSchema
    {
        $arguments = new ArgumentsSchema();

        $this->forwardEachContainers(function (SchemaContainerContract $container) use($arguments) {
            $container->argumentsSchema($arguments);
        });

        return $arguments;
    }

    /**
     * Построить схему результата процедуры
     * @return ResultSchema
     */
    public function buildProcedureResultSchema() : ResultSchema {
        $result = new ResultSchema();

        $this->backwardEachContainers(function (SchemaContainerContract $container) use($result) {
            $container->resultSchema($result);
        });

        return $result;
    }

    /**
     * Пройтись по стеку от корня
     * @param callable $closure
     */
    protected function forwardEachContainers(callable $closure) {
        for ($index = 0; $index < count($this->containers); $index++) {
            $closure($this->containers[$index]);
        }
    }

    /**
     * Пройтись по стеку к корню
     * @param callable $closure
     */
    protected function backwardEachContainers(callable $closure) {
        for ($index = count($this->containers) - 1; $index >= 0; $index--) {
            $closure($this->containers[$index]);
        }
    }

}
