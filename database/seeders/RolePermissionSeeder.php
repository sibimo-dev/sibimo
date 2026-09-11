<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roleIds = DB::table('roles')->pluck('role_id', 'name');
        $permissionIds = DB::table('permissions')->pluck('permission_id', 'slug');

        $allSlugs = $permissionIds->keys()->all();
        $assignments = [
            'Superadmin' => $allSlugs,
            'Admin' => $allSlugs,
            'Operator' => [
                'pengaduan',
                'perpustakaan',
                'pengelolaan-surat',
                'verifikasi-surat',
                'otorisasi-surat',
                'tipe-surat',
                'profil-kalurahan',
                'sejarah',
                'visi-misi',
                'struktur-organisasi',
                'data-wilayah',
                'potensi-kalurahan',
            ],
        ];

        foreach ($assignments as $roleName => $slugs) {
            $roleId = $roleIds->get($roleName);

            if (!$roleId) {
                continue;
            }

            $rows = collect($slugs)
                ->map(fn (string $slug) => $permissionIds->get($slug))
                ->filter()
                ->map(fn (int $permissionId) => [
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ])
                ->values()
                ->all();

            DB::table('role_permissions')->where('role_id', $roleId)->delete();

            if ($rows) {
                DB::table('role_permissions')->insert($rows);
            }
        }
    }
}
