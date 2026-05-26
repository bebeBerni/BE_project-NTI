<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user->load('roles');

        if (!$user->roles->pluck('name')->intersect($roles)->count()) {
            return response()->json([
                'message' => 'Forbidden - insufficient permissions'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
