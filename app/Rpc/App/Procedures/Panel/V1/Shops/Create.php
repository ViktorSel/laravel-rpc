<?php
namespace App\Rpc\App\Procedures\Panel\V1\Shops;

use IceCloud\RpcServer\Lib\Contracts\Schema\SchemaContainerContract;
use IceCloud\RpcServer\Lib\Middleware;
use IceCloud\RpcServer\Lib\Procedure;
use IceCloud\RpcServer\Lib\Request;
use IceCloud\RpcServer\Lib\Schema\ArgumentsSchema;
use IceCloud\RpcServer\Lib\Schema\ResultSchema;

use App\Models\User;
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
        return "Создание магазина";
    }

    /**
     * Имя процедуры.
     *
     * @return  string
     */
    public function name(): string
    {
        return "Panel.V1.Shops.Create";
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
            ->max(255,'Поле :attribute должно быть не больше 255 символов');
        $schema->string(Shop::FIELD_USER_ID)
            ->required('Заполните обязательное поле :attribute')
            ->uuidFormat('Неверный тип идентификатора :attribute')
            ->exists(User::class,User::FIELD_ID,'Пользователь с таким идентификатором не найден');
        $schema->string(Shop::FIELD_URL)
            ->required('Заполните обязательное поле :attribute')
            ->uriFormat('Неверный тип поля :attribute');
        $schema->string(Shop::FIELD_EMAIL)
            ->required('Заполните обязательное поле :attribute')
            ->emailFormat('Неверный тип поля :attribute');
        $schema->int(Shop::FIELD_INN)
            ->required('Заполните обязательное поле :attribute')
            ->min(1000000000,'Поле :attribute должно содержать не менее 10 знаков')
            ->max(9999999999);
        $schema->string(Shop::FIELD_ACCOUNTER)
            ->required('Заполните обязательное поле :attribute')
            ->max(255,'Поле :attribute должно быть не больше 255 символов');
        $schema->int(Shop::FIELD_ACCOUNTER_INN)
            ->required('Заполните обязательное поле :attribute')
            ->min(100000000000,'Поле :attribute должно содержать не менее 12 знаков')
            ->max(999999999999);
        $schema->string('tax')
            ->required('Заполните обязательное поле :attribute')
            ->enum(Shop::TAX,'Недопустимое значение поля :attribute');
        $schema->bool(Shop::FIELD_ACTIVE);
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
        $shop = Shop::query()->create($request->getParams());
        if ($shop) {
            return $shop;
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
