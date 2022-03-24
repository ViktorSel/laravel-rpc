<?php


namespace IceCloud\RpcServer\Lib\Responses;


use IceCloud\RpcServer\Lib\Response;

/**
 * Ответ сервера в состоянии ошибки
 *
 * @author a.kazakov
 */
class ErrorResponse extends Response
{
    public function __construct(?int $id, string $version, int $code, string $message, ?array $data = null)
    {
        $this->setError(
            $code,
            $message,
            $data
        );
        parent::__construct($id, $version);
    }
}
