<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 6; $i++) {
            DB::table('service')->updateOrInsert(
                ['icon' => 'icons/service-' . ($i + 1) . '.svg'],
                [
                    'title' => 'Layanan Desa ' . ($i + 1),
                    'description' => 'Informasi layanan administrasi Kalurahan Bimomartani.',
                    'sort_order' => $i,
                    'is_active' => 1,
                    'created_at' => now(),
                ],
            );
        }
    }
}
