<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 15; $i++) {
            DB::table('feedbacks')->insert([
                'full_name' => fake('id_ID')->name(),
                'email' => fake()->safeEmail(),
                'message' => fake('id_ID')->paragraph(3),
                'status' => fake()->randomElement(['Unread', 'Read', 'Replied']),
                'submitted_at' => now(),
            ]);
        }
    }
}