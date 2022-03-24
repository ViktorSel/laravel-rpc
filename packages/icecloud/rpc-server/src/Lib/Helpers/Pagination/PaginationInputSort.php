<?php


namespace IceCloud\RpcServer\Lib\Helpers\Pagination;


class PaginationInputSort
{
    private string $property;
    private bool $desc;

    public function __construct(array $item)
    {
        $this->property = $item[PaginationSchema::ARG_OBJECT_SORT_PROPERTY];
        $this->desc = $item[PaginationSchema::ARG_OBJECT_SORT_DESC];
    }

    /**
     * @return mixed|string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @return bool|mixed
     */
    public function isDesc(): bool
    {
        return $this->desc;
    }
}
