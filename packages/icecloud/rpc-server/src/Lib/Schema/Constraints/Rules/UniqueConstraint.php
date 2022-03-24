<?php

namespace IceCloud\RpcServer\Lib\Schema\Constraints\Rules;

use IceCloud\RpcServer\Lib\Contracts\Schema\Constraints\WithLaravelMessageConstraint;
use IceCloud\RpcServer\Lib\Exceptions\Schema\Constraints\ConflictConstraintsException;
use IceCloud\RpcServer\Lib\Schema\Constraints\Constraint;
use IceCloud\RpcServer\Lib\Schema\Constraints\ConstraintsMap;
use IceCloud\RpcServer\Lib\Server;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\Validator;

class UniqueConstraint extends Constraint implements WithLaravelMessageConstraint
{
    const VALIDATOR = 'rpc_unique';

    private string $table;
    private string $attribute;
    private ?string $ignoreByInputRef;
    private ?string $ignoreByColumn;

    protected function resolveTableName(string $entity): string
    {
        if (! Str::contains($entity, '\\') || ! class_exists($entity)) {
            return $entity;
        }

        if (is_subclass_of($entity, Model::class)) {
            $model = new $entity;

            $entity=$model->getTable();
        }

        return $entity;
    }

    public function __construct(string $entity, string $attribute, ?string $ignoreByInputRef = null, ?string $ignoreByColumn = null, ?string $message = null)
    {
        $this->table = $this->resolveTableName($entity);
        $this->attribute = $attribute;
        $this->ignoreByInputRef = $ignoreByInputRef;
        $this->ignoreByColumn = $ignoreByColumn ?? 'id';
//        $this->rule = (new Unique($modelClass, $field));
        parent::__construct($message);
    }

    function key(): string
    {
        return self::VALIDATOR;
    }

    function print(): string
    {
        return sprintf($this->key().':%s,%s,%s,%s', $this->table, $this->attribute, $this->ignoreByInputRef ?? 'NULL', $this->ignoreByColumn);
    }

    public function assert(ConstraintsMap $constraints)
    {
        $excepted = $constraints->exists(ExistsConstraint::class);
        if ($excepted) {
            throw new ConflictConstraintsException($this, [
                ExistsConstraint::class
            ]);
        }
    }

    function toLaravelValidation()
    {
        return $this->print();
    }

    public static function boot()
    {
        \Validator::extend(self::VALIDATOR, function ($attribute, $value, $parameters, Validator $validator) {
            $validator->addReplacer(self::VALIDATOR, function ($message) use($value) {
                return str_replace(':value', $value, $message);
            });

            $q = \DB::query()
                ->from($parameters[0])
                ->where([$parameters[1] => $value]);

            if ($parameters[2] !== 'NULL') {
                $q->where($parameters[3], '!=', Server::request()->input($parameters[2]));
            }

            return !$q->count();
        }, "Запись ':value' уже существует");
    }

    public function toLaravelMessageAnchor(): string
    {
        return $this->key();
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }



//    public static function boot()
//    {
//        \Validator::extend('rpc_unique', function ($attribute, $value, $params, $validator) {
//            dd($params);
//        });
//    }
}
