<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = DB::table('news_categories')->pluck('category_id');
        $userIds = DB::table('users')->pluck('user_id');
        $newsImages = [
            'news-01.jpeg',
            'news-02.jpeg',
            'news-03.jpeg',
            'news-04.jpg',
            'news-05.jpg',
            'news-06.jpg',
            'news-07.jpg',
            'news-08.jpg',
            'news-09.jpg',
            'news-10.jpg',
            'news-11.jpg',
            'news-12.jpg',
            'news-13.jpg',
        ];

        for ($i = 0; $i < 15; $i++) {
            $title = fake('id_ID')->sentence(6);
            DB::table('news')->insert([
                'category_id' => $categoryIds->random(),
                'author_id' => $userIds->random(),
                'title' => $title,
                'slug' => Str::slug($title) . '-' . fake()->unique()->randomNumber(4),
                'content' => fake('id_ID')->paragraphs(5, true),
                'thumbnail' => Storage::disk('public')->url('news/' . $newsImages[$i % count($newsImages)]),
                'status' => fake()->randomElement(['Draft', 'Published', 'Archived']),
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
