<?php
/**
 * Created by PhpStorm.
 * User: anoos
 * Date: 15/04/18
 * Time: 02:16 م
 */

namespace App\Lib\Log;



class ValidationError
{
    public static function handle($validator) {

        return Response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
            'details' => $validator->errors()
        ], 400);

    }
}