<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AgendaSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('user_id');
        $disk = Storage::disk('public');
        $images = array_values(array_filter($disk->files('galleries'), fn (string $file) =>
            in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)
        ));

        for ($i = 0; $i < 10; $i++) {
            $title = 'Agenda Seeder ' . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);

            DB::table('agendas')->updateOrInsert(
                ['title' => $title],
                [
                    'description' => 'Agenda contoh untuk pengujian sistem SIBIMO.',
                    'event_date' => now()->addDays($i + 1)->format('Y-m-d'),
                    'start_time' => '08:00:00',
                    'end_time' => '10:00:00',
                    'location' => 'Kalurahan Bimomartani',
                    'attachment_path' => count($images) > 0 ? $images[$i % count($images)] : null,
                    'status' => 'Published',
                    'created_by' => $userIds->first(),
                    'created_at' => now(),
                ],
            );
        }
    }
}
