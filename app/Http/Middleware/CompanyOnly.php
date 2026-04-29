<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->companies()->exists()) {
            return response()->json([
                'message' => 'Access denied. Company members only.'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
