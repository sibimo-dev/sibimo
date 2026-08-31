<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitizenSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 30; $i++) {
            DB::table('citizens')->insert([
                'national_id' => fake()->unique()->numerify('################'), // 16 digit
                'family_card_number' => fake()->numerify('################'),
                'full_name' => fake('id_ID')->name(),
                'birth_place' => fake('id_ID')->city(),
                'birth_date' => fake()->date('Y-m-d', '2005-01-01'),
                'gender' => fake()->randomElement(['Laki-laki', 'Perempuan']),
                'address' => fake('id_ID')->address(),
                'phone_number' => '08' . fake()->numerify('##########'),
                'occupation' => fake()->randomElement(['Wiraswasta', 'Guru', 'Petani', 'Perawat', 'Karyawan Swasta', 'Ibu Rumah Tangga', 'Wirausaha', 'Mahasiswa', 'Nelayan', 'Pedagang']),
                'education' => fake()->randomElement(['SD', 'SMP', 'SMA/SMK', 'D3', 'S1']),
                'marital_status' => fake()->randomElement(['Menikah', 'Belum Menikah', 'Cerai Hidup', 'Cerai Mati']),
                'status' => fake()->randomElement(['Active', 'Active', 'Active', 'Pindah']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}