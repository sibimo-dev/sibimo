<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VillagePotentialSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 8; $i++) {
            DB::table('village_potentials')->insert([
                'category' => fake()->randomElement(['UMKM', 'Agriculture', 'Tourism', 'BUMDes']),
                'title' => fake('id_ID')->sentence(4),
                'description' => fake('id_ID')->paragraphs(3, true),
                'image' => 'potentials/' . fake()->uuid() . '.jpg',
                'location' => fake('id_ID')->address(),
                'created_at' => now(),
            ]);
        }
    }
}