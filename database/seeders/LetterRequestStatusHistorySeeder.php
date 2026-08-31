<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LetterRequestStatusHistorySeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('user_id');
        $requests = DB::table('letter_requests')->get(['letter_request_id', 'status']);

        foreach ($requests as $req) {
            DB::table('letter_request_status_histories')->insert([
                'letter_request_id' => $req->letter_request_id,
                'status' => $req->status,
                'note' => fake()->boolean(50) ? fake('id_ID')->sentence(6) : null,
                'change_by' => $userIds->random(),
                'changed_at' => now(),
            ]);
        }
    }
}