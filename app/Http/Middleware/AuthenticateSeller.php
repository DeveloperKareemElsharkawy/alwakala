<?php

namespace App\Http\Middleware;

use App\Enums\UserTypes\UserType;
use App\Models\Seller;
use App\Models\Store;
use Closure;
use Illuminate\Http\Request;

class AuthenticateSeller
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
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
            if (!is_null($user) && $user->type_id != UserType::SELLER) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not Seller'
                ], 401);
            }
            $seller = Seller::query()->where('user_id', $user->id)->first();
            $store = Store::query()->where('id', $seller->store_id)->first();
            $request->request->add(
                [
                    'user_id' => $store->user_id,
                    'seller_id' => $user->id,
                    'type_id' => $user->type_id,
                    'activation' => $user->activation
                ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Error Token'
            ], 401);
        }

        return $next($request);
    }
}
