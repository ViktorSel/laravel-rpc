<?php


namespace IceCloud\RpcServer\Lib\Contracts\Cache;


interface ExportedClassContract
{
    /**
     * @param array $an_array
     * @return $this
     */
    public static function __set_state(array $an_array): ExportedClassContract;
}
