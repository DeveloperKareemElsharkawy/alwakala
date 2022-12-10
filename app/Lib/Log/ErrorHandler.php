<?php
/**
 * Created by PhpStorm.
 * User: anoos
 * Date: 15/04/18
 * Time: 02:11 م
 */
namespace App\Lib\Log;

interface ErrorHandler
{
    public static function handle($errorCarrier);
}