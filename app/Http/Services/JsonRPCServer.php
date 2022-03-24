<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use App\Http\Response\JsonRPCResponse;

class JsonRPCServer
{
    public function handle(Request $request, $controller)
    {
        try {
            $content = json_decode($request->getContent(), true);
            if (empty($content)) {
                throw new JsonRPCException('Parse Error', JsonRPCException::PARSE_ERROR);
            }
            $result = $controller->{$content['method']}(...[$content['params']]);
            return JsonRPCResponse::success($result,$content['id']);
        } catch (\Exception $e) {
            return JsonRPCResponse::error($e->getMessage(), $content['id']??null);
        }
    }
}