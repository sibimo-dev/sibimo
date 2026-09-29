<?php

use App\Models\User;
use Database\Seeders\CitizenSeeder;
use Database\Seeders\LetterNumberSequenceSeeder;
use Database\Seeders\LetterRequestAttachmentSeeder;
use Database\Seeders\LetterRequestSeeder;
use Database\Seeders\LetterRequestStatusHistorySeeder;
use Database\Seeders\LetterTypeDocumentSeeder;
use Database\Seeders\LetterTypeFieldSeeder;
use Database\Seeders\LetterTypeSeeder;
use Database\Seeders\SignerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function seedLetterFixtures(): void
{
    test()->seed(SignerSeeder::class);
    test()->seed(CitizenSeeder::class);
    User::factory()->create();
    test()->seed(LetterTypeSeeder::class);
    test()->seed(LetterTypeFieldSeeder::class);
    test()->seed(LetterTypeDocumentSeeder::class);
    test()->seed(LetterNumberSequenceSeeder::class);
    test()->seed(LetterRequestSeeder::class);
    test()->seed(LetterRequestAttachmentSeeder::class);
    test()->seed(LetterRequestStatusHistorySeeder::class);
}

it('seeds a complete and connected letter fixture set', function () {
    seedLetterFixtures();

    expect(DB::table('letter_types')->count())->toBe(46)
        ->and(DB::table('letter_type_fields')->count())->toBeGreaterThan(0)
        ->and(DB::table('letter_type_documents')->count())->toBeGreaterThanOrEqual(92)
        ->and(DB::table('letter_number_sequences')->where('year', now()->year)->count())->toBe(46)
        ->and(DB::table('letter_requests')->where('request_code', 'like', 'SEED-REQ-%')->count())->toBe(46);

    expect(DB::table('letter_type_fields as fields')
        ->leftJoin('letter_types as types', 'types.letter_type_id', '=', 'fields.letter_type_id')
        ->whereNull('types.letter_type_id')
        ->count())->toBe(0);

    expect(DB::table('letter_type_documents as documents')
        ->leftJoin('letter_types as types', 'types.letter_type_id', '=', 'documents.letter_type_id')
        ->whereNull('types.letter_type_id')
        ->count())->toBe(0);

    expect(DB::table('letter_request_attachments as attachments')
        ->join('letter_requests as requests', 'requests.letter_request_id', '=', 'attachments.letter_request_id')
        ->join('letter_type_documents as documents', 'documents.letter_type_document_id', '=', 'attachments.letter_type_document_id')
        ->whereColumn('requests.letter_type_id', '<>', 'documents.letter_type_id')
        ->count())->toBe(0);

    expect(DB::table('letter_type_fields')
        ->select('letter_type_id', 'field_key')
        ->groupBy('letter_type_id', 'field_key')
        ->havingRaw('COUNT(*) > 1')
        ->count())->toBe(0);
});

it('can rerun the letter seeders without increasing fixture counts', function () {
    seedLetterFixtures();

    $before = [
        'types' => DB::table('letter_types')->count(),
        'fields' => DB::table('letter_type_fields')->count(),
        'documents' => DB::table('letter_type_documents')->count(),
        'sequences' => DB::table('letter_number_sequences')->count(),
        'requests' => DB::table('letter_requests')->where('request_code', 'like', 'SEED-REQ-%')->count(),
        'attachments' => DB::table('letter_request_attachments')->count(),
        'histories' => DB::table('letter_request_status_histories')->count(),
    ];

    seedLetterFixtures();

    $after = [
        'types' => DB::table('letter_types')->count(),
        'fields' => DB::table('letter_type_fields')->count(),
        'documents' => DB::table('letter_type_documents')->count(),
        'sequences' => DB::table('letter_number_sequences')->count(),
        'requests' => DB::table('letter_requests')->where('request_code', 'like', 'SEED-REQ-%')->count(),
        'attachments' => DB::table('letter_request_attachments')->count(),
        'histories' => DB::table('letter_request_status_histories')->count(),
    ];

    expect($after)->toBe($before);
});
