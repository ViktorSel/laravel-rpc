<?php


namespace IceCloud\RpcServer\Lib\Npm\Models;


class PackageMeta
{
    private $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getLatestVersion() {
        return $this->data['dist-tags']['latest'];
    }
}
