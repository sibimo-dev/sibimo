<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitizenSeeder extends Seeder
{
    public function run(): void
    {
        $religions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];
        $rtIds = DB::table('rts')->pluck('id')->all();

        for ($i = 0; $i < 30; $i++) {
            $nationalId = '999900000000' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT);
            $rtId = !empty($rtIds) ? $rtIds[$i % count($rtIds)] : null;

            DB::table('citizens')->updateOrInsert(
                ['national_id' => $nationalId],
                [
                    'record_type' => 'PENDUDUK',
                    'rt_id' => $rtId,
                    'national_id' => $nationalId,
                    'family_card_number' => fake()->numerify('################'),
                    'full_name' => fake('id_ID')->name(),
                    'birth_place' => fake('id_ID')->city(),
                    'birth_date' => fake()->date('Y-m-d', '2005-01-01'),
                    'age' => fake()->numberBetween(1, 80),
                    'gender' => fake()->randomElement(['Laki-laki', 'Perempuan']),
                    'address' => fake('id_ID')->address(),
                    'rt' => str_pad((string) (($i % 10) + 1), 3, '0', STR_PAD_LEFT),
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
                    'disability_type' => null,
                    'disability_status' => fake()->randomElement(['Tidak Ada', 'Tidak Ada', 'Ringan', 'Sedang']),
                    'religion' => $religions[$i % count($religions)],
                    'mother_national_id' => fake()->unique()->numerify('################'),
                    'father_national_id' => fake()->unique()->numerify('################'),
                    'spouse_national_id' => null,
                    'nationality' => 'WNI',
                    'ktp_address' => fake('id_ID')->address(),
                    'previous_address' => null,
                    'passport_number' => null,
                    'passport_expiry_date' => null,
                    'status' => $i < count($religions)
                        ? 'Active'
                        : fake()->randomElement(['Active', 'Active', 'Active', 'Pindah']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }
}
