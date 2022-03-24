<?php

namespace App\Http\Response;

class JsonRPCResponse
{
    const JSON_RPC_VERSION = '2.0';

    public static function success($result, string $id = null)
    {
        return [
            'jsonrpc'   => self::JSON_RPC_VERSION,
            'result'    => $result,
            'id'        => $id,
        ];
    }

    public static function error($error, string $id = null)
    {
        return [
            'jsonrpc'   => self::JSON_RPC_VERSION,
            'error'     => $error,
            'id'        => $id,
        ];
    }
}