<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LetterRequestSeeder extends Seeder
{
    public function run(): void
    {
        $citizenIds = DB::table('citizens')->pluck('citizen_id');
        $letterTypeIds = DB::table('letter_types')->pluck('letter_type_id');
        $userIds = DB::table('users')->pluck('user_id');
        $signerIds = DB::table('staff')->where('is_signer', true)->pluck('staff_id');

        $statuses = ['submitted', 'verified', 'authorized', 'completed', 'rejected'];

        for ($i = 0; $i < 15; $i++) {
            $status = fake()->randomElement($statuses);

            DB::table('letter_requests')->insert([
                'request_code' => 'REQ-' . now()->format('Ymd') . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'citizen_id' => fake()->boolean(70) ? $citizenIds->random() : null,
                'applicant_name' => fake('id_ID')->name(),
                'applicant_nik' => fake()->numerify('################'),
                'applicant_phone' => '08' . fake()->numerify('##########'),
                'applicant_address' => fake('id_ID')->address(),
                'letter_type_id' => $letterTypeIds->random(),
                'status' => $status,
                'form_data' => null,
                'letter_number' => in_array($status, ['authorized', 'completed']) ? fake()->bothify('???###') : null,
                'signature_type' => fake()->randomElement(['manual', 'digital']),
                'verified_by' => in_array($status, ['verified', 'authorized', 'completed']) ? $userIds->random() : null,
                'authorized_by_signer_id' => in_array($status, ['authorized', 'completed']) ? $signerIds->random() : null,
                'source' => fake()->randomElement(['Online', 'Manual (Kelurahan)']),
                'notes' => fake()->boolean(40) ? fake('id_ID')->sentence(8) : null,
                'authorized_by' => in_array($status, ['authorized', 'completed']) ? $userIds->random() : null,
                'submitted_at' => now(),
                'verified_at' => in_array($status, ['verified', 'authorized', 'completed']) ? now() : null,
                'authorized_at' => in_array($status, ['authorized', 'completed']) ? now() : null,
                'completed_at' => $status === 'completed' ? now() : null,
                'result_file_path' => $status === 'completed' ? 'results/' . fake()->uuid() . '.pdf' : null,
                'remarks' => null,
            ]);
        }
    }
}
