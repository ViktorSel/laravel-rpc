<?php
    use IceCloud\RpcServer\Lib\Generator\JsGenerator;
    use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsClassFile;

    /**
     * @var JsGenerator $generator
     * @var JsClassFile $file
     * @var JsClassFile $methods
     */
?>
import RpcCommonClient from "ice-lib-rpc-common/src/RpcCommonClient.mjs";

@include('rpc-server::clients.frontend.class.uses', ['class' => $file])

let endpoint;

class {{$file->getClassName()}} extends RpcCommonClient {
    /**
    * Первоначальная настройка клиента
    * @public
    * @param {String} host
    */
    static setup(host) {
        endpoint = host;
    }

    /**
    * @inheritDoc
    * @constructor
    */
    constructor() {
        super(endpoint, '{{$generator->getServer()->getProtocolVersion()}}');
        this.Api = {{$methods->getClassName()}}(this);
    }
}

export default {{$file->getClassName()}};
