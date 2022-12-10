<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\Seller;
use Closure;

class AuthenticateAdmin
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
            $seller = Admin::query()->where('user_id', $user->id)->first();
            if (is_null($seller)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error Token'
                ], 401);
            }
            $request->request->add(['user_id' => $user->id, 'user' => $user]);

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Error Token'
            ], 401);
        }

        return $next($request);
    }
}
