<?php

namespace App\Http\Controllers;

use App\Rpc\App\AppRpcServer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RpcController
{
    public function handle(Request $request, AppRpcServer $server)
    {
        return new JsonResponse(
            $server->handle($request)
        );
    }
}