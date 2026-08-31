<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComplaintStatusHistorySeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('user_id');
        $complaints = DB::table('complaints')->get(['complaint_id', 'status']);

        foreach ($complaints as $complaint) {
            DB::table('complaint_status_histories')->insert([
                'complaint_id' => $complaint->complaint_id,
                'user_id' => $userIds->random(),
                'status' => $complaint->status,
                'note' => fake()->boolean(50) ? fake('id_ID')->sentence(6) : null,
                'changed_at' => now(),
            ]);
        }
    }
}