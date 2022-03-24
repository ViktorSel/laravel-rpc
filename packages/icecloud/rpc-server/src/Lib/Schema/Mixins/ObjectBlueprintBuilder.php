<?php


namespace IceCloud\RpcServer\Lib\Schema\Mixins;


use IceCloud\RpcServer\Lib\Contracts\Schema\ObjectBlueprint;
use IceCloud\RpcServer\Lib\Contracts\Schema\ValueDefinition;
use IceCloud\RpcServer\Lib\Exceptions\RpcException;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Operations\ConflictedMergingException;
use IceCloud\RpcServer\Lib\Schema\Definitions\BaseDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ArrayDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ObjectDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\CompositeDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\BoolDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\FloatDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\IntDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\StringDefinition;

trait ObjectBlueprintBuilder
{
    protected array $properties = [];

    /**
     * Целочисленное свойство
     * @param string $name
     * @return IntDefinition
     */
    public function int(string $name): IntDefinition
    {
        return $this->properties[$name] = (new IntDefinition($name));
    }

    /**
     * Свойство - число с плавающей точкой
     * @param string $name
     * @return FloatDefinition
     */
    public function float(string $name): FloatDefinition
    {
        return $this->properties[$name] = (new FloatDefinition($name));
    }

    /**
     * Булево свойство
     * @param string $name
     * @return BoolDefinition
     */
    public function bool(string $name): BoolDefinition
    {
        return $this->properties[$name] = (new BoolDefinition($name));
    }

    /**
     * Свойство строка
     * @param string $name
     * @return StringDefinition
     */
    public function string(string $name): StringDefinition
    {
        return $this->properties[$name] = (new StringDefinition($name));
    }

    /**
     * Свойство - объект
     * @param string $name
     * @param callable $schema Колбек, аргументом которого будет {@link ObjectBlueprint}
     * @return ObjectDefinition
     */
    public function object(string $name, callable $schema): ObjectDefinition
    {
        return $this->properties[$name] = (new ObjectDefinition($schema, $name));
    }

    /**
     * Свойство - массив
     * @param string $name
     * @param callable $of Колбек который должен вернуть экземпляр {@link ValueDefinition}
     * @return ArrayDefinition
     * @throws RpcException
     */
    public function array(string $name, callable $of): ArrayDefinition
    {
        return $this->properties[$name] = (new ArrayDefinition($of, $name));
    }

    /**
     * Получить свйоства
     * @return ValueDefinition[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Слияние с другим конструктором
     * @param ObjectBlueprint $builder
     * @deprecated
     * @removed
     * @return $this
     * @throws ConflictedMergingException
     */
    public function merge(ObjectBlueprint $builder) {
        $properties = $builder->getProperties();

        foreach ($properties as $name=>$property) {
            $exists = isset($this->properties[$name]);

            if ($exists) {
                throw new ConflictedMergingException($name);
            }

            $this->properties[$name] = $property;
        }

        return $this;
    }
}
