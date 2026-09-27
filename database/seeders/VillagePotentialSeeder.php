<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VillagePotentialSeeder extends Seeder
{
    public function run(): void
    {
        $potentials = [
            ['UMKM', 'potential-umkm.jpg'],
            ['Agriculture', 'potential-agriculture.jpeg'],
            ['Tourism', 'potential-tourism.jpg'],
            ['BUMDes', 'potential-bumdes.jpeg'],
        ];

        for ($i = 0; $i < 8; $i++) {
            [$category, $image] = $potentials[$i % count($potentials)];
            $slug = 'seed-potential-' . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);

            DB::table('village_potentials')->updateOrInsert(
                ['slug' => $slug],
                [
                    'category' => $category,
                    'title' => 'Potensi Kalurahan ' . ($i + 1),
                    'description' => 'Potensi unggulan Kalurahan Bimomartani untuk mendukung pemberdayaan masyarakat.',
                    'image' => Storage::disk('public')->url('village-potentials/' . $image),
                    'location' => 'Kalurahan Bimomartani',
                    'short_desc' => 'Potensi unggulan Kalurahan Bimomartani.',
                    'contact' => 'Kalurahan Bimomartani',
                    'extra_info' => json_encode(['seeded' => true, 'index' => $i + 1]),
                    'created_at' => now(),
                ],
            );
        }
    }
}
