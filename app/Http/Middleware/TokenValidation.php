<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            // Token is valid
            return $next($request);
        }

        abort(401, 'Unauthenticated');
    }
}
