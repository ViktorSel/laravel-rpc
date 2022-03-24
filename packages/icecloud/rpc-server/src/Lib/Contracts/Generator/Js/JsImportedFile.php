<?php


namespace IceCloud\RpcServer\Lib\Contracts\Generator\Js;


use IceCloud\RpcServer\Lib\Generator\Compiler\File;

interface JsImportedFile
{
    /**
     * Сформировать путь для импортируемого файла относительно текущего
     *
     * @param File $file
     * @return string
     */
    public function import(File $file) : string;
}
