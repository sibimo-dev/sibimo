<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComplaintAttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $complaintIds = DB::table('complaints')->pluck('complaint_id');

        foreach ($complaintIds as $complaintId) {
            if (fake()->boolean(60)) {
                $fileName = fake()->uuid() . '.jpg';
                DB::table('complaint_attachments')->insert([
                    'complaint_id' => $complaintId,
                    'file_name' => $fileName,
                    'file_path' => 'complaints/' . $fileName,
                    'uploaded_at' => now(),
                ]);
            }
        }
    }
}