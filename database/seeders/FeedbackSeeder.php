<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 15; $i++) {
            $email = 'seed-feedback-' . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) . '@sibimo.test';

            DB::table('feedbacks')->updateOrInsert(
                ['email' => $email],
                [
                    'full_name' => 'Feedback Seeder ' . ($i + 1),
                    'message' => 'Feedback contoh untuk pengujian sistem SIBIMO.',
                    'status' => ['Unread', 'Read', 'Replied'][$i % 3],
                    'submitted_at' => now(),
                ],
            );
        }
    }
}
