<?php


namespace IceCloud\RpcServer\Lib\Schema\Constraints\Rules;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\ValuedConstraint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\BadValueConstraintException;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConflictConstraintsException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\NullableConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RequiredConstraint;

class DefaultConstraint extends Constraint implements ValuedConstraint
{
    private $value;

    protected $priority = self::MEDIUM_PRIORITY;

    /**
     * DefaultConstraint constructor.
     * @param mixed $value
     * @param string|null $message
     */
    public function __construct($value, ?string $message = null)
    {
        $this->value = $value;
        parent::__construct($message);
    }

    function key(): string
    {
        return 'default';
    }

    public function assert(ConstraintsMap $constraints)
    {
        $excepted = $constraints->exists(RequiredConstraint::class);
        if ($excepted) {
            throw new ConflictConstraintsException($this, [RequiredConstraint::class]);
        }

        // Проверяем если null значение по умолчанию и нет nullable разрешения - бросаем исключение
        if ($this->getValue() === null && !$constraints->exists(NullableConstraint::class)) {
            throw new BadValueConstraintException($this, "Предварительно должно быть разрешено 'nullable'");
        }
    }

    public function getValue()
    {
        return $this->value;
    }

    public function toLaravelValidation() : ?string{
        return null;
    }

    function print(): string
    {
        return $this->key();
    }
}
