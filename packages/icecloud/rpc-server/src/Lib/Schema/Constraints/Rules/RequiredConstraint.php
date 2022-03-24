<?php


namespace IceCloud\RpcServer\Lib\Schema\Constraints\Rules;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\WithLaravelMessageConstraint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConflictConstraintsException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\DefaultConstraint;
use IceCloud\RpcServer\Lib\Schema\Definitions\CompositeDefinition;

class RequiredConstraint extends Constraint implements WithLaravelMessageConstraint
{
    protected $priority = self::ULTRA_PRIORITY;

    public function assert(ConstraintsMap $constraints)
    {
        $excepted = $constraints->exists(DefaultConstraint::class, NullableConstraint::class);
        if ($excepted) {
            throw new ConflictConstraintsException($this, [
                DefaultConstraint::class
            ]);
        }
    }

    protected function hasRequiredWithAll() : bool {
        return $this->getMap()->getDefinition()->getParent() instanceof CompositeDefinition;
    }

    function key(): string
    {
        return 'required';
    }

    public function toLaravelValidation() : ?string
    {
        if ($this->hasRequiredWithAll()) {
            $definition = $this->getMap()->getDefinition();
            $fields = [];

            foreach ($definition->getParents() as $parent) {
                $fields[] = $parent->getFullQualifiedName();
            }

            return 'required_with_all:'.implode(',', $fields);
        }

        return 'required';
    }

    public function toLaravelMessageAnchor(): string
    {
        return $this->hasRequiredWithAll() ? 'required_with_all' : 'required';
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    function print(): string
    {
        return $this->key();
    }
}
