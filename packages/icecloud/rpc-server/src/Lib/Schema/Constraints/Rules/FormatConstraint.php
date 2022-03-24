<?php


namespace IceCloud\RpcServer\Lib\Schema\Constraints\Rules;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\ValuedConstraint;
use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\WithLaravelMessageConstraint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConstraintConfigurationException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\PatternConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Types\StringConstraint;

class FormatConstraint extends Constraint implements ValuedConstraint, WithLaravelMessageConstraint
{
    const FORMAT_IPV4 = 'ipv4';
    const FORMAT_IPV6 = 'ipv6';
    const FORMAT_DATE = 'date';
    const FORMAT_DATETIME = 'datetime';
    const FORMAT_EMAIL = 'email';
    const FORMAT_UUID = 'uuid';
    const FORMAT_URI = 'uri';
    const FORMAT_RELATIVE_URI = 'relative_uri';
    const FORMAT_URI_PATH = 'uri_path';

    const FORMAT_DIGITS = 'digits';
    const FORMAT_URL_SLUG_COMPATIBLE = 'url_slug';
    const FORMAT_SLUG = 'slug';
    const FORMAT_FLEX_PHONE = 'flex_phone';

    const REGEX_DIGITS = '/^[0-9]+$/';
    const REGEX_SLUG = '/^[0-9a-z_]+$/';
    const REGEX_URL_SLUG_COMPATIBLE = '/^[0-9a-z-]+$/';
    /**
     * Честно спизжено у симфони 6.1
     * Внимание, паттерны не поддерживают экранированные символы %%[A-Fa-f0-9]{2}, что странно - они есть в паттернах,
     * но не отрабатывают
     *
     * https://github.com/symfony/validator/blob/6.1/Constraints/UrlValidator.php
     */
//    "~^
//        (?:/ (?:[\pL\pN\-._\~!$&\'()*+,;=:@]|%%[0-9A-Fa-f]{2})* )*          # a path
//        (?:\? (?:[\pL\pN\-._\~!$&\'\[\]()*+,;=:@/?]|%%[0-9A-Fa-f]{2})* )?   # a query (optional)
//        (?:\# (?:[\pL\pN\-._\~!$&\'()*+,;=:@/?]|%%[0-9A-Fa-f]{2})* )?       # a fragment (optional)
//    $~ixu";
    const REGEX_URI_PATH = "~^
        (?:/ (?:[\pL\pN\-._\~!$&\'()*+,;=:@]|%%[0-9A-Fa-f]{2})* )+          # a path
    $~ixu";
    const REGEX_FLEX_PHONE = '/^\s*(?:\+7|8)?\s*\(?\s*(\d{3})\s*\)?\s*(\d{3})\s*\-?\s*(\d{2})\s*\-?\s*(\d{2})\s*$/';

    public $value;

    public static $formats = [
        self::FORMAT_IPV4,
        self::FORMAT_IPV6,
        self::FORMAT_EMAIL,
        self::FORMAT_DATE,
        self::FORMAT_DATETIME,
        self::FORMAT_UUID,
        self::FORMAT_URI,
        self::FORMAT_URI_PATH,

        self::FORMAT_SLUG,
        self::FORMAT_URL_SLUG_COMPATIBLE,
        self::FORMAT_DIGITS,
        self::FORMAT_FLEX_PHONE
    ];

    public function __construct(string $format, ?string $message = null)
    {
        $this->value = $format;
        parent::__construct($message);
    }

    public static function slug(?string $message = null): self
    {
        return new static(self::FORMAT_SLUG, $message);
    }

    public static function urlSlugCompatible(?string $message = null): self
    {
        return new static(self::FORMAT_URL_SLUG_COMPATIBLE, $message);
    }

    public static function digits(?string $message = null): self
    {
        return new static(self::FORMAT_DIGITS, $message);
    }

    public static function flexPhone(?string $message = null): self
    {
        return new static(self::FORMAT_FLEX_PHONE, $message);
    }

    public static function ipv4(?string $message = null): FormatConstraint
    {
        return new static(self::FORMAT_IPV4, $message);
    }

    public static function ipv6(?string $message = null): FormatConstraint
    {
        return new static(self::FORMAT_IPV6, $message);
    }

    public static function date(?string $message = null): FormatConstraint
    {
        return new static(self::FORMAT_DATE, $message);
    }

    public static function datetime(?string $message = null): FormatConstraint
    {
        return new static(self::FORMAT_DATETIME, $message);
    }

    public static function email(?string $message = null): FormatConstraint
    {
        return new static(self::FORMAT_EMAIL, $message);
    }

    public static function uuid(?string $message = null): FormatConstraint
    {
        return new static(self::FORMAT_UUID, $message);
    }

    public static function uri(?string $message = null): FormatConstraint
    {
        return new static(self::FORMAT_URI, $message);
    }

    public static function uriPath(?string $message = null): FormatConstraint
    {
        return new static(self::FORMAT_URI_PATH, $message);
    }

    function key(): string
    {
        return 'format';
    }

    public function assert(ConstraintsMap $constraints)
    {
        if (!in_array($this->value, self::$formats, true)) {
            throw new ConstraintConfigurationException(
                "Некорректный формат '{$this->value}', допускаются [" .
                implode(', ', self::$formats) .
                "]"
            );
        }

        if (!$constraints->exists(StringConstraint::class)) {
            throw new ConstraintConfigurationException("Формат может быть применен только для типа string");
        }

        if ($constraints->exists(PatternConstraint::class)) {
            throw new ConstraintConfigurationException("Формат исключает применение регулярного выражения");
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function toLaravelValidation() : ?string
    {
        switch ($this->value) {
            case self::FORMAT_IPV4: return 'ipv4';
            case self::FORMAT_IPV6: return 'ipv6';
            case self::FORMAT_EMAIL: return 'email';
            case self::FORMAT_UUID: return 'uuid';
            case self::FORMAT_DATE: return 'date_format:Y-m-d';
            case self::FORMAT_DATETIME: return 'date_format:' . \DateTimeInterface::W3C;
            case self::FORMAT_URI: return 'url';
            case self::FORMAT_DIGITS: return 'regex:' . self::REGEX_DIGITS;
            case self::FORMAT_SLUG: return 'regex:' . self::REGEX_SLUG;
            case self::FORMAT_URL_SLUG_COMPATIBLE: return 'regex:' . self::REGEX_URL_SLUG_COMPATIBLE;
            case self::FORMAT_FLEX_PHONE: return 'regex:' . self::REGEX_FLEX_PHONE;
            case self::FORMAT_URI_PATH: return 'regex:' . self::REGEX_URI_PATH;
        }
        return null;
    }

    public function toLaravelMessageAnchor(): string
    {
        switch ($this->value) {
            case self::FORMAT_IPV4: return 'ipv4';
            case self::FORMAT_IPV6: return 'ipv6';
            case self::FORMAT_EMAIL: return 'email';
            case self::FORMAT_UUID: return 'uuid';
            case self::FORMAT_DATE:
            case self::FORMAT_DATETIME: return 'date_format';
            case self::FORMAT_URI: return 'url';
            case self::FORMAT_SLUG:
            case self::FORMAT_URL_SLUG_COMPATIBLE:
            case self::FORMAT_URI_PATH:
            case self::FORMAT_DIGITS:
            case self::FORMAT_FLEX_PHONE: return 'regex';
        }
        return '';
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    function print(): string
    {
        return $this->key() . $this->value;
    }
}
