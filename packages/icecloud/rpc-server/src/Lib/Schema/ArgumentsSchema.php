<?php


namespace IceCloud\RpcServer\Lib\Schema;

use IceCloud\RpcServer\Lib\Contracts\Schema\ObjectBlueprint;
use IceCloud\RpcServer\Lib\Contracts\Schema\ValueDefinition;
use IceCloud\RpcServer\Lib\Models\CompiledValidationInstructions;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\DefaultConstraint;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ArrayDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ObjectDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\CompositeDefinition;
use IceCloud\RpcServer\Lib\Schema\Mixins\ObjectBlueprintBuilder;

/**
 * Абстракция схемы аргументов процедуры.
 *
 * @package IceCloud\RpcServer\Lib\Schema
 */

class ArgumentsSchema implements ObjectBlueprint
{
    const COMPLEXITY_LIMIT = 4;

    use ObjectBlueprintBuilder;

    /**
     * Скомпилировать схему в инструкции валидации
     * @return CompiledValidationInstructions
     */
    public function compileValidationInstructions() : CompiledValidationInstructions {
        $rules = [];
        $messages = [];
        $defaults = [];

        foreach ($this->getProperties() as $property) {
            $this->compileDefinition($rules, $messages, $defaults, $property, []);
        }

        return new CompiledValidationInstructions($rules, $messages, $defaults);
    }

    /**
     * Является ли аргументация процедуры сложной (слишком много аргументов)
     * @return bool
     */
    public function complex() : bool
    {
        return count($this->getProperties()) > self::COMPLEXITY_LIMIT;
    }

    /**
     * Скомпилировать определение
     * @param array $rules
     * @param array $messages
     * @param array $defaults
     * @param ValueDefinition $definition
     * @param array $path
     */
    protected function compileDefinition(array &$rules, array &$messages, array &$defaults, ValueDefinition $definition, array $path = [])
    {
        $path[] = $definition->getName();

//        $definition->getFullQualifiedName();

        $definition->getConstraints()->prepare();
        $qualifiedName = $definition->getFullQualifiedName();
        $rules = array_merge($rules, [
            $qualifiedName => $definition->getConstraints()->toLaravelValidation()
        ]);

        if ($default = $definition->getConstraints()->find(DefaultConstraint::class)) {
            /* @var $default DefaultConstraint */
            $defaults = array_merge(
                $defaults,
                [$qualifiedName => $default->getValue()]
            );
        }

        $messages = array_merge(
            $messages,
            $definition->getConstraints()->toLaravelValidationMessages($qualifiedName)
        );

        if (!$definition instanceof CompositeDefinition) {
            return;
        }

        if ($definition instanceof ArrayDefinition) {
            $this->compileDefinition($rules, $messages, $defaults, $definition->getOf(), $path);
        }
        if ($definition instanceof ObjectDefinition) {
            foreach ($definition->getProperties() as $name => $propertyDefinition) {
                $this->compileDefinition($rules, $messages, $defaults, $propertyDefinition, $path);
            }
        }
    }
}
