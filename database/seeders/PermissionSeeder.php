<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'User Management',
                'slug' => 'user-management',
                'description' => 'Mengelola pengguna sistem',
            ],
            [
                'name' => 'Profil Kalurahan',
                'slug' => 'profil-kalurahan',
                'description' => 'Mengelola informasi profil kalurahan.',
            ],
            [
                'name' => 'Berita & Pengumuman',
                'slug' => 'berita',
                'description' => 'Mengelola berita dan pengumuman desa.',
            ],
            [
                'name' => 'Layanan Desa',
                'slug' => 'layanan-desa',
                'description' => 'Mengelola layanan desa.',
            ],
            [
                'name' => 'Potensi Kalurahan',
                'slug' => 'potensi-kalurahan',
                'description' => 'Mengelola data potensi kalurahan.',
            ],
            [
                'name' => 'Agenda',
                'slug' => 'agenda',
                'description' => 'Mengelola agenda dan kegiatan desa.',
            ],
            [
                'name' => 'Gallery',
                'slug' => 'gallery',
                'description' => 'Mengelola gallery desa.',
            ],
            [
                'name' => 'Pengaduan',
                'slug' => 'pengaduan',
                'description' => 'Mengelola pengaduan masyarakat.',
            ],
            [
                'name' => 'Perpustakaan',
                'slug' => 'perpustakaan',
                'description' => 'Mengelola data perpustakaan.',
            ],
            [
                'name' => 'Pengelolaan Surat',
                'slug' => 'pengelolaan-surat',
                'description' => 'Mengelola pengajuan surat dan proses persuratan.',
            ],
            [
                'name' => 'Verifikasi Surat',
                'slug' => 'verifikasi-surat',
                'description' => 'Memverifikasi pengajuan surat.',
            ],
            [
                'name' => 'Otorisasi Surat',
                'slug' => 'otorisasi-surat',
                'description' => 'Memberikan otorisasi pada pengajuan surat.',
            ],
            [
                'name' => 'Tipe Surat',
                'slug' => 'tipe-surat',
                'description' => 'Mengelola tipe dan dokumen surat.',
            ],
            [
                'name' => 'Sejarah',
                'slug' => 'sejarah',
                'description' => 'Mengelola sejarah kalurahan.',
            ],
            [
                'name' => 'Visi & Misi',
                'slug' => 'visi-misi',
                'description' => 'Mengelola visi & misi kalurahan.',
            ],
            [
                'name' => 'Struktur Organisasi',
                'slug' => 'struktur-organisasi',
                'description' => 'Mengelola struktur organisasi kalurahan.',
            ],
            [
                'name' => 'Data Wilayah',
                'slug' => 'data-wilayah',
                'description' => 'Mengelola data wilayah kalurahan.',
            ],
        ];

        // Dashboard and any permission removed from this code-defined list are
        // not configurable modules and must not remain in the database.
        $definedSlugs = array_column($permissions, 'slug');
        $stalePermissionIds = DB::table('permissions')
            ->whereNotIn('slug', $definedSlugs)
            ->pluck('permission_id');

        if ($stalePermissionIds->isNotEmpty()) {
            DB::table('role_permissions')
                ->whereIn('permission_id', $stalePermissionIds)
                ->delete();
            DB::table('permissions')
                ->whereIn('permission_id', $stalePermissionIds)
                ->delete();
        }

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $permission['slug']],
                [
                    'name' => $permission['name'],
                    'description' => $permission['description'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
