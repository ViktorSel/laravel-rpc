<?php
    use IceCloud\RpcServer\Lib\Generator\JsGenerator;
    use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsFile;

    /**
     * @var JsFile $file
     * @var JsGenerator $generator
     */
?>
{
    "name": "{{$generator->getPackageName()}}",
    "version": "{{$generator->getVersionStorage()->getVersion()}}",
    "description": "{{$generator->getDescription()}}",
    "type" : "module",
    "scripts": {
        "test": "echo \"Error: no test specified\" && exit 1"
    },
    "dependencies": {
        "ice-lib-rpc-common": "latest",
        "ice-lib-data-routine": "latest"
    },
    "keywords": [
        "rpc",
        "client",
        "{{$generator->getProjectSlug()}}" @if($generator->getScopeSlug()),
        "{{$generator->getScopeSlug()}}"
        @endif
    ],
    "author": "php-generator",
    "license": "proprietary"
}
