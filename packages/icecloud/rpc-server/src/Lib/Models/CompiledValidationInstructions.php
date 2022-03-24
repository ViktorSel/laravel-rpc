<?php


namespace IceCloud\RpcServer\Lib\Models;


use IceCloud\RpcServer\Lib\Contracts\Cache\ExportedClassContract;

class CompiledValidationInstructions implements ExportedClassContract
{
    private array $rules;
    private array $messages;
    private array $defaults;

    public function __construct(array $rules, array $messages, array $defaults)
    {
        $this->rules = $rules;
        $this->messages = $messages;
        $this->defaults = $defaults;
    }

    public static function __set_state(array $an_array): CompiledValidationInstructions
    {
        return new static($an_array['rules'], $an_array['messages'], $an_array['defaults']);
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }
}
