<?php

namespace Database\Seeders;

use App\Models\Development;
use App\Models\DevelopmentProgress;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'slug' => 'rehabilitasi-jalan-lingkungan-bimomartani',
                'name' => 'Rehabilitasi Jalan Lingkungan Bimomartani',
                'address' => 'Padukuhan Krajan, Kalurahan Bimomartani',
                'category' => 'infrastruktur',
                'status' => 'selesai',
                'funding_source' => 'Dana Desa Tahun Anggaran 2026',
                'budget' => 185000000,
                'volume' => 850,
                'volume_unit' => 'meter',
                'executor' => 'Pemerintah Kalurahan Bimomartani',
                'year' => 2026,
                'start_date' => '2026-02-10',
                'target_date' => '2026-05-30',
                'description' => 'Rehabilitasi jalan lingkungan untuk meningkatkan akses warga menuju fasilitas umum dan area pertanian.',
                'latitude' => -7.7389000,
                'longitude' => 110.4657000,
            ],
            [
                'slug' => 'peningkatan-posyandu-bimomartani',
                'name' => 'Peningkatan Sarana Posyandu Bimomartani',
                'address' => 'Padukuhan Ngemplak, Kalurahan Bimomartani',
                'category' => 'kesehatan',
                'status' => 'sedang-berjalan',
                'funding_source' => 'APBKal Bimomartani Tahun Anggaran 2026',
                'budget' => 76000000,
                'volume' => 1,
                'volume_unit' => 'paket',
                'executor' => 'Tim Pelaksana Kegiatan Kalurahan',
                'year' => 2026,
                'start_date' => '2026-07-01',
                'target_date' => '2026-10-31',
                'description' => 'Peningkatan sarana posyandu untuk mendukung pelayanan kesehatan ibu dan anak di tingkat kalurahan.',
                'latitude' => -7.7396000,
                'longitude' => 110.4671000,
            ],
            [
                'slug' => 'pengelolaan-sampah-terpadu-bimomartani',
                'name' => 'Pengelolaan Sampah Terpadu Bimomartani',
                'address' => 'Kalurahan Bimomartani, Kapanewon Ngemplak',
                'category' => 'lingkungan',
                'status' => 'perencanaan',
                'funding_source' => 'Dana Keistimewaan dan Swadaya Masyarakat',
                'budget' => 120000000,
                'volume' => 1,
                'volume_unit' => 'paket',
                'executor' => 'Pemerintah Kalurahan bersama Kelompok Masyarakat',
                'year' => 2026,
                'start_date' => '2026-11-01',
                'target_date' => '2026-12-20',
                'description' => 'Perencanaan pengelolaan sampah terpadu berbasis masyarakat untuk mengurangi sampah rumah tangga dan meningkatkan kebersihan lingkungan.',
                'latitude' => -7.7402000,
                'longitude' => 110.4664000,
            ],
        ];

        foreach ($items as $item) {
            $development = Development::query()->updateOrCreate(
                ['slug' => $item['slug']],
                $item,
            );

            foreach ([0, 50, 100] as $percentage) {
                DevelopmentProgress::query()->firstOrCreate(
                    ['development_id' => $development->development_id, 'percentage' => $percentage],
                );
            }
        }
    }
}
