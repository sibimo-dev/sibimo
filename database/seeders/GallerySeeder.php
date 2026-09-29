<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('user_id');
        $galleryImages = [
            'gallery-01.jpg',
            'gallery-02.jpg',
            'gallery-03.jpeg',
            'gallery-04.jpeg',
            'gallery-05.jpeg',
            'gallery-06.jpeg',
            'gallery-07.jpeg',
            'gallery-08.jpeg',
            'gallery-09.jpeg',
            'gallery-10.jpeg',
            'gallery-11.jpeg',
            'gallery-12.jpeg',
            'gallery-13.jpeg',
            'gallery-14.jpeg',
        ];

        for ($i = 0; $i < 12; $i++) {
            $image = Storage::disk('public')->url('galleries/' . $galleryImages[$i % count($galleryImages)]);

            DB::table('galleries')->updateOrInsert(
                ['image' => $image],
                [
                    'title' => 'Galeri Seeder ' . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT),
                    'description' => 'Galeri contoh untuk pengujian sistem SIBIMO.',
                    'uploaded_by' => $userIds->first(),
                    'uploaded_at' => now(),
                ],
            );
        }
    }
}
