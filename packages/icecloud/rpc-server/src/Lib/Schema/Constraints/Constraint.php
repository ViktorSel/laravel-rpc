<?php


namespace IceCloud\RpcServer\Lib\Schema\Constraints;


use IceCloud\RpcServer\Lib\Contracts\Schema\ValueDefinition;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RuleConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\UniqueConstraint;

abstract class Constraint
{
    const LOW_PRIORITY = -1;
    const MEDIUM_PRIORITY = 0;
    const HIGH_PRIORITY = 1;
    const ULTRA_PRIORITY = 2;

    protected $message;
    protected $priority = -1;
    private ?ConstraintsMap $map = null;

    public function __construct(?string $message = null)
    {
        $this->message = $message;
    }

    /**
     * Установить карту ограничений, к которой принадлежит ограничение
     * @param ConstraintsMap $map
     * @return $this
     */
    public function setMap(ConstraintsMap $map) {
        $this->map = $map;
        return $this;
    }

    /**
     * Получить карту ограничений
     * @return ConstraintsMap|null
     */
    public function getMap(): ?ConstraintsMap
    {
        return $this->map;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Ключ ограничения
     * @return string
     */
    abstract function key(): string;

    /**
     * Отпечаток ограничения. Должен включать в себя уникальный ключ и параметры ограничения.
     * @return string
     */
    abstract function print(): string;


    public function hasAvailableMessage(): bool
    {
        return $this->message !== null;
    }

    /**
     * Проверка на ожидаемое содержимое в наборе ограничений
     * @param ConstraintsMap $constraints
     * @return mixed
     */
    abstract public function assert(ConstraintsMap $constraints);

    abstract function toLaravelValidation();

    public static function boot()
    {
        UniqueConstraint::boot();
    }
}
