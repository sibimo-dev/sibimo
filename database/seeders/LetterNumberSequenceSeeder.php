<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LetterNumberSequenceSeeder extends Seeder
{
    public function run(): void
    {
        $letterTypeIds = DB::table('letter_types')->pluck('letter_type_id');

        foreach ($letterTypeIds as $letterTypeId) {
            DB::table('letter_number_sequences')->insert([
                'letter_type_id' => $letterTypeId,
                'year' => (int) now()->format('Y'),
                'last_sequence' => fake()->numberBetween(0, 20),
                'updated_at' => now(),
            ]);
        }
    }
}