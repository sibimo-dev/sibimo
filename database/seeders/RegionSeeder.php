<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regionNames = [
            'BALONG',
            'BANJARHARJO',
            'COKROGATEN',
            'KALIBULUS',
            'KOROULON KIDUL',
            'KOROULON LOR',
            'KRAGILAN',
            'KREBET',
            'MACANAN',
            'PONDOK SURUH',
            'PURWOBINANGUN',
            'ROGOBANGSAN',
            'SORASAN',
        ];

        foreach ($regionNames as $name) {
            Region::firstOrCreate(
                ['name' => $name],
                [
                    'head_name' => null,
                    'rw_count' => 0,
                    'rt_count' => 0,
                    'kk_count' => 0,
                    'population' => 0,
                    'male_count' => 0,
                    'female_count' => 0,
                ],
            );
        }
    }
}
