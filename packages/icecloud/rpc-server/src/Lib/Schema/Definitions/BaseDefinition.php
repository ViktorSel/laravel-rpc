<?php


namespace IceCloud\RpcServer\Lib\Schema\Definitions;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\TypeDefinitionConstraint;
use IceCloud\RpcServer\Lib\Contracts\Schema\ObjectBlueprint;
use IceCloud\RpcServer\Lib\Contracts\Schema\ValueDefinition;
use IceCloud\RpcServer\Lib\Exceptions\RpcException;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConstraintAlreadyAppliedException;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\TestDefaultValueException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\BailConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\CustomConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\DefaultConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\NullableConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RequiredConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RequiredWithConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RuleConstraint;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ArrayDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ObjectDefinition;
use IceCloud\RpcServer\Lib\Schema\Rule;
use Illuminate\Support\Facades\Validator;

/**
 * Абстракция определения
 *
 * @package IceCloud\RpcServer\Lib\Schema\Definitions
 */
abstract class BaseDefinition implements ValueDefinition
{
    protected ?string $name = null;
    protected ConstraintsMap $constraints;
    protected Constraint $typeConstraint;

    protected ?string $comment = null;

    protected ?CompositeDefinition $parent = null;

    /**
     * Установка ограничения по типу значения
     * @return TypeDefinitionConstraint
     */
    abstract protected function setupTypeConstraint(): TypeDefinitionConstraint;

    public function __construct(?string $name = null)
    {
        $this->name = $name;
        $this->constraints = new ConstraintsMap($this);
        $this->constraints->add(
            $this->typeConstraint = $this->setupTypeConstraint()
        );
    }

    public function getConstraints(): ConstraintsMap
    {
        return $this->constraints;
    }

    /**
     * Установить родительское определение
     * @param CompositeDefinition $definition
     * @return $this
     */
    public function setParent(CompositeDefinition $definition)
    {
        $this->parent = $definition;
        return $this;
    }

    /**
     * Получить родительское определение
     * @return CompositeDefinition|null
     */
    public function getParent(): ?CompositeDefinition
    {
        return $this->parent;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Получить имя определения
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Установить комментарий поля (описание)
     * @param string $comment
     * @return $this
     */
    public function comment(string $comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Получить комментарий поля (описание)
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Получить всех родителей
     * @return CompositeDefinition[]
     */
    public function getParents(): array
    {
        $that = $this;
        $parents = [];
        while (($parent = $that->getParent()) && $parent instanceof CompositeDefinition) {
            $parents[] = $parent;
            $that = $parent;
        }
        return array_reverse($parents);
    }

    /**
     * Получить полное квалификационное имя поля
     * @return string
     */
    public function getFullQualifiedName(): string
    {
        $map = $this->getParents();
        $map[] = $this;
        return implode('.', array_map(function (ValueDefinition $value) {
            return $value->getName();
        }, $map));
    }

    public function getPascalName(): string
    {
        return implode(
            '',
            array_map(function ($value) {
                return ucfirst($value === '*' ? '' : $value);
            }, explode('.', $this->getFullQualifiedName()))
        );
    }

    public function getNamespace(): array
    {
        $map = $this->getParents();
        $map[] = $this;
        array_shift($map);
        return array_map(function (ValueDefinition $value) {
            return $value->getName();
        }, $map);
    }

    public function isArray(): bool
    {
        return $this instanceof ArrayDefinition;
    }

    public function isArrayOfObjects(): bool
    {
        return $this instanceof ArrayDefinition && $this->getOf() instanceof ObjectDefinition;
    }

    public function isObject(): bool
    {
        return $this instanceof ObjectDefinition;
    }

    public function isComposite(): bool
    {
        return $this instanceof CompositeDefinition;
    }

    public function isScalar(): bool
    {
        return $this instanceof ScalarDefinition;
    }

    private ?string $hash = null;

    /** FAST CONSTRAINTS */
    public function isRequired(): bool
    {
        return $this->constraints->exists(RequiredConstraint::class);
    }

    public function isNullable(): bool
    {
        return $this->constraints->exists(NullableConstraint::class);
    }

    /**
     * Хэш структуры определения
     *
     * @return string
     */

    public function hash(): string
    {
        return $this->hash === null ? $this->hash = $this->hashing() : $this->hash;
    }

    abstract protected function hashing(): string;

    /**
     * @return mixed
     */
    abstract public function exampleData();


    /**
     * Применить кастомный валидатор
     *
     * Ограничения:
     *
     * Можно использовать единожды для одного Definition
     * Нельзя использовать $this и конструкцию use
     *
     * @param callable $validator
     * @return $this
     * @throws ConstraintAlreadyAppliedException return $this
     */
    public function custom(callable $validator) {
        $this->constraints->add(
            new CustomConstraint($validator)
        );
        return $this;
    }

    /**
     * Прерывать при первой ошибке
     *
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function bail() {
        $this->constraints->add(
            new BailConstraint()
        );
        return $this;
    }

    /**
     * Пользовательское правило проверки Definition
     * Может быть вызвано несколько раз - не допускаются дублирования правил
     * @param Rule|class-string<Rule> $rule
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function rule($rule)
    {
        $rule = is_string($rule) ? new $rule : $rule;

        if (!$rule instanceof Rule) {
            throw new RpcException("Rule must be instance of " . Rule::class);
        }

        $this->constraints->add(
            new RuleConstraint($rule)
        );

        return $this;
    }

    /**
     * Обязательно если указанное поле не пусто
     *
     * @param string $field
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function requiredWith(string $field, ?string $message = null)
    {
        $this->constraints->add(
            new RequiredWithConstraint($field, $message)
        );
        return $this;
    }

}
