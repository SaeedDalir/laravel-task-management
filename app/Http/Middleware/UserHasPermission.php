<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserHasPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        $allowed = $permissions;
        if (count($permissions) === 1 && str_contains($permissions[0], '|')) {
            $allowed = explode('|', $permissions[0]);
        }

        if (! $user->hasPermission($allowed)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
