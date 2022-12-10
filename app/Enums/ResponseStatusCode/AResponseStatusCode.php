<?php
namespace App\Enums\ResponseStatusCode;
/**
 * Created by PhpStorm.
 * User: anoos
 * Date: 20/10/18
 * Time: 11:15 م
 */

class AResponseStatusCode
{
    const SUCCESS = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const NO_CONTENT = 204;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const INTERNAL_SERVER_ERROR = 500;
    const NOT_FOUNT = 404;
    const FORBIDDEN = 403;
}
