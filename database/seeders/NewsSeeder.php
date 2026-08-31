<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = DB::table('news_categories')->pluck('category_id');
        $userIds = DB::table('users')->pluck('user_id');

        for ($i = 0; $i < 15; $i++) {
            $title = fake('id_ID')->sentence(6);
            DB::table('news')->insert([
                'category_id' => $categoryIds->random(),
                'author_id' => $userIds->random(),
                'title' => $title,
                'slug' => Str::slug($title) . '-' . fake()->unique()->randomNumber(4),
                'content' => fake('id_ID')->paragraphs(5, true),
                'thumbnail' => 'news/' . fake()->uuid() . '.jpg',
                'status' => fake()->randomElement(['Draft', 'Published', 'Archived']),
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}