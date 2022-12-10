<?php
namespace App\Lib\Log;
use Illuminate\Support\Facades\Log;

/**
 * Created by PhpStorm.
 * User: anoos
 * Date: 15/04/18
 * Time: 02:10 م
 */

class ServerError implements ErrorHandler
{
    public static function handle($e) {
        $error = [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'error' => $e->getMessage(),
        ];
        Log::info(json_encode($error));
        return response()->json(array('success' => false,
            'message' => trans('validation.500'),
            'details' => $error
        ), 500);
    }
}
