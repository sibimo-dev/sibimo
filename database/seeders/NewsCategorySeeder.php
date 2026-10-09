<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Pemerintahan', 'Kegiatan Warga', 'Pembangunan', 'Pengumuman', 'Kesehatan'];
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
