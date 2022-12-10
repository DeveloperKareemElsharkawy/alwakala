<?php


namespace App\Lib\Log;


class ViolationError implements ErrorHandler
{
    public static function handle($message)
    {
        return Response()->json([
            'success' => false,
            'message' => $message
        ], 403);
    }
}