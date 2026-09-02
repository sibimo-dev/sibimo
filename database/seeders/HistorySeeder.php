<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistorySeeder extends Seeder
{
    public function run(): void
    {
        $publisherId = DB::table('users')->value('user_id');

        DB::table('histories')->insert([
            'title' => 'Sejarah Kalurahan Bimomartani',
            'year_founded' => 1946,
            'points' => json_encode([
                'Nama Kalurahan Bimomartani terbentuk pada tanggal 29 April 1946.',
                'Kalurahan Bimomartani merupakan gabungan dari Jatisari, Cokrosari, dan Opaksari.',
                'Pemerintah kalurahan terdiri dari Lurah dan Perangkat Kalurahan.',
            ]),
            'photos' => json_encode([]),
            'published_by' => $publisherId,
            'status' => 'Published',
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
