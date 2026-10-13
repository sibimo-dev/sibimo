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
            DB::table('book_categories')->updateOrInsert(
                ['category_name' => $name],
                [
                    'description' => 'Kategori buku ' . $name . '.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }
}
