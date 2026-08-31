<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $citizenIds = DB::table('citizens')->pluck('citizen_id');

        for ($i = 0; $i < 15; $i++) {
            $status = fake()->randomElement(['Submitted', 'In Progress', 'Resolved', 'Rejected']);

            DB::table('complaints')->insert([
                'citizen_id' => $citizenIds->random(),
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