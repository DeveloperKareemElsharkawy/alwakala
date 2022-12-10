<?php

namespace App\Http\Middleware;

use Closure;

class Localization
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
        // Check header request and determine localization
        $local = ($request->hasHeader('X-localization') && in_array($request->hasHeader('X-localization'), ['ar','en'])) ? $request->header('X-localization') : 'ar';
        // set laravel localization
        app()->setLocale($local);
        // continue request
        return $next($request);
    }
}
