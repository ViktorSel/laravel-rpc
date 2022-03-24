<?php


namespace IceCloud\RpcServer\Lib\Generator\Compiler\Js;


use IceCloud\RpcServer\Lib\Contracts\Generator\Js\JsVariable;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\Virtual\JsProcedure;

class JsInputModelsCompilingResult
{
    /**
     * @var JsInputModelFile[]
     */
    private array $files;
    /**
     * @var JsProcedure[]
     */
    private array $jsProcedures;

    public function __construct(array $files, array $jsProcedures)
    {
        $this->files = $files;
        $this->jsProcedures = $jsProcedures;
    }

    /**
     * @return JsInputModelFile[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @return JsVariable[]
     */
    public function getJsProcedures(): array
    {
        return $this->jsProcedures;
    }
}
