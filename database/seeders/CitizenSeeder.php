<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitizenSeeder extends Seeder
{
    public function run(): void
    {
        $religions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];

        for ($i = 0; $i < 30; $i++) {
            DB::table('citizens')->insert([
                'record_type' => 'PENDUDUK',
                'record_event' => 'Lahir01',
                'national_id' => fake()->unique()->numerify('################'), // 16 digit
                'family_card_number' => fake()->numerify('################'),
                'dusun' => 'Dusun ' . (($i % 8) + 1),
                'full_name' => fake('id_ID')->name(),
                'birth_place' => fake('id_ID')->city(),
                'birth_date' => fake()->date('Y-m-d', '2005-01-01'),
                'age' => fake()->numberBetween(1, 80),
                'gender' => fake()->randomElement(['Laki-laki', 'Perempuan']),
                'address' => fake('id_ID')->address(),
                'rt' => str_pad((string) (($i % 10) + 1), 3, '0', STR_PAD_LEFT),
                'rw' => str_pad((string) (($i % 5) + 1), 3, '0', STR_PAD_LEFT),
                'phone_number' => '08' . fake()->numerify('##########'),
                'birth_certificate_status' => fake()->randomElement(['Ada', 'Belum Ada']),
                'birth_certificate_number' => fake()->numerify('################'),
                'blood_type' => fake()->randomElement(['A', 'B', 'AB', 'O']),
                'occupation' => fake()->randomElement(['Wiraswasta', 'Guru', 'Petani', 'Perawat', 'Karyawan Swasta', 'Ibu Rumah Tangga', 'Wirausaha', 'Mahasiswa', 'Nelayan', 'Pedagang']),
                'education' => fake()->randomElement(['SD', 'SMP', 'SMA/SMK', 'D3', 'S1']),
                'marital_status' => fake()->randomElement(['Menikah', 'Belum Menikah', 'Cerai Hidup', 'Cerai Mati']),
                'marriage_certificate_status' => fake()->randomElement(['Ada', 'Belum Ada']),
                'marriage_certificate_number' => fake()->numerify('################'),
                'marriage_date' => fake()->date('Y-m-d', '2024-12-31'),
                'divorce_certificate_status' => fake()->randomElement(['Ada', 'Belum Ada']),
                'divorce_certificate_number' => fake()->numerify('################'),
                'divorce_date' => fake()->date('Y-m-d', '2024-12-31'),
                'family_relationship' => fake()->randomElement(['Kepala Keluarga', 'Istri', 'Anak']),
                'physical_disability' => fake()->randomElement(['Tidak Ada', 'Tidak Ada', 'Gangguan penglihatan', 'Gangguan pendengaran']),
                'disability_status' => fake()->randomElement(['Tidak Ada', 'Tidak Ada', 'Ringan', 'Sedang']),
                'religion' => $religions[$i % count($religions)],
                'mother_national_id' => fake()->unique()->numerify('################'),
                'mother_name' => fake('id_ID')->name('female'),
                'father_national_id' => fake()->unique()->numerify('################'),
                'father_name' => fake('id_ID')->name('male'),
                'nationality' => 'WNI',
                'ktp_address' => fake('id_ID')->address(),
                'status' => $i < count($religions)
                    ? 'Active'
                    : fake()->randomElement(['Active', 'Active', 'Active', 'Pindah']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
