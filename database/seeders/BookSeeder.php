<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = DB::table('book_categories')->pluck('category_id');

        for ($i = 0; $i < 20; $i++) {
            $isbn = '978999900' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT);

            DB::table('books')->updateOrInsert(
                ['isbn' => $isbn],
                [
                    'category_id' => $categoryIds[$i % $categoryIds->count()],
                    'title' => 'Buku Seeder ' . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT),
                    'author' => 'Penulis Seeder',
                    'stock' => 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }
}
