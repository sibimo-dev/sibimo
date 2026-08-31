<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgendaSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('user_id');

        for ($i = 0; $i < 10; $i++) {
            DB::table('agendas')->insert([
                'title' => fake('id_ID')->sentence(4),
                'description' => fake('id_ID')->paragraph(2),
                'event_date' => fake()->dateTimeBetween('now', '+2 months')->format('Y-m-d'),
                'start_time' => fake()->time('H:i:s'),
                'end_time' => fake()->time('H:i:s'),
                'location' => fake('id_ID')->address(),
                'created_by' => $userIds->random(),
                'created_at' => now(),
            ]);
        }
    }
}