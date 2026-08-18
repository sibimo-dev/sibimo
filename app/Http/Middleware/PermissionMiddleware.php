<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class PermissionMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string $permission
    ): Response {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil Role User
        |--------------------------------------------------------------------------
        */

        $roleId = DB::table('users')
            ->where('user_id', $user->user_id)
            ->value('role_id');

        /*
        |--------------------------------------------------------------------------
        | Cek Permission dari Role
        |--------------------------------------------------------------------------
        */

        $hasRolePermission = false;

        if ($roleId) {
            $hasRolePermission = DB::table('role_permissions')
                ->join(
                    'permissions',
                    'permissions.permission_id',
                    '=',
                    'role_permissions.permission_id'
                )
                ->where('role_permissions.role_id', $roleId)
                ->where('permissions.slug', $permission)
                ->exists();
        }

        /*
        |--------------------------------------------------------------------------
        | Cek Permission Khusus User
        |--------------------------------------------------------------------------
        */

        $hasUserPermission = DB::table('user_permissions')
            ->join(
                'permissions',
                'permissions.permission_id',
                '=',
                'user_permissions.permission_id'
            )
            ->where('user_permissions.user_id', $user->user_id)
            ->where('permissions.slug', $permission)
            ->exists();

        /*
        |--------------------------------------------------------------------------
        | User memiliki permission?
        |--------------------------------------------------------------------------
        */

        if (!$hasRolePermission && !$hasUserPermission) {
            return response()->json([
                'message' => 'Forbidden. Anda tidak memiliki permission untuk mengakses resource ini.',
            ], 403);
        }

        return $next($request);
    }
}