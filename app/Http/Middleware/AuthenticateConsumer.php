<?php

namespace App\Http\Middleware;

use App\Enums\UserTypes\UserType;
use Closure;

class AuthenticateConsumer
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $auth = $request->header('Authorization');
        if (isset($auth) && !is_null($auth)) {
            $user = auth('api')->user();
            if (is_null($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error Token'
                ], 401);
            }
            if (!is_null($user) && $user->type_id != UserType::CONSUMER) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not consumer'
                ], 401);
            }
            $request->request->add(['user_id' => $user->id, 'type_id' => $user->type_id, 'activation' => $user->activation]);

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Error Token'
            ], 401);
        }
        return $next($request);
    }
}
