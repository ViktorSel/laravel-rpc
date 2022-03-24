<?php


namespace IceCloud\RpcServer\Lib\Generator\Compiler\Js\Virtual;


use App\Rpc\RpcServer;
use IceCloud\RpcServer\Lib\Contracts\Generator\Js\JsVariable;
use IceCloud\RpcServer\Lib\Procedure;

class JsProcedure
{
    /**
     * @var Procedure
     */
    private $procedure;

    /**
     * @var JsVariable[]
     */
    private array $arguments = [];

    private ?JsVariable $dedicated = null;

    public function __construct(Procedure $procedure)
    {
        $this->procedure = $procedure;
    }

    public function addArgument(JsVariable $variable) : void
    {
        $this->arguments[$variable->name()] = $variable;
    }

    public function setDedicatedArgument(JsVariable $variable): void {
        $this->dedicated = $variable;
    }

    /**
     * @return JsVariable|null
     */
    public function getDedicatedArgument(): ?JsVariable
    {
        return $this->dedicated;
    }

    public function isDedicatedArgument(): bool {
        return $this->dedicated !== null;
    }

    /**
     * @return JsVariable[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return Procedure
     */
    public function getProcedure(): Procedure
    {
        return $this->procedure;
    }
}
