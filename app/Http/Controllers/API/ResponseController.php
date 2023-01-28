<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class ResponseController extends Controller
{
    public const MESSAGE = "MESSAGE";
    public const ERROR = "ERROR";
    public static function response ($status, $message, $status_code){
        return response()->json([
            'status' => $status,
            'data' => $message
        ], $status_code);
    }
}
