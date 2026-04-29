<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
   /* protected function redirectTo($request): ?string
    {
        // API esetén NINCS redirect login oldalra
        if ($request->expectsJson()) {
            return null;
        }

        return null; // vagy hagyhatod így teljesen API projectnél
    }*/
        protected function redirectTo($request): ?string
{
    return null;
}
}
