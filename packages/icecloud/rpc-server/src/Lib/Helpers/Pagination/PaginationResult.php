<?php


namespace IceCloud\RpcServer\Lib\Helpers\Pagination;


use IceCloud\RpcServer\Lib\ProcedureResult;

final class PaginationResult extends ProcedureResult
{
    private array $container;

    public function __construct(array $items, int $total)
    {
        $this->container = [
            PaginationSchema::RES_ITEMS => $items,
            PaginationSchema::RES_TOTAL => $total
        ];
    }

    public function jsonSerialize()
    {
        return $this->container;
    }
}
