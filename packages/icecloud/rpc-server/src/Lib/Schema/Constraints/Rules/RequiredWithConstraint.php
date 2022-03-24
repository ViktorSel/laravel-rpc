<?php


namespace IceCloud\RpcServer\Lib\Schema\Constraints\Rules;


use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\WithLaravelMessageConstraint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConflictConstraintsException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;
use IceCloud\RpcServer\Lib\Schema\Constraints\Rules\DefaultConstraint;
use IceCloud\RpcServer\Lib\Schema\Definitions\CompositeDefinition;

class RequiredWithConstraint extends Constraint implements WithLaravelMessageConstraint
{
    protected $priority = self::ULTRA_PRIORITY;

    private string $field;

    public function __construct(string $field, ?string $message = null)
    {
        $this->field = $field;
        parent::__construct($message);
    }

    public function assert(ConstraintsMap $constraints)
    {
        $excepted = $constraints->exists(DefaultConstraint::class, RequiredConstraint::class, NullableConstraint::class);
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
        return 'required_with';
    }

    public function toLaravelValidation() : ?string
    {
        if ($this->hasRequiredWithAll()) {
            $definition = $this->getMap()->getDefinition();
            $fields = [$this->field];

            foreach ($definition->getParents() as $parent) {
                $fields[] = $parent->getFullQualifiedName();
            }

            return 'required_with_all:'.implode(',', $fields);
        }

        return 'required_with:'. $this->field;
    }

    public function toLaravelMessageAnchor(): string
    {
        return $this->hasRequiredWithAll() ? 'required_with_all' : 'required_with';
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
