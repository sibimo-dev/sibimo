<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Pengumuman', 'Kegiatan Desa', 'Berita Umum', 'Bantuan Sosial'];
        foreach ($categories as $name) {
            DB::table('news_categories')->updateOrInsert(
                ['slug' => Str::slug($name)],
                [
                    'category_name' => $name,
                    'created_at' => now(),
                ],
            );
        }
    }
}
