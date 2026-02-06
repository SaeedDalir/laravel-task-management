<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        $allowed = $roles;
        if (count($roles) === 1 && str_contains($roles[0], '|')) {
            $allowed = explode('|', $roles[0]);
        }

        if (! $user->hasRole($allowed)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
