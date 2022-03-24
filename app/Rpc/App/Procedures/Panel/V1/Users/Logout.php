<?php
namespace App\Rpc\App\Procedures\Panel\V1\Users;

use IceCloud\RpcServer\Lib\Contracts\Schema\SchemaContainerContract;
use IceCloud\RpcServer\Lib\Middleware;
use IceCloud\RpcServer\Lib\Procedure;
use IceCloud\RpcServer\Lib\Request;
use IceCloud\RpcServer\Lib\Schema\ArgumentsSchema;
use IceCloud\RpcServer\Lib\Schema\ResultSchema;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Logout extends Procedure
{
    /**
     * Описание процедуры, которое будет использовано для генерации swagger и комментариев клиента.
     * @return  string
     */
    public function description(): string
    {
        return "Авторизация пользователя";
    }

    /**
     * Имя процедуры.
     *
     * @return  string
     */
    public function name(): string
    {
        return "Panel.V1.Users.Logout";
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
        //Auth::user()->deleteToken();
        return Auth::user();
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
