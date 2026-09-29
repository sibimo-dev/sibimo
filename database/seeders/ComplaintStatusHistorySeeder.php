<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComplaintStatusHistorySeeder extends Seeder
{
    public function run(): void
    {
        $userId = DB::table('users')->value('user_id');
        $complaints = DB::table('complaints')
            ->where('title', 'like', 'Complaint Seeder %')
            ->get(['complaint_id', 'status']);

        $complaintIds = $complaints->pluck('complaint_id');
        DB::table('complaint_status_histories')
            ->whereIn('complaint_id', $complaintIds)
            ->delete();

        foreach ($complaints as $complaint) {
            DB::table('complaint_status_histories')->insert([
                'complaint_id' => $complaint->complaint_id,
                'user_id' => $userId,
                'status' => $complaint->status,
                'note' => 'Riwayat status contoh dari seeder.',
                'changed_at' => now(),
            ]);
        }
    }
}
