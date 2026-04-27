<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->student) {
            return response()->json([
                'message' => 'Access denied. Students only.'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
