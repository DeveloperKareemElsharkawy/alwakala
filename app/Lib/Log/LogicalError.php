<?php
/**
 * Created by PhpStorm.
 * User: MSoliman
 * Date: 4/17/2018
 * Time: 3:17 PM
 */

namespace App\Lib\Log;


class LogicalError implements ErrorHandler
{
    public static function handle($message)
    {
        return Response()->json([
            'message' => trans('validation.invalidFields'),
            'details' => trans($message)
        ], 403);
    }
}