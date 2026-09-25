<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LetterRequestStatusHistorySeeder extends Seeder
{
    public function run(): void
    {
        $userId = DB::table('users')->orderBy('user_id')->value('user_id');

        if (!$userId) {
            throw new RuntimeException('Users must be seeded before letter request histories.');
        }

        $requests = DB::table('letter_requests')
            ->where('request_code', 'like', 'SEED-REQ-%')
            ->orderBy('letter_request_id')
            ->get(['letter_request_id', 'status', 'submitted_at']);

        foreach ($requests as $request) {
            DB::table('letter_request_status_histories')
                ->where('letter_request_id', $request->letter_request_id)
                ->delete();

            $submittedAt = $request->submitted_at ?? now();
            $events = [
                [
                    'status' => 'submitted',
                    'note' => 'Request contoh dibuat oleh seeder.',
                    'changed_at' => $submittedAt,
                ],
            ];

            if (in_array($request->status, ['verified', 'authorized', 'completed'], true)) {
                $events[] = [
                    'status' => 'verified',
                    'note' => 'Request contoh lolos verifikasi.',
                    'changed_at' => $this->addHours($submittedAt, 2),
                ];
            }

            if (in_array($request->status, ['authorized', 'completed'], true)) {
                $events[] = [
                    'status' => 'authorized',
                    'note' => 'Request contoh telah diotorisasi.',
                    'changed_at' => $this->addHours($submittedAt, 4),
                ];
            }

            if ($request->status === 'completed') {
                $events[] = [
                    'status' => 'completed',
                    'note' => 'Request contoh selesai diproses.',
                    'changed_at' => $this->addHours($submittedAt, 6),
                ];
            }

            if ($request->status === 'rejected') {
                $events[] = [
                    'status' => 'rejected',
                    'note' => 'Request contoh ditolak untuk pengujian alur.',
                    'changed_at' => $this->addHours($submittedAt, 2),
                ];
            }

            foreach ($events as $event) {
                DB::table('letter_request_status_histories')->insert([
                    'letter_request_id' => $request->letter_request_id,
                    'status' => $event['status'],
                    'note' => $event['note'],
                    'change_by' => $userId,
                    'changed_at' => $event['changed_at'],
                ]);
            }
        }
    }

    private function addHours($date, int $hours): Carbon
    {
        return Carbon::parse($date)->addHours($hours);
    }
}
