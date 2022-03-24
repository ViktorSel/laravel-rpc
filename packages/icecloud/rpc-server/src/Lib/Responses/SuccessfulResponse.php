<?php


namespace IceCloud\RpcServer\Lib\Responses;


use IceCloud\RpcServer\Lib\Response;

/**
 * Ответ сервера в состоянии результата
 *
 * @package IceCloud\RpcServer\Lib\Responses
 * @author a.kazakov
 */
class SuccessfulResponse extends Response
{
    public function __construct(?int $id, string $version, $result)
    {
        parent::__construct($id, $version);
        $this->setResult(
            $result
        );
    }
}
