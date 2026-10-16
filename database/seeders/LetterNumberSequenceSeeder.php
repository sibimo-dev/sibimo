<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LetterNumberSequenceSeeder extends Seeder
{
    public function run(): void
    {
        $year  = (int) now()->format('Y');
        $month = (int) now()->format('n');

        DB::table('letter_types')
            ->orderBy('letter_type_id')
            ->pluck('letter_type_id')
            ->each(function (int $letterTypeId) use ($year, $month): void {
                DB::table('letter_number_sequences')->insertOrIgnore([
                    'letter_type_id' => $letterTypeId,
                    'year' => $year,
                    'month' => $month,
                    'last_sequence' => 0,
                    'updated_at' => now(),
                ]);
            });
    }
}
