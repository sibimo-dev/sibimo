<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LetterRequestAttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $letterRequestIds = DB::table('letter_requests')->pluck('letter_request_id');
        $documentIds = DB::table('letter_type_documents')->pluck('letter_type_document_id');

        foreach ($letterRequestIds as $requestId) {
            foreach (range(1, fake()->numberBetween(1, 2)) as $n) {
                $fileName = fake()->uuid() . '.pdf';
                DB::table('letter_request_attachments')->insert([
                    'letter_request_id' => $requestId,
                    'letter_type_document_id' => $documentIds->random(),
                    'file_name' => $fileName,
                    'file_path' => 'letter_requests/' . $fileName,
                    'uploaded_at' => now(),
                ]);
            }
        }
    }
}