<?php

namespace App\Http\Middleware;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use Closure;

class CheckUserActivation
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
        if (!$request->activation) {
            return response()->json([
                'status' => false,
                'message' => 'Activate your account'
            ], AResponseStatusCode::FORBIDDEN);
        }
        return $next($request);
    }
}
