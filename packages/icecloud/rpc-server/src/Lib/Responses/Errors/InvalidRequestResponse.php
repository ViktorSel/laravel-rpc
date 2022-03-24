<?php


namespace IceCloud\RpcServer\Lib\Responses\Errors;

use IceCloud\RpcServer\Lib\Responses\ErrorResponse;

/**
 * Ошибка некорректного запроса
 *
 * @package IceCloud\RpcServer\Lib\Responses\Errors
 * @author a.kazakov
 */
class InvalidRequestResponse extends ErrorResponse
{
    public function __construct(string $version)
    {
        parent::__construct(
            null,
            $version,
            self::INVALID_REQUEST_CODE,
            "Invalid request",
            null
        );
    }
}
