<?php


namespace IceCloud\RpcServer\Lib\Helpers\Pagination;


use IceCloud\RpcServer\Lib\Contracts\Schema\ObjectBlueprint;
use IceCloud\RpcServer\Lib\Contracts\Schema\ValueDefinition;
use IceCloud\RpcServer\Lib\Exceptions\Middlewares\RequiredResultDefinitionException;
use IceCloud\RpcServer\Lib\Schema\ArgumentsSchema;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ArrayDefinition;
use IceCloud\RpcServer\Lib\Schema\Definitions\Composite\ObjectDefinition;
use IceCloud\RpcServer\Lib\Schema\ResultSchema;

class PaginationSchema
{
    const ARG_OBJECT = 'paginate';
    const ARG_OBJECT_LIMIT = 'limit';
    const ARG_OBJECT_PAGE = 'page';
    const ARG_OBJECT_SORT_ARRAY = 'sort';
    const ARG_OBJECT_SORT_PROPERTY = 'property';
    const ARG_OBJECT_SORT_DESC = 'desc';

    const RES_TOTAL = 'total';
    const RES_ITEMS = 'items';

    static function arguments(ArgumentsSchema $schema) : ObjectBlueprint
    {
        $schema->object(self::ARG_OBJECT, function (ObjectBlueprint $object) {
            $object->int(self::ARG_OBJECT_LIMIT)
                ->min(1)
                ->default(PaginationInput::DEFAULT_LIMIT)
                ->comment("Лимит записей на страницу");

            $object->int(self::ARG_OBJECT_PAGE)
                ->min(1)
                ->default(PaginationInput::DEFAULT_PAGE)
                ->comment("Страница пагинации");

            $object->array(self::ARG_OBJECT_SORT_ARRAY, function () {
                return new ObjectDefinition(function (ObjectBlueprint $object) {
                    $object->string(self::ARG_OBJECT_SORT_PROPERTY)
                        ->comment('Свойство сортировки')
                        ->required();
                    $object->bool(self::ARG_OBJECT_SORT_DESC)
                        ->comment("DESC режим");

                });
            })->comment("Массив свойств для сортировки");
        })->nullable();

        return $schema;
    }

    static function results(ResultSchema $schema, callable $of): ArrayDefinition
    {
        $array = null;
        $schema->object(function (ObjectBlueprint $object) use ($of, &$array) {
            $object->int(self::RES_TOTAL)->min(0)->comment("Всего записей");
            $array = $object->array(self::RES_ITEMS, $of);
        });
        return $array;
    }
}
