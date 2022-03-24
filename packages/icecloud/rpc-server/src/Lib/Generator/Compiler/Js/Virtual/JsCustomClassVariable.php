<?php


namespace IceCloud\RpcServer\Lib\Generator\Compiler\Js\Virtual;


use IceCloud\RpcServer\Lib\Contracts\Generator\Js\JsVariable;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsClassFile;
use Illuminate\Support\Str;

class JsCustomClassVariable implements JsVariable
{
    private $name;
    private $class;
    private ?string $comment;

    public function __construct($name, JsClassFile $file, ?string $comment = null)
    {
        $this->name = $name;
        $this->class = $file;
        $this->comment = $comment;
    }

    public function type(): string
    {
        return $this->class->getClassName();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function camel(): string
    {
        return Str::camel($this->name);
    }

    public function comment(): string
    {
        return $this->comment === null
            ? "The " . ucfirst($this->name)
            : $this->comment;
    }

    public function constant(): string
    {
        return Str::upper(Str::snake($this->name));
    }
}
