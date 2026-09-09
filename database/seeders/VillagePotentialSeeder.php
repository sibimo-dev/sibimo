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

            DB::table('village_potentials')->insert([
                'category' => $category,
                'title' => fake('id_ID')->sentence(4),
                'description' => fake('id_ID')->paragraphs(3, true),
                'image' => Storage::disk('public')->url('village-potentials/' . $image),
                'location' => fake('id_ID')->address(),
                'created_at' => now(),
            ]);
        }
    }
}
