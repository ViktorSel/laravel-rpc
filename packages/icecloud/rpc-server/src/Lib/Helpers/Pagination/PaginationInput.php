<?php


namespace IceCloud\RpcServer\Lib\Helpers\Pagination;


use IceCloud\RpcServer\Lib\Request;

class PaginationInput
{
    const DEFAULT_LIMIT = 10;
    const DEFAULT_PAGE = 1;

    private int $page;
    private array $sort = [];
    private int $limit;

    public function __construct(Request $request)
    {
        $this->page = $this->input($request, PaginationSchema::ARG_OBJECT_PAGE, self::DEFAULT_PAGE);
        $this->limit = $this->input($request, PaginationSchema::ARG_OBJECT_LIMIT, self::DEFAULT_LIMIT);
        $sort = $this->input($request, PaginationSchema::ARG_OBJECT_SORT_ARRAY, []);
        foreach ($sort as $item) {
            $this->sort[] = new PaginationInputSort($item);
        }
    }

    protected function input(Request $request, string $path, $default = null)
    {
        return $request->input(PaginationSchema::ARG_OBJECT . '.' . $path, $default);
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return PaginationInputSort[]
     */
    public function getSort(): array
    {
        return $this->sort;
    }
}
