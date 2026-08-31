<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Fiksi', 'Non-Fiksi', 'Pendidikan', 'Agama', 'Sejarah'];
        foreach ($categories as $name) {
            DB::table('book_categories')->insert([
                'category_name' => $name,
                'description' => fake('id_ID')->sentence(8),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}