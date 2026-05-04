<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
{
  $user = auth()->user();
  
if (!$user) {
    abort(403);
}

$user->load('roles');

    if (!$user) {
        abort(403);
    }

   if (!$user || !$user->roles->pluck('name')->intersect($roles)->count()) {
    abort(403);
}

    return $next($request);
}
}
