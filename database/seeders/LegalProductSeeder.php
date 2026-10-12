<?php

namespace Database\Seeders;

use App\Models\LegalProduct;
use Illuminate\Database\Seeder;

class LegalProductSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title' => 'Peraturan Kalurahan Bimomartani tentang APBKal Tahun Anggaran 2026',
                'category' => 'perkal',
                'status' => 'berlaku',
                'number' => 'Perkal Bimomartani Nomor 01 Tahun 2026',
                'year' => 2026,
                'description' => 'Peraturan mengenai Anggaran Pendapatan dan Belanja Kalurahan Bimomartani Tahun Anggaran 2026.',
            ],
            [
                'title' => 'Peraturan Kalurahan Bimomartani tentang Pengelolaan Sampah',
                'category' => 'perkal',
                'status' => 'berlaku',
                'number' => 'Perkal Bimomartani Nomor 02 Tahun 2025',
                'year' => 2025,
                'description' => 'Peraturan tentang pengurangan, pemilahan, dan pengelolaan sampah berbasis masyarakat.',
            ],
            [
                'title' => 'Keputusan Lurah Bimomartani tentang Pembentukan Tim Pelaksana Kegiatan',
                'category' => 'sk-lurah',
                'status' => 'berlaku',
                'number' => 'SK Lurah Bimomartani Nomor 05 Tahun 2026',
                'year' => 2026,
                'description' => 'Keputusan pembentukan tim pelaksana kegiatan pembangunan Kalurahan Bimomartani.',
            ],
        ];

        foreach ($items as $item) {
            LegalProduct::query()->updateOrCreate(
                ['number' => $item['number']],
                $item,
            );
        }
    }
}
