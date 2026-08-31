<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileContentSeeder extends Seeder
{
    public function run(): void
    {
        $sectionIds = DB::table('profile_sections')->pluck('section_id');
        $userIds = DB::table('users')->pluck('user_id');

        foreach ($sectionIds as $sectionId) {
            DB::table('profile_contents')->insert([
                'section_id' => $sectionId,
                'title' => fake('id_ID')->sentence(4),
                'content' => fake('id_ID')->paragraphs(4, true),
                'thumbnail' => 'thumbnails/' . fake()->uuid() . '.jpg',
                'published_by' => $userIds->random(),
                'status' => fake()->randomElement(['Draft', 'Published']),
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}