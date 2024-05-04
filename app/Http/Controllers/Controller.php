<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function response($status, $data = null, $message = null, $log = null, $httpStatusCode = 200)
    {
        return response()->json([
            "status" => $status,
            "data" => $data,
            "message" => $message,
            "log" => $log,
        ], $httpStatusCode, [], JSON_UNESCAPED_UNICODE);
    }
}
