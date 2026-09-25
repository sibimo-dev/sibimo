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
            $slug = 'seed-news-' . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);

            DB::table('news')->updateOrInsert(
                ['slug' => $slug],
                [
                    'category_id' => $categoryIds[$i % $categoryIds->count()],
                    'author_id' => $userIds->first(),
                    'title' => 'Berita Seeder ' . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT),
                    'content' => 'Berita contoh untuk pengujian sistem SIBIMO.',
                    'thumbnail' => Storage::disk('public')->url('news/' . $newsImages[$i % count($newsImages)]),
                    'status' => ['Draft', 'Published', 'Archived'][$i % 3],
                    'published_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }
}
