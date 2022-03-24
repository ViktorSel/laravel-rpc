<?php
namespace App\Rpc\App\Procedures\Panel\V1\Users;

use IceCloud\RpcServer\Lib\Contracts\Schema\SchemaContainerContract;
use IceCloud\RpcServer\Lib\Middleware;
use IceCloud\RpcServer\Lib\Procedure;
use IceCloud\RpcServer\Lib\Request;
use IceCloud\RpcServer\Lib\Schema\ArgumentsSchema;
use IceCloud\RpcServer\Lib\Schema\ResultSchema;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Create extends Procedure
{
    /**
     * Описание процедуры, которое будет использовано для генерации swagger и комментариев клиента.
     * @return  string
     */
    public function description(): string
    {
        return "Создание пользователя";
    }

    /**
     * Имя процедуры.
     *
     * @return  string
     */
    public function name(): string
    {
        return "Panel.V1.Users.Create";
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
//        $schema->object("book", function (ObjectBlueprint $object) {
//            $object->string("title")
//                ->required()
//                ->min(2)
//                ->max(255);
//            $object->float("price")
//                ->required()
//                ->min(0.01);
//        });
        $schema->string(User::FIELD_NAME)
            ->required('Заполните обязательное поле :attribute')
            ->max(255,'Поле :attribute должно быть не больше 255 символов');
        $schema->string(User::FIELD_EMAIL)
            ->required('Заполните обязательное поле :attribute')
            ->emailFormat('Не верный формат эл почты в поле :attribute')
            ->unique(User::TABLE, User::FIELD_EMAIL,null,null,'Пользователь с такой почтой уже существует');
        $schema->string(User::FIELD_PASSWORD)
            ->required('Заполните обязательное поле :attribute');
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
        $user = User::query()->create(array_merge($request->getParams(),[User::FIELD_PASSWORD => Hash::make($request->input(User::FIELD_PASSWORD))]));
        if ($user) {
            return $user;
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
