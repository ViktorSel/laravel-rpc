<?php


namespace IceCloud\RpcServer\Lib\Schema\Definitions\Composite;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\TypeDefinitionConstraint;
use IceCloud\RpcServer\Lib\Contracts\Schema\ObjectBlueprint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConstraintAlreadyAppliedException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\MaxConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\MinConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\NullableConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RequiredConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Types\ObjectConstraint;
use IceCloud\RpcServer\Lib\Schema\Definitions\BaseDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\CompositeDefinition;
use IceCloud\RpcServer\Lib\Schema\Mixins\ObjectBlueprintBuilder;

/**
 * Определение объекта
 * @package IceCloud\RpcServer\Lib\Schema\Definitions\Composite
 * @author a.kazakov
 */
class ObjectDefinition extends CompositeDefinition implements ObjectBlueprint
{
    use ObjectBlueprintBuilder;

    private $structureId = null;

    /**
     * ObjectDefinition constructor.
     * @param callable|null $schema Функция, аргументом которой будет экземпляр {@link ObjectBlueprint} или null если нужен филлер
     * @param string|null $name
     */
    public function __construct(?callable $schema = null, ?string $name = null)
    {
//        $name !== null && $this->setName($name);
        parent::__construct($name);

        if ($schema !== null) {
            $schema($this);
        }

        foreach ($this->getProperties() as $property) {
            $property->setParent($this);
        }

//        $this->required(); // Объект не может быть пустым
    }

    /**
     * @inheritDoc
     */
    protected function setupTypeConstraint(): TypeDefinitionConstraint
    {
        return new ObjectConstraint();
    }

    /**
     * Обязателен и не пуст
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function required(?string $message = null): ObjectDefinition
    {
        $this->constraints->add(
            new RequiredConstraint($message)
        );
        return $this;
    }

    /**
     * Может быть null
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function nullable(): ObjectDefinition
    {
        $this->constraints->add(
            new NullableConstraint()
        );
        return $this;
    }

    /**
     * Указать ID структуры, чтобы генераторы моделей на базе этого определения создавали одну структуру
     * модели для указанного ID
     * @param string $structureId
     * @return ObjectDefinition
     */
    public function id(string $structureId): ObjectDefinition
    {
        $this->structureId = $structureId;
        return $this;
    }

    /**
     * @return null
     * @deprecated
     */
    public function getStructureId()
    {
        return $this->structureId;
    }

    public function hashing() : string
    {
        $bucket = [static::class, $this->getConstraints()->print()];

        foreach ($this->getProperties() as $property) {
            $bucket[] = [$property->getName(), $property->hash()];
        }

        return md5(serialize($bucket));
    }

    public function exampleData()
    {
        $data = [];
        foreach ($this->getProperties() as $property) {
            $data[$property->getName()] = $property->exampleData();
        }
        return $data;
    }
}
