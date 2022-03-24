<?php


namespace IceCloud\RpcServer\Lib\Schema\Constraints;

use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\WithLaravelMessageConstraint;
use IceCloud\RpcServer\Lib\Contracts\Schema\ValueDefinition;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConstraintAlreadyAppliedException;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\TestDefaultValueException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\DefaultConstraint;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Translation\Translator;

/**
 * Карта ограничений
 *
 * @package IceCloud\RpcServer\Lib\Schema\Constraints
 * @author a.kazakov
 */
class ConstraintsMap
{
    /**
     * @var Collection|Constraint[]
     */
    private Collection $items;

    private ValueDefinition $definition;

    public function __construct(ValueDefinition $definition)
    {
        $this->definition = $definition;
        $this->items = new Collection();
    }

    /**
     * Получить определение
     * @return ValueDefinition
     */
    public function getDefinition(): ValueDefinition
    {
        return $this->definition;
    }

    /**
     * Добавить ограничение
     * @param Constraint $constraint
     * @throws ConstraintAlreadyAppliedException
     */
    public function add(Constraint $constraint)
    {
        if ($this->keyExists($constraint->key())) {
            throw new ConstraintAlreadyAppliedException($constraint);
        }

        $this->items[$constraint->key()] = $constraint->setMap($this);
    }

    /**
     * Найти ограничение по классу
     * @param string $constraintClass
     * @return Constraint|null
     */
    public function find(string $constraintClass): ?Constraint
    {
        return $this->items->whereInstanceOf($constraintClass)->first();
    }

    /**
     * Присутствуют ли перечисленные ограничения по ключу
     * @param string ...$keys
     * @return bool
     */
    public function keyExists(string ...$keys): bool
    {
        return count(array_intersect(
            array_keys($keys),
            $keys
        )) > 0;
    }

    /**
     * Присутствуют ли перечисленные ограничения
     * @param string ...$constraintClasses
     * @return bool
     */
    public function exists(string ...$constraintClasses): bool
    {
        foreach ($constraintClasses as $class) {
            if ($this->items->whereInstanceOf($class)->count() > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Найти всех кроме себя по ключу
     * @param string $key
     * @param Constraint $self
     * @return bool
     */
    public function keyExistsWithoutSelf(string $key, Constraint $self): bool
    {
        foreach ($this->items as $instance) {
            if (!$instance instanceof $self && $instance->key() === $self->key()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Найти всех кроме себя
     * @param array $classes
     * @param Constraint $self
     * @return bool
     */
    public function existsWithoutSelf(array $classes, Constraint $self): bool
    {
        foreach ($classes as $class) {

            foreach ($this->items as $instance) {
                if (!($instance instanceof $self) && ($instance instanceof $class)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function prepare() : ConstraintsMap {
        $this->order();

        foreach ($this->items as $constraint) { /* @var Constraint $constraint */
            $constraint->assert($this);
        }

        /** @var $defaultConstraint DefaultConstraint */
        $defaultConstraint = $this->find(DefaultConstraint::class);
        if ($defaultConstraint instanceof DefaultConstraint) {
            $validator = Validator::make(
                ['test' => $defaultConstraint->getValue()],
                ['test' =>$this->toLaravelValidation()]
            );

            if ($validator->fails()) {
                throw new TestDefaultValueException($defaultConstraint);
            }
        }

        return $this;
    }

    /**
     * Сконвертировать в Laravel совместимые правила валидации
     * @return array
     */
    public function toLaravelValidation(): array
    {
        $rules = [];
        foreach ($this->items as $item) {
            /* @var $item Constraint */
            $validator = $item->toLaravelValidation();
            if ($validator !== null) {
                $rules [] = $validator;
            }
        }

        return $rules;
    }

    /**
     * Сконвертировать в структуру laravel совместимых сообщений
     * @param string $rootName
     * @return array
     */
    public function toLaravelValidationMessages(string $rootName): array
    {
        $messages = [];

        foreach ($this->items->whereInstanceOf(WithLaravelMessageConstraint::class) as $constraint) {
            /* @var Constraint|WithLaravelMessageConstraint $constraint */
            if ($constraint->toLaravelMessageAnchor() !== null && $constraint->getMessage() !== null) {
                $messages[$rootName . '.' . $constraint->toLaravelMessageAnchor()] = $constraint->getMessage();
            }
        }

        return $messages;
    }

    /**
     * Отсортировать по приоритетам
     * @return ConstraintsMap
     */
    private function order() : ConstraintsMap
    {
        $this->items = $this->items->sortByDesc(function (Constraint $constraint) {
            return $constraint->getPriority();
        });
        return $this;
    }

    public function print() : string
    {
        $prints = [];

        foreach ($this->items as $item) {
            // Значение по умолчанию игнорируется
            if ($item instanceof DefaultConstraint) {
                continue;
            }
            $prints [] = $item->print();
        }

        return implode(', ', $prints);
    }
}
