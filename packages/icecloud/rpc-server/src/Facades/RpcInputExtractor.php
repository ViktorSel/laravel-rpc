<?php


namespace IceCloud\RpcServer\Facades;


use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\FormatConstraint;
use Illuminate\Support\Facades\Facade;

class RpcInputExtractor extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return static::class;
    }

    public function flexPhone(string $value): string
    {
        return preg_replace(FormatConstraint::REGEX_FLEX_PHONE, '$1$2$3$4', $value);
    }
}
