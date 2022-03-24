<?php
    use IceCloud\RpcServer\Lib\Generator\Compiler\Js\JsClassFile;

    /** @var JsClassFile $file */
?>

<?php foreach ($file->getUses() as $className => $use): ?>
import { {{$className}} } from "{{$file->import($use)}}"
<?php endforeach; ?>
