@php
use IceCloud\RpcServer\Lib\Generator\JsGenerator;
use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsClassFile;

/**
 * @var JsGenerator $generator
 * @var JsClassFile $file
 * @var JsClassFile $client
 * @var array $procedures
 */
@endphp

import RpcRequestPromise from "ice-lib-rpc-common/src/RpcRequestPromise.mjs";
@include('rpc-server::clients.frontend.class.uses', ['file' => $file])

/**
 * Методы клиента
 * @param { {{$client->getClassName()}} } client
 */
export function {{$file->getClassName()}}(client) {
    return {
@include('rpc-server::clients.frontend.procedure.tree', ['tree' => $procedures])
    };
}
