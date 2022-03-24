<?php


namespace IceCloud\RpcClient\Lib;


use Illuminate\Contracts\Support\Arrayable;

class Arguments implements Arrayable
{
    private $data = [];
    public function __construct(array $data)
    {
        $this->data = $data;
    }


    public function toArray(): array
    {
        return $this->data;
    }
}
