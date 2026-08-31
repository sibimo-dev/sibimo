<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LetterTypeDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $letterTypeIds = DB::table('letter_types')->pluck('letter_type_id');
        $documents = ['Kartu Tanda Penduduk', 'Kartu Keluarga', 'Surat Pengantar RT/RW', 'Surat Nikah'];

        foreach ($letterTypeIds as $letterTypeId) {
            foreach (fake()->randomElements($documents, fake()->numberBetween(1, 3)) as $doc) {
                DB::table('letter_type_documents')->insert([
                    'letter_type_id' => $letterTypeId,
                    'document_name' => $doc,
                    'description' => null,
                    'is_required' => fake()->boolean(80),
                    'created_at' => now(),
                ]);
            }
        }
    }
}