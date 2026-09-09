<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 15; $i++) {
            $status = fake()->randomElement(['Submitted', 'In Progress', 'Resolved', 'Rejected']);
            $hasReporter = $i % 3 !== 0;

            DB::table('complaints')->insert([
                'reporter_name' => $hasReporter ? fake('id_ID')->name() : null,
                'reporter_phone' => $hasReporter ? fake()->numerify('08##########') : null,
                'category' => fake()->randomElement(['Infrastructure', 'Public Service', 'Environment', 'Security', 'Other']),
                'title' => fake('id_ID')->sentence(5),
                'description' => fake('id_ID')->paragraph(4),
                'status' => $status,
                'submitted_at' => now(),
                'resolved_at' => in_array($status, ['Resolved', 'Rejected']) ? now() : null,
            ]);
        }
    }
}
