<?php
namespace App\Rpc\App\Procedures\Panel\V1\PaySystems;

use IceCloud\RpcServer\Lib\Contracts\Schema\SchemaContainerContract;
use IceCloud\RpcServer\Lib\Middleware;
use IceCloud\RpcServer\Lib\Procedure;
use IceCloud\RpcServer\Lib\Request;
use IceCloud\RpcServer\Lib\Schema\ArgumentsSchema;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ObjectDefinition;
use IceCloud\RpcServer\Lib\Schema\ResultSchema;

use App\Models\Shop;
use App\Models\PaySystem;
use Illuminate\Support\Facades\Hash;

class Create extends Procedure
{
    /**
     * Описание процедуры, которое будет использовано для генерации swagger и комментариев клиента.
     * @return  string
     */
    public function description(): string
    {
        return "Создание платежной системы для магазина";
    }

    /**
     * Имя процедуры.
     *
     * @return  string
     */
    public function name(): string
    {
        return "Panel.V1.PaySystems.Create";
    }

    /**
     * Схема аргументов формируется по всему пайплайну в режиме forward - т.е. сначала отработают middlewares
     * в порядке своих позиций, затем метод процедуры. В качестве аргумента передается инстанс схемы, который
     * модифицируется методом по своему усмотрению.
     *
     * @param  ArgumentsSchema $schema
     * @return  void
     */
    public function argumentsSchema(ArgumentsSchema $schema)
    {
        $schema->string(Shop::FIELD_LABEL)
            ->required('Заполните обязательное поле :attribute')
            ->enum(PaySystem::PAY_SYSTEMS,'Недопустимое значение поля :attribute');
        $schema->string(PaySystem::FIELD_SHOP_ID)
            ->required('Заполните обязательное поле :attribute')
            ->uuidFormat('Неверный тип идентификатора :attribute')
            ->exists(Shop::class,Shop::FIELD_ID,'Магазин с таким идентификатором не найден');
        $schema->object(PaySystem::FIELD_CONFIG, function (ObjectDefinition $object) {
                $object->string("terminalKey")
                    ->required('Заполните обязательное поле :attribute')
                    ->min(1)
                    ->max(255);
                $object->string("secretKey")
                    ->required('Заполните обязательное поле :attribute')
                    ->min(1);
                $object->string('url')
                    ->required('Заполните обязательное поле :attribute')
                    ->uriFormat('Неверный тип поля :attribute');
            })
            ->required('Заполните обязательное поле :attribute');
        $schema->bool(PaySystem::FIELD_ACTIVE);
    }

    /**
     * Схема результата формируется также, как и в {@link  SchemaContainer::argumentsSchema()},
     * но в обратном порядке.
     *
     * @param  ResultSchema $schema
     * @return  mixed|void
     */
    public function resultSchema(ResultSchema $schema)
    {
        $schema->bool()->comment("Успех операции");
    }

    /**
     * Определяет middlewares, формируя пайплайн вызова процедуры.
     * В массиве должны быть переданы имена классов middleware, унаследованные от {@link  Middleware} или их инстансы
     *
     * @return  Middleware[]|string[]
     */
    public function middlewares(): array
    {
        return [
            // Классы middlewares
        ];
    }

    /**
     * Обработчик процедуры, на вход которой поступают проверенные данные
     * с помощью схемы аргументов {@link  SchemaContainerContract::argumentsSchema()}
     *
     * @param  Request $request
     * @return  mixed
     */
    public function handle(Request $request)
    {
        $paySystem = PaySystem::query()->create($request->getParams());
        if ($paySystem) {
            return $paySystem;
        }
        else {
            throw new \Exception('123123',-23);
        }
    }

    /**
     * Нужен ли детализированный вывод ошибок (ДДО) - это, как правило,
     * нужно для фронтенда с сервер-сайд валидацией
     *
     * @return  bool
     */
    public function hasDetailedValidationErrors(): bool
    {
        return false;
    }

}
