<?php

namespace App\Lib\Log;

class Error implements ErrorHandler
{
    public static function handle($message)
    {
        return Response()->json([
            'success' => false,
            'message' => $message
        ], 400);
    }
}