<?php


namespace IceCloud\RpcServer\Lib\Responses\Errors;


use IceCloud\RpcServer\Lib\Response;
use IceCloud\RpcServer\Lib\Responses\ErrorResponse;

/**
 * Ошибка парсинга
 * @package IceCloud\RpcServer\Lib\Responses\Errors
 *
 * @author a.kazakov
 */
class ParsingErrorResponse extends ErrorResponse
{
    public function __construct(string $version)
    {
        parent::__construct(
            null,
            $version,
            self::PARSING_ERROR_CODE,
            "Parsing error",
            null
        );
    }
}
