<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('user_id');

        for ($i = 0; $i < 12; $i++) {
            DB::table('galleries')->insert([
                'title' => fake('id_ID')->sentence(3),
                'description' => fake('id_ID')->sentence(10),
                'image' => 'galleries/' . fake()->uuid() . '.jpg',
                'uploaded_by' => $userIds->random(),
                'uploaded_at' => now(),
            ]);
        }
    }
}