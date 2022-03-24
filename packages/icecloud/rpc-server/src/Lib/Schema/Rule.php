<?php

namespace IceCloud\RpcServer\Lib\Schema;

use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RuleConstraint;
use IceCloud\RpcServer\Lib\Schema\Definitions\BaseDefinition;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;

/**
 * Абстракция правила поля схемы.
 * Используется ограничением {@link RuleConstraint} и принимается методом {@link BaseDefinition::rule()}
 */
abstract class Rule implements ImplicitRule, ValidatorAwareRule
{
    private ?string $attribute = null;
    private ?string $message = null;
    private ?Validator $validator = null;
    /**
     * Говорит, что нужно остановить следующие правила валидации атрибута, если это сгенерировало ошибки
     * @var bool
     */
    protected bool $shouldStopAttributeValidation = false;
    private bool $failed = false;

    final public function setValidator($validator)
    {
        $this->validator = $validator;
    }

    /**
     * Валидация значения.
     *
     * Ошибки валидации записываются методом {@link Rule::fail()}
     *
     * @param string $attribute
     * @param $value
     * @return mixed
     */
    abstract function validate(string $attribute, $value);

    final public function passes($attribute, $value): bool
    {
        $this->attribute = $attribute;
        $this->validate($attribute, $value);
        return !$this->failed;
    }

    final public function message()
    {
        return $this->message;
    }

    /**
     * Есть ли ошибки валидации атрибута
     * @return bool
     */
    final public function failed(): bool
    {
        return $this->failed;
    }

    /**
     * Получить значение любого атрибута из данных запроса
     * @param string|null $name
     * @param $default
     * @return array|\ArrayAccess|mixed
     */
    final public function attribute(?string $name = null, $default = null)
    {
        return Arr::get($this->validator->attributes(), $name, $default);
    }

    final public function siblingAttribute(string $name, $default = null)
    {
        $path = array_slice(explode('.', $this->attribute), 0, -1);
        return $this->attribute(
            join('.', array_merge($path, [$name])),
            $default
        );
    }

    /**
     * Разбивает ссылку на атрибут (специфичное для laravel именование) и возвращает путь к этому атрибуту
     * в виде массива
     *
     * @param string $attributeName
     * @return array
     */
    final public function extractPathAttribute(string $attributeName): array
    {
        return array_slice(
            explode('.', $attributeName),
            0,
            -1
        );
    }

    /**
     * Записывает ошибку валидации атрибута
     *
     * @param string $format Формат ошибки или ошибка
     * @param array $args Replacements в тексте ошибки
     * @return void
     */
    final public function fail(string $format, array $args = [])
    {
        $this->message = empty($args)
            ? $format
            : str_replace(array_keys($args), array_values($args), $format);

        $this->failed = true;

        if ($this->shouldStopAttributeValidation) {
            $bail = $this->validator->hasRule($this->attribute, 'Bail');

            if (!$bail) {
                $this->validator->addRules([
                    $this->attribute => ['bail']
                ]);
            }
        }
    }
}
