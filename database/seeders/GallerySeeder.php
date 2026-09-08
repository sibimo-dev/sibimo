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
            DB::table('galleries')->insert([
                'title' => fake('id_ID')->sentence(3),
                'description' => fake('id_ID')->sentence(10),
                'image' => Storage::disk('public')->url('galleries/' . $galleryImages[$i % count($galleryImages)]),
                'uploaded_by' => $userIds->random(),
                'uploaded_at' => now(),
            ]);
        }
    }
}
