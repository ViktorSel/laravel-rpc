<?php


namespace IceCloud\RpcServer\Lib\Contracts\Generator;


interface CompileClassFile
{
    /**
     * Имя класса (короткое)
     * @return string
     */
    public function getClassName() : string;

    /**
     * Пространство имен
     * @return string[]
     */
    public function getNamespace() : array;
}
