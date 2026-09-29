<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 15; $i++) {
            $status = ['Submitted', 'In Progress', 'Resolved', 'Rejected'][$i % 4];
            $hasReporter = $i % 3 !== 0;
            $title = 'Complaint Seeder ' . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);

            DB::table('complaints')->updateOrInsert(
                ['title' => $title],
                [
                    'reporter_name' => $hasReporter ? 'Pelapor Seeder ' . ($i + 1) : null,
                    'reporter_phone' => $hasReporter ? '089900000' . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) : null,
                    'category' => ['Infrastructure', 'Public Service', 'Environment', 'Security', 'Other'][$i % 5],
                    'description' => 'Aduan contoh untuk pengujian sistem SIBIMO.',
                    'status' => $status,
                    'submitted_at' => now(),
                    'resolved_at' => in_array($status, ['Resolved', 'Rejected']) ? now() : null,
                ],
            );
        }
    }
}
