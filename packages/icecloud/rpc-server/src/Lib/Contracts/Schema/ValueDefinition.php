<?php


namespace IceCloud\RpcServer\Lib\Contracts\Schema;

use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;
use IceCloud\RpcServer\Lib\Schema\Definitions\CompositeDefinition;

/**
 * Interface TypeContract
 * @package IceCloud\RpcServer\Contracts
 */
interface ValueDefinition
{
    /**
     * Имя определения
     * @return string|null
     */
    public function getName() : ?string;

    /**
     * Родитель
     * @return CompositeDefinition|null
     */
    public function getParent() : ?CompositeDefinition;

    /**
     * Все родители
     * @return ValueDefinition[]
     */
    public function getParents() : array;

    /**
     * Квалифицированное имя
     * @return string
     */
    public function getFullQualifiedName(): string;

    /**
     * Ограничения
     * @return ConstraintsMap
     */
    public function getConstraints() : ConstraintsMap;

    /**
     * Сигнатура определения
     *
     * @return string
     */
    public function hash(): string;

    /**
     * Определение массива
     * @return bool
     */
    public function isArray() : bool;

    /**
     * Определение массива объектов
     * @return bool
     */
    public function isArrayOfObjects() : bool;

    /**
     * Определение объекта
     * @return bool
     */
    public function isObject() : bool;

    /**
     * Составное определение (массив или объект)
     * @return bool
     */
    public function isComposite() : bool;

    /**
     * Скалярное определение
     * @return bool
     */
    public function isScalar() : bool;

    /**
     * Получить комментарий определения
     * @return string|null
     */
    public function getComment() : ?string;

    /**
     * Пример данных, построенный на основе ограничений определения
     * @return mixed
     */
    public function exampleData();
}
