<?php


namespace IceCloud\RpcServer\Lib\Schema\Definitions\Scalar;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\TypeDefinitionConstraint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConstraintAlreadyAppliedException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\DefaultConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\EnumConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\ExistsConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\FormatConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\MaxConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\MinConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\NullableConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\PatternConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RequiredConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RequiredWithConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\SameConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\UniqueConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Types\StringConstraint;
use IceCloud\RpcServer\Lib\Schema\Definitions\ScalarDefinition;

/**
 * Определение строки
 * @package IceCloud\RpcServer\Lib\Schema\Definitions\Scalar
 * @author a.kazakov <a.kazakov@iceberg.ru>
 */
class StringDefinition extends ScalarDefinition
{
    /**
     * @inheritDoc
     */
    protected function setupTypeConstraint(): TypeDefinitionConstraint
    {
        return new StringConstraint();
    }

    /**
     * Минимальная длинна
     * @param int $value
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function min(int $value, ?string $message = null): StringDefinition
    {
        $this->constraints->add(
            new MinConstraint($value, $message)
        );
        return $this;
    }

    public function same(string $target, ?string $message = null): StringDefinition
    {
        $this->constraints->add(
            new SameConstraint($target, $message)
        );
        return $this;
    }

    /**
     * Номинальная длинна строки
     * @param int $value
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function length(int $value, ?string $message = null): StringDefinition
    {
        $this->constraints->add(new MinConstraint($value, $message));
        $this->constraints->add(new MaxConstraint($value, $message));

        return $this;
    }

    /**
     * Максимальная длинна
     * @param int $value
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function max(int $value, ?string $message = null): StringDefinition
    {
        $this->constraints->add(
            new MaxConstraint($value, $message)
        );
        return $this;
    }

    /**
     * Соответствует набору
     * @param string[] $values
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function enum(array $values, ?string $message = null): StringDefinition
    {
        $this->constraints->add(
            new EnumConstraint($values, $message)
        );
        return $this;
    }

    /**
     * Значение по умолчанию
     * @param string|null $value
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function default(?string $value): StringDefinition
    {
        $this->constraints->add(
            new DefaultConstraint($value)
        );
        return $this;
    }

    /**
     * Обязательно и не пусто
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function required(?string $message = null): StringDefinition
    {
        $this->constraints->add(
            new RequiredConstraint($message)
        );
        return $this;
    }

    /**
     * Возможно null значение
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function nullable(): StringDefinition
    {
        $this->constraints->add(
            new NullableConstraint()
        );
        return $this;
    }

    /**
     * Соответствует формату. Доступные форматы смотреть в {@link FormatConstraint::$formats}
     * @param string $format
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function format(string $format, ?string $message = null): StringDefinition
    {
        $this->constraints->add(
            new FormatConstraint($format, $message)
        );
        return $this;
    }


    /**
     * Существует ли указанная модель
     *
     * @param string $modelClass Класс eloquent модели
     * @param string $field Поле
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function exists(string $modelClass, string $field, ?string $message = null): StringDefinition
    {
        $this->constraints->add(
            new ExistsConstraint($modelClass, $field, $message)
        );
        return $this;
    }

    /**
     * Не существует ли указанная модель
     *
     * @param string $table
     * @param string $attribute
     * @param string|null $ignoreByInputRef
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function unique(string $table, string $attribute, ?string $ignoreByInputRef = null, ?string $ignoreByColumn = null, ?string $message = null): StringDefinition
    {
        $this->constraints->add(
            new UniqueConstraint($table, $attribute, $ignoreByInputRef, $ignoreByColumn, $message)
        );
        return $this;
    }


    /**
     * Соответствует паттерну
     * @param string $pattern
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function pattern(string $pattern, ?string $message = null): StringDefinition
    {
        $this->constraints->add(
            new PatternConstraint($pattern, $message)
        );
        return $this;
    }

    /**
     * Слаг
     * @param string $spacer
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function slug(string $spacer = '_', ?string $message = null): self
    {
        $this->constraints->add(
            FormatConstraint::slug($message)
        );
        return $this;
    }

    /**
     * Слаг для URL
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function urlSlugCompatible(?string $message = null): self
    {
        $this->constraints->add(
            FormatConstraint::urlSlugCompatible($message)
        );
        return $this;
    }
    /**
     * Относительный URL
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function uriPath(?string $message = null): self
    {
        $this->constraints->add(
            FormatConstraint::uriPath($message)
        );
        return $this;
    }



    /**
     * Только цифры
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function digits(?string $message=null) : self
    {
        $this->constraints->add(
            FormatConstraint::digits($message)
        );
        return $this;
    }

    /**
     * Гибкий формат телефона
     * Допускает наличие или отсутствие кода +7 или 8
     * Допускает наличие пробелов между группами
     * Допускает обрамление скобками код оператора
     * Допускает дефисы-тире между группами
     *
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function flexPhone(?string $message=null): self
    {
        $this->constraints->add(
            FormatConstraint::flexPhone($message)
        );
        return $this;
    }

    /**
     * IPv4 формат
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function ipv4Format(?string $message = null): StringDefinition
    {
        $this->constraints->add(
            FormatConstraint::ipv4($message)
        );
        return $this;
    }

    /**
     * IPv6 формат
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function ipv6Format(?string $message = null): StringDefinition
    {
        $this->constraints->add(
            FormatConstraint::ipv6($message)
        );
        return $this;
    }

    /**
     * Формат даты. Дата в формате RFC 3339 - "Y-m-d"
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function dateFormat(?string $message = null): StringDefinition
    {
        $this->constraints->add(
            FormatConstraint::date($message)
        );
        return $this;
    }

    /**
     * Формат даты-времени RFC 3339 - "Y-m-d\TH:i:sP"
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function datetimeFormat(?string $message = null): StringDefinition
    {
        $this->constraints->add(
            FormatConstraint::datetime($message)
        );
        return $this;
    }

    /**
     * UUID формат
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function uuidFormat(?string $message = null): StringDefinition
    {
        $this->constraints->add(
            FormatConstraint::uuid($message)
        );
        return $this;
    }

    /**
     * E-mail формат
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function emailFormat(?string $message = null): StringDefinition
    {
        $this->constraints->add(
            FormatConstraint::email($message)
        );
        return $this;
    }

    /**
     * URI формат (ссылка)
     * @param string|null $message
     * @return $this
     * @throws ConstraintAlreadyAppliedException
     */
    public function uriFormat(?string $message = null): StringDefinition
    {
        $this->constraints->add(
            FormatConstraint::uri($message)
        );
        return $this;
    }

    public function exampleData()
    {
        return $this->getComment() ? $this->getComment() : "Строка";
    }
}
