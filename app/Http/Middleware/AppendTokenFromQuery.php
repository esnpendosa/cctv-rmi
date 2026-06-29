<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class AppendTokenFromQuery
 * 
 * Appends token query parameter as Bearer authorization header if not already present.
 * Useful for media streams loaded by native player components.
 * 
 * @package App\Http\Middleware
 */
class AppendTokenFromQuery
{
    /**
     * Handle an incoming request.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('token') && !$request->headers->has('Authorization')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->query('token'));
        }

        return $next($request);
    }
}
