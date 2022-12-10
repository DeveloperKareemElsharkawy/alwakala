<?php

namespace App\Http\Middleware;

use App\Enums\UserTypes\UserType;
use Closure;

class OpenApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $auth = $request->header('Authorization');
        if (isset($auth) && !is_null($auth)) {
            $user = auth('api')->user();
            $requestData = ['guest' => true];
            if ($user) {
                $requestData['guest'] = false;
                $requestData = ['user_id' => $user->id, 'type_id' => $user->type_id, 'activation' => $user->activation];
            }
            $request->merge($requestData);
        }
        return $next($request);
    }
}
