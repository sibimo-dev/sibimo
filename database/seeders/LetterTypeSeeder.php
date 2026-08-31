<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LetterTypeSeeder extends Seeder
{
    public function run(): void
    {
        $signerId = DB::table('signers')->value('signer_id');

        $types = [
            ['SKTM', 'Surat Keterangan Tidak Mampu', 'Keterangan', 'manual'],
            ['SPPD', 'SPPD (Surat Perintah Perjalanan Dinas)', 'Perintah', 'digital'],
            ['SKBM', 'Surat Keterangan Belum Menikah', 'Keterangan', 'manual'],
            ['SKD', 'Surat Keterangan Domisili', 'Keterangan', 'manual'],
            ['SPU', 'Surat Pengantar Usaha', 'Pengantar', 'digital'],
        ];

        foreach ($types as [$code, $name, $category, $method]) {
            DB::table('letter_types')->insert([
                'code' => $code,
                'letter_name' => $name,
                'category' => $category,
                'description' => fake('id_ID')->sentence(10),
                'blade_view' => null,
                'number_prefix' => strtoupper(fake()->lexify('???')) . '/',
                'processing_time' => fake()->randomElement(['15 menit', '1 hari', '2 hari', '3 hari']),
                'signature_method' => $method,
                'signer_id' => $signerId,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}