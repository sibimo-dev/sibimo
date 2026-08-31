<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 6; $i++) {
            DB::table('service')->insert([
                'title' => fake('id_ID')->sentence(3),
                'description' => fake('id_ID')->paragraph(2),
                'icon' => 'icons/service-' . ($i + 1) . '.svg',
                'sort_order' => $i,
                'is_active' => 1,
                'created_at' => now(),
            ]);
        }
    }
}