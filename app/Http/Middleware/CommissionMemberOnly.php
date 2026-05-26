<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CommissionMemberOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user->commissionMembers()->exists()) {
            return response()->json([
                'message' => 'Commission members only'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
