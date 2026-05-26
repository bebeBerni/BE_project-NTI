<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MentorOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->mentor) {
            return response()->json([
                'message' => 'Access denied. Mentors only.'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
