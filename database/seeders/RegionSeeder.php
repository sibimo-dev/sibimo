<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Citizen;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('id_ID');
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
            'ROGOBANGSAN',
            'SORASAN',
        ];

        $citizens = Citizen::query()
            ->where('status', 'Active')
            ->get(['family_card_number', 'gender']);
        $citizensByRegion = array_fill(0, count($regionNames), []);

        foreach ($citizens->values() as $index => $citizen) {
            $citizensByRegion[$index % count($regionNames)][] = $citizen;
        }

        foreach ($regionNames as $index => $name) {
            $regionCitizens = collect($citizensByRegion[$index]);
            $population = $regionCitizens->count();
            $maleCount = $regionCitizens->where('gender', 'Laki-laki')->count();
            $rwCount = $population > 0 ? $faker->numberBetween(1, 3) : 0;

            $region = [
                'name' => $name,
                'head_name' => $population > 0 ? $faker->name() : null,
                'rw_count' => $rwCount,
                'rt_count' => $population > 0 ? $rwCount * $faker->numberBetween(2, 4) : 0,
                'kk_count' => $regionCitizens->pluck('family_card_number')->filter()->unique()->count(),
                'population' => $population,
                'male_count' => $maleCount,
                'female_count' => $population - $maleCount,
            ];

            Region::updateOrCreate(
                ['name' => $name],
                $region,
            );
        }
    }
}
