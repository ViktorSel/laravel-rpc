<?php


namespace IceCloud\RpcClient\Lib\Generation;


use IceCloud\RpcClient\Lib\RpcClient;

/**
 * Слой пространства имен. Нужен для генератора клиента.
 *
 * @package IceCloud\RpcClient\Lib\Generation
 * @author a.kazakov <a.kazakov@iceberg.ru>
 */
class NamespaceLayer
{
    private RpcClient $client;
    public function __construct(RpcClient $client)
    {
        $this->client = $client;
    }

    public function getClient(): RpcClient
    {
        return $this->client;
    }
}
