<?php


namespace IceCloud\RpcServer\Lib\Responses\Errors;

use IceCloud\RpcServer\Lib\Responses\ErrorResponse;

/**
 * Внутренняя ошибка сервера
 *
 * @package IceCloud\RpcServer\Lib\Responses\Errors
 * @author a.kazakov
 */
class InternalErrorResponse extends ErrorResponse
{
    public function __construct(?int $id, string $version, ?string $message = null)
    {
        parent::__construct(
            $id,
            $version,
            self::INTERNAL_ERROR_CODE,
            $message ? $message : "Internal error"
        );
    }
}
