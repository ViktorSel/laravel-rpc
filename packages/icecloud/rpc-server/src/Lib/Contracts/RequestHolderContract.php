<?php

namespace IceCloud\RpcServer\Lib\Contracts;

use IceCloud\RpcServer\Lib\Request;

interface RequestHolderContract
{
    function getRequest(): Request;
}
