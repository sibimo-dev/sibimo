<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\StaffAssignment;
use App\Models\StaffPerson;
use Illuminate\Database\Seeder;

class RegionAreaSeeder extends Seeder
{
    public function run(): void
    {
        // [nama padukuhan, jumlah RW, jumlah RT]. Nomor RW lanjut antar padukuhan,
        // nomor RT lanjut di dalam satu padukuhan.
        $seed = [
            ['Krebet', 2, 5], ['Rogobangsan', 2, 5], ['Kalibulus', 2, 4], ['Macanan', 2, 4],
            ['Cokrogaten', 3, 6], ['Koroulon Lor', 2, 5], ['Pondok Suruh', 3, 7], ['Balong', 2, 6],
            ['Banjarharjo', 3, 6], ['Kragilan', 2, 4], ['Sorasan', 2, 5], ['Koroulon Kidul', 2, 4],
        ];

        $rwCounter = 0;

        foreach ($seed as [$name, $rwCount, $rtCount]) {
            $region = Region::firstOrCreate(['name' => $name]);
            $rtCounter = 0;

            for ($i = 0; $i < $rwCount; $i++) {
                $rwCounter++;
                $rwNumber = str_pad((string) $rwCounter, 2, '0', STR_PAD_LEFT);

                $rw = Rw::firstOrCreate(
                    ['region_id' => $region->region_id, 'number' => $rwNumber],
                    ['head_name' => "Ketua RW {$rwNumber} (dummy)"],
                );
                $this->seedHead('Ketua RW', 'rw_id', $rw->id, $rw->head_name);

                $rtTotal = intdiv($rtCount, $rwCount) + ($i < $rtCount % $rwCount ? 1 : 0);

                for ($j = 0; $j < $rtTotal; $j++) {
                    $rtCounter++;
                    $rtNumber = str_pad((string) $rtCounter, 2, '0', STR_PAD_LEFT);

                    $rt = Rt::firstOrCreate(
                        ['rw_id' => $rw->id, 'number' => $rtNumber],
                        ['head_name' => "Ketua RT {$rtNumber} {$name} (dummy)"],
                    );
                    $this->seedHead('Ketua RT', 'rt_id', $rt->id, $rt->head_name);
                }
            }
        }
    }

    // Buat penugasan ketua hanya kalau wilayah itu belum punya ketua aktif,
    // supaya data asli dari admin tidak tertimpa dan seeder aman dijalankan ulang.
    private function seedHead(string $position, string $column, int $id, ?string $name): void
    {
        if (blank($name)) {
            return;
        }

        $hasActive = StaffAssignment::query()->active()
            ->where('position', $position)->where($column, $id)->exists();
        if ($hasActive) {
            return;
        }

        $person = StaffPerson::query()->create(['name' => $name]);

        StaffAssignment::query()->create([
            'staff_id' => $person->id,
            'position' => $position,
            $column => $id,
            'start_date' => today()->toDateString(),
        ]);
    }
}