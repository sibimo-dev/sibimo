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
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'description' => 'Akses ke dashboard sistem.',
            ],
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
                'slug' => 'surat',
                'description' => 'Mengelola pengajuan dan penerbitan surat.',
            ],
        ];

        foreach ($permissions as $permission)
            DB::table('permissions')->insert([
                ...$permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    }
}
