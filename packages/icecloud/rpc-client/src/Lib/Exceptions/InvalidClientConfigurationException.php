<?php


namespace IceCloud\RpcClient\Lib\Exceptions;


use Throwable;

class InvalidClientConfigurationException extends RpcClientException
{
    public function __construct()
    {
        parent::__construct("Некорректная конфигурация клиента. Проверьте конфиги rpc-client-* или сбросьте кеш конфигурации.");
    }
}
