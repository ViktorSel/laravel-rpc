<?php


namespace IceCloud\RpcServer\Lib\Responses\Errors;


use IceCloud\RpcServer\Lib\Responses\ErrorResponse;

/**
 * Метод не найден
 *
 * @package IceCloud\RpcServer\Lib\Responses\Errors
 * @author a.kazakov
 */
class MethodNotFoundResponse extends ErrorResponse
{
    public function __construct(?int $id, string $version)
    {
        parent::__construct(
            $id,
            $version,
            self::METHOD_NOT_FOUND_CODE,
            "Method not found",
            null
        );
    }
}
