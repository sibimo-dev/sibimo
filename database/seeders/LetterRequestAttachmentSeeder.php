<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LetterRequestAttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $requests = DB::table('letter_requests')
            ->where('request_code', 'like', 'SEED-REQ-%')
            ->orderBy('letter_request_id')
            ->get(['letter_request_id', 'letter_type_id']);

        foreach ($requests as $request) {
            $documents = DB::table('letter_type_documents')
                ->where('letter_type_id', $request->letter_type_id)
                ->orderBy('letter_type_document_id')
                ->limit(2)
                ->get(['letter_type_document_id']);

            foreach ($documents as $document) {
                $fileName = sprintf(
                    'seed-request-%d-document-%d.pdf',
                    $request->letter_request_id,
                    $document->letter_type_document_id,
                );

                DB::table('letter_request_attachments')->updateOrInsert(
                    [
                        'letter_request_id' => $request->letter_request_id,
                        'letter_type_document_id' => $document->letter_type_document_id,
                    ],
                    [
                        'file_name' => $fileName,
                        'file_path' => 'letter_requests/' . $fileName,
                        'uploaded_at' => now(),
                    ],
                );
            }
        }
    }
}
