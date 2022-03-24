<?php


namespace IceCloud\RpcServer\Lib\Utils;

/**
 * Класс-утилита для проверки соответствия имен методов маске форма "Foo.Bar.*"
 * @package IceCloud\RpcServer\Lib\Utils
 * @author a.kazakov <a.kazakov@iceberg.ru>
 *
 *
 */
class MethodsMask
{
    const CATALOGUE_SEPARATOR = '.';
    const CATALOGUE_SEPARATOR_EXPRESSION = '\.';
    const LOT_OF_SYMBOL = '*';
    const LOT_OF_EXPRESSION = '[a-zA-Z0-9\.]+';

    private string $mask;
    public function __construct(string $mask)
    {
        $this->mask = $mask;
    }

    /**
     * Соответствует ли имя метода маске
     * @param string $methodName
     * @return bool
     */
    public function match(string $methodName): bool
    {
        return static::fastMatch($methodName, $this->mask);
    }

    /**
     * Для "быстрой" проверки аналогично {@link MethodsMask::match()} без создания класса
     * @param string $methodName
     * @param string $mask
     * @return bool
     */
    public static function fastMatch(string $methodName, string $mask): bool
    {
        return preg_match(self::createRegex($mask), trim($methodName));
    }

    /**
     * Создать регулярное выражение из маски
     * @param $mask
     * @return string
     */
    protected static function createRegex($mask): string
    {
        $regex = str_replace([
            self::CATALOGUE_SEPARATOR,
            self::LOT_OF_SYMBOL
        ], [
            self::CATALOGUE_SEPARATOR_EXPRESSION,
            self::LOT_OF_EXPRESSION
        ],
            $mask
        );

        return '/^' . $regex . '$/iu';
    }
}
