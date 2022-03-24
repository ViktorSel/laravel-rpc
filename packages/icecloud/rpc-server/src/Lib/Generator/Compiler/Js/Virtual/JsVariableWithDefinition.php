<?php


namespace IceCloud\RpcServer\Lib\Generator\Compiler\Js\Virtual;


use IceCloud\RpcServer\Lib\Contracts\Generator\Js\JsVariable;
use IceCloud\RpcServer\Lib\Contracts\Schema\ValueDefinition;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsClassFile;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsInputModelFile;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\DefaultConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\NullableConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\RequiredConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Types\BooleanConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Types\FloatConstraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\Types\IntegerConstraint;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ArrayDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ObjectDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\BoolDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\FloatDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\IntDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Scalar\StringDefinition;
use Illuminate\Support\Str;

class JsVariableWithDefinition implements JsVariable
{
    /**
     * @var ValueDefinition
     */
    protected ValueDefinition $definition;
    protected ?JsClassFile $use;
    protected string $constant;

    public function __construct(ValueDefinition $definition, ?JsClassFile $use = null)
    {
        $this->definition = $definition;
        $this->use = $use;
        $this->constant = 'ATTR_' . Str::upper(Str::snake($definition->getName()));
    }

    /**
     * @param JsClassFile|null $use
     */
    public function setUse(JsClassFile $use): void
    {
        $this->use = $use;
    }

    /**
     * @return JsInputModelFile|null
     */
    public function getUse(): ?JsClassFile
    {
        return $this->use;
    }


    public function type(): string
    {
        $def = $this->definition;
        $constraints = $def->getConstraints();

        $dec = '';
        $constraints->exists(RequiredConstraint::class) && $dec = '!';
        $constraints->exists(NullableConstraint::class) && $dec = '?';

        $type = [];

        $def instanceof IntDefinition && $type[] = $dec.'number';
        $def instanceof FloatDefinition && $type[] = $dec.'number';
        $def instanceof StringDefinition && $type[] = $dec.'string';
        $def instanceof BoolDefinition && $type[] = $dec.'boolean';

        if ($this->use) {
            $def instanceof ObjectDefinition && $type[] = $dec.$this->use->getClassName();
            $def instanceof ArrayDefinition && $type[] = $dec.$this->use->getClassName() . '[]';
        } else {
            $def instanceof ObjectDefinition && $type[] = $dec.'Object';
            $def instanceof ArrayDefinition && $type[] = $dec.'Array';
        }

        return implode('|', $type);
    }
    /**
     * @return ValueDefinition
     */
    public function getDefinition(): ValueDefinition
    {
        return $this->definition;
    }

    public function name(): string
    {
        return $this->definition->getName();
    }

    public function comment(): string
    {
        return $this->definition->getComment() === null ? "The {$this->definition->getName()}" : $this->definition->getComment();
    }

    public function constant(): string
    {
        return $this->constant;
    }

    public function camel(): string
    {
        return Str::camel($this->name());
    }

}
