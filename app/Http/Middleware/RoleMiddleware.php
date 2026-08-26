<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $roleName = $user->roleRelation?->name;

        if (!$roleName || !in_array($roleName, $roles, true)) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk mengakses fitur ini.',
            ], 403);
        }

        return $next($request);
    }
}
