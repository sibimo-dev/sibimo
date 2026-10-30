<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Staff;
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
            // Dusun yang sudah ada di tabel regions dipakai ulang (pencocokan nama di MySQL
            // tidak membedakan huruf besar/kecil, jadi 'Krebet' cocok dengan 'KREBET').
            $region = Region::firstOrCreate(['name' => $name]);
            $rtCounter = 0;

            for ($i = 0; $i < $rwCount; $i++) {
                $rwCounter++;
                $rwNumber = str_pad((string) $rwCounter, 2, '0', STR_PAD_LEFT);

                $rw = Rw::firstOrCreate(
                    ['region_id' => $region->region_id, 'number' => $rwNumber],
                    ['head_staff_id' => $this->dummyStaff("Ketua RW {$rwNumber} (dummy)", "Ketua RW {$rwNumber}", 'RW')->staff_id],
                );

                $rtTotal = intdiv($rtCount, $rwCount) + ($i < $rtCount % $rwCount ? 1 : 0);

                for ($j = 0; $j < $rtTotal; $j++) {
                    $rtCounter++;
                    $rtNumber = str_pad((string) $rtCounter, 2, '0', STR_PAD_LEFT);

                    // Nomor RT mulai dari 01 lagi di tiap padukuhan, jadi nama dummy memuat padukuhan.
                    Rt::firstOrCreate(
                        ['rw_id' => $rw->id, 'number' => $rtNumber],
                        ['head_staff_id' => $this->dummyStaff("Ketua RT {$rtNumber} {$name} (dummy)", "Ketua RT {$rtNumber}", 'RT')->staff_id],
                    );
                }
            }
        }
    }

    // Baris staff dummy untuk ketua RT/RW. Bila insert gagal karena ada kolom wajib
    // di tabel staff, tambahkan nilainya di array kedua.
    private function dummyStaff(string $name, string $position, string $level): Staff
    {
        return Staff::firstOrCreate(['name' => $name], ['position' => $position, 'level' => $level]);
    }
}