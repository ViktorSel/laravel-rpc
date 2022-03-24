<?php

namespace App\Http\Controllers;

class TestController extends Controller
{
    public function test(array $params = [])
    {
        $data = "123";
        return $data;
    }
}