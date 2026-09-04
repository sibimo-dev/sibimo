<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $staff = [
            [
                'name' => 'Bambang Bimomartani',
                'position' => 'Lurah',
                'level' => 'Lurah',
                'description' => 'Pimpinan Kalurahan Bimomartani.',
            ],
            [
                'name' => 'Siti Rahmawati',
                'position' => 'Carik',
                'level' => 'Carik',
                'description' => 'Sekretaris Kalurahan.',
            ],
            [
                'name' => 'Agus Setiawan',
                'position' => 'Kaur Danarta',
                'level' => 'Kepala Urusan (Sekretariat & Keuangan)',
                'description' => 'Mengelola urusan keuangan kalurahan.',
            ],
            [
                'name' => 'Dewi Lestari',
                'position' => 'Kaur Tata Laksana',
                'level' => 'Kepala Urusan (Sekretariat & Keuangan)',
                'description' => 'Mengelola tata laksana pemerintahan.',
            ],
            [
                'name' => 'Rudi Hartono',
                'position' => 'Kaur Pangripta',
                'level' => 'Kepala Urusan (Sekretariat & Keuangan)',
                'description' => 'Mengelola perencanaan pembangunan.',
            ],
            [
                'name' => 'Nina Kartika',
                'position' => 'Kamituwa',
                'level' => 'Kepala Seksi',
                'description' => 'Mengelola urusan kesejahteraan masyarakat.',
            ],
            [
                'name' => 'Joko Santoso',
                'position' => 'Ulu-Ulu',
                'level' => 'Kepala Seksi',
                'description' => 'Mengelola urusan pelayanan umum.',
            ],
            [
                'name' => 'Wahyu Pratama',
                'position' => 'Jagabaya',
                'level' => 'Kepala Seksi',
                'description' => 'Mengelola urusan pemerintahan dan keamanan.',
            ],
        ];

        foreach ($staff as $person) {
            Staff::updateOrCreate(
                [
                    'name' => $person['name'],
                    'position' => $person['position'],
                    'is_signer' => false,
                ],
                [
                    ...$person,
                    'is_signer' => false,
                ],
            );
        }
    }
}
