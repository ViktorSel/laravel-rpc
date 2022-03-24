<?php
    use IceCloud\RpcServer\Lib\Generator\JsGenerator;
    use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsFile;

    /**
     * @var JsFile $file
     * @var JsGenerator $generator
     */
?>
registry = "{{$generator->getNpmRegistry()->getRegistryHost()}}/"
