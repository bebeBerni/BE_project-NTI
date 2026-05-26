<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenValidation
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Unauthenticated'
        ], Response::HTTP_UNAUTHORIZED);
    }
}
