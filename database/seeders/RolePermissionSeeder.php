<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Ambil Role
        |--------------------------------------------------------------------------
        */

        $superadmin = DB::table('roles')
            ->where('name', 'Superadmin')
            ->value('role_id');

        $admin = DB::table('roles')
            ->where('name', 'Admin')
            ->value('role_id');

        $operator = DB::table('roles')
            ->where('name', 'Operator')
            ->value('role_id');
        /*
        |--------------------------------------------------------------------------
        | Ambil Permission
        |--------------------------------------------------------------------------
        */

        $permissions = DB::table('permissions')
            ->pluck('permission_id', 'slug');


        /*
        |--------------------------------------------------------------------------
        | Permission Superadmin
        | Semua permission
        |--------------------------------------------------------------------------
        */

        $superadminPermissions = $permissions->map(function ($permissionId) use ($superadmin) {
            return [
                'role_id' => $superadmin,
                'permission_id' => $permissionId,
            ];
        })->values()->toArray();


        /*
        |--------------------------------------------------------------------------
        | Permission Admin
        | Semua kecuali User Management
        |--------------------------------------------------------------------------
        */
        $adminSlugs = [
            'dashboard',
            'user-management',
            'profil-kalurahan',
            'berita',
            'layanan-desa',
            'potensi-kalurahan',
            'agenda',
            'gallery',
            'pengaduan',
            'perpustakaan',
            'surat',
        ];

        $adminPermissions = [];

        foreach ($adminSlugs as $slug) {
            if (isset($permissions[$slug])) {
                $adminPermissions[] = [
                    'role_id' => $admin,
                    'permission_id' => $permissions[$slug],
                ];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Permission Operator
        | Dashboard + fitur operasional
        |--------------------------------------------------------------------------
        */
        $operatorSlugs = [
            'dashboard',
            'pengaduan',
            'perpustakaan',
            'surat',
            'profil-kalurahan',
            'potensi-kalurahan',
        ];

        $operatorPermissions = [];

        foreach ($operatorSlugs as $slug) {
            if (isset($permissions[$slug])) {
                $operatorPermissions[] = [
                    'role_id' => $operator,
                    'permission_id' => $permissions[$slug],
                ];
            }
        }
                /*
        |--------------------------------------------------------------------------
        | Simpan ke role_permissions
        |--------------------------------------------------------------------------
        */

        DB::table('role_permissions')->insertOrIgnore(
            array_merge(
                $superadminPermissions,
                $adminPermissions,
                $operatorPermissions
            )
        );
    }
}
