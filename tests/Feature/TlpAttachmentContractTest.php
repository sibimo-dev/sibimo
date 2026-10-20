<?php

use App\Models\LetterRequest;
use App\Models\LetterType;
use App\Models\User;
use App\Services\LetterPdfService;
use Database\Seeders\LetterTypeDocumentSeeder;
use Database\Seeders\LetterTypeFieldSeeder;
use Database\Seeders\LetterTypeSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\SignerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function seedTlpAttachmentContract(): array
{
    test()->seed(SignerSeeder::class);
    test()->seed(LetterTypeSeeder::class);
    test()->seed(LetterTypeFieldSeeder::class);
    test()->seed(LetterTypeDocumentSeeder::class);
    test()->seed(PermissionSeeder::class);

    $user = User::factory()->create();
    $permissionId = DB::table('permissions')->where('slug', 'pengelolaan-surat')->value('permission_id');
    DB::table('user_permissions')->insert([
        'user_id' => $user->user_id,
        'permission_id' => $permissionId,
    ]);

    $letterType = LetterType::with('documents')->where('code', 'SMLPI')->firstOrFail();

    return [$user, $letterType];
}

it('matches multipart TLP attachment labels from request input to documents of the submitted SMLPI type', function () {
    [$user, $letterType] = seedTlpAttachmentContract();
    Storage::fake('public');

    $document = $letterType->documents->firstWhere('document_name', 'Fotocopy Kartu Keluarga (KK)');
    expect($document)->not->toBeNull();

    $response = $this->actingAs($user, 'sanctum')->post('/api/letter-requests', [
        'letter_type_id' => $letterType->letter_type_id,
        'applicant_name' => 'Pemohon Uji',
        'applicant_nik' => '3400000000000001',
        'applicant_address' => 'Alamat Uji',
        'source' => 'Manual (Kelurahan)',
        'form_data' => [],
        'attachments' => [[
            'label' => 'Fotocopy Kartu Keluarga (KK)',
            'file' => UploadedFile::fake()->image('kk.png'),
        ]],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.letter_type.code', 'SMLPI')
        ->assertJsonPath('data.attachments.0.letter_type_document.document_name', $document->document_name);

    $requestId = $response->json('data.letter_request_id');
    expect(LetterRequest::with('attachments')->findOrFail($requestId)->attachments->first()->letter_type_document_id)
        ->toBe($document->letter_type_document_id);
});

it('continues rejecting TLP attachments whose labels do not match a configured document', function () {
    [$user, $letterType] = seedTlpAttachmentContract();
    Storage::fake('public');

    $this->actingAs($user, 'sanctum')->post('/api/letter-requests', [
        'letter_type_id' => $letterType->letter_type_id,
        'applicant_name' => 'Pemohon Uji',
        'applicant_nik' => '3400000000000001',
        'applicant_address' => 'Alamat Uji',
        'source' => 'Manual (Kelurahan)',
        'form_data' => [],
        'attachments' => [[
            'label' => 'Label tidak dikenal',
            'file' => UploadedFile::fake()->image('unknown.png'),
        ]],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['attachments']);
});

it('normalizes TLP form_data to the exact recipient and event keys consumed by its Blade template', function () {
    [, $letterType] = seedTlpAttachmentContract();
    $request = LetterRequest::make([
        'request_code' => 'TEST-TLP-PDF',
        'letter_number' => 'SMLPI/TEST/X/2026',
        'applicant_name' => 'Pemohon Uji',
        'applicant_nik' => '3400000000000001',
        'applicant_address' => 'Alamat Pemohon Uji',
        'form_data' => [
            'ditujukan_kepada' => 'Penerima Uji',
            'lokasi_tujuan_surat' => 'Alamat Penerima Uji',
            'nomor_surat_asal' => '001/IZIN/2026',
            'tanggal_kegiatan' => '2026-10-19',
            'waktu_kegiatan' => '09.00 WIB',
            'nama_acara' => 'Kegiatan Uji',
            'tempat_kegiatan' => 'Lokasi Kegiatan Uji',
        ],
    ]);
    $request->setRelation('letterType', $letterType);
    $request->setRelation('citizen', null);
    $request->setRelation('authorizedSigner', null);

    $data = app(LetterPdfService::class)->viewData($request, 'permit-followup-letter');

    expect($data['recipient'])->toBe('Penerima Uji')
        ->and($data['recipient_address'])->toBe('Alamat Penerima Uji')
        ->and($data['ref_number'])->toBe('001/IZIN/2026')
        ->and($data['event'])->toMatchArray([
            'day_name' => 'Senin',
            'date' => '2026-10-19',
            'time' => '09.00 WIB',
            'objective' => 'Kegiatan Uji',
            'place' => 'Lokasi Kegiatan Uji',
        ]);

    $bufferLevel = ob_get_level();
    try {
        $html = view('letters.permit-followup-letter', $data)->render();
    } finally {
        while (ob_get_level() > $bufferLevel) {
            ob_end_clean();
        }
    }
    expect($html)->toContain('Penerima Uji')
        ->and($html)->toContain('Alamat Penerima Uji')
        ->and($html)->toContain('Senin')
        ->and($html)->toContain('19 Oktober 2026')
        ->and($html)->toContain('09.00 WIB')
        ->and($html)->toContain('Kegiatan Uji')
        ->and($html)->toContain('Lokasi Kegiatan Uji');
});
