<?php


namespace IceCloud\RpcServer\Lib\Contracts\Generator;


interface CompileFile
{
    /**
     * Собрать файл
     * @param string $outputFolder Папка назначения
     * @return mixed
     */
    public function compile(string $outputFolder);

    /**
     * Имя файла
     * @return string
     */
    public function getFilename() : string;
}
