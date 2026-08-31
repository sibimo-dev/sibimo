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
            DB::table('books')->insert([
                'category_id' => $categoryIds->random(),
                'title' => fake('id_ID')->sentence(4),
                'author' => fake('id_ID')->name(),
                'isbn' => fake()->isbn13(),
                'stock' => fake()->numberBetween(1, 15),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}