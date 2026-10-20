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

function seedSpskgAttachmentContract(): array
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

    return [$user, LetterType::with('documents')->where('code', 'SPSKG')->firstOrFail()];
}

it('matches the admin SPSKG attachment labels to documents configured for that type', function () {
    [$user, $letterType] = seedSpskgAttachmentContract();
    Storage::fake('public');

    $requestLabel = 'Surat permintaan dari pihak penyewa';
    $assetLabel = 'Dokumen kepemilikan aset (sertifikat / Letter C)';
    $requestDocument = $letterType->documents->firstWhere('document_name', $requestLabel);
    $assetDocument = $letterType->documents->firstWhere('document_name', $assetLabel);

    expect($requestDocument)->not->toBeNull()
        ->and($assetDocument)->not->toBeNull()
        ->and($assetDocument->is_required)->toBeFalsy();

    $response = $this->actingAs($user, 'sanctum')->post('/api/letter-requests', [
        'letter_type_id' => $letterType->letter_type_id,
        'applicant_name' => 'Pemohon Uji',
        'applicant_nik' => '3400000000000001',
        'applicant_address' => 'Alamat Uji',
        'source' => 'Manual (Kelurahan)',
        'form_data' => [],
        'attachments' => [
            [
                'label' => $requestLabel,
                'file' => UploadedFile::fake()->image('permintaan.png'),
            ],
            [
                'label' => $assetLabel,
                'file' => UploadedFile::fake()->image('aset.png'),
            ],
        ],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.letter_type.code', 'SPSKG')
        ->assertJsonCount(2, 'data.attachments')
        ->assertJsonPath('data.attachments.0.letter_type_document.document_name', $requestLabel)
        ->assertJsonPath('data.attachments.1.letter_type_document.document_name', $assetLabel);

    $requestId = $response->json('data.letter_request_id');
    expect(LetterRequest::with('attachments')->findOrFail($requestId)->attachments->pluck('letter_type_document_id')->all())
        ->toBe([$requestDocument->letter_type_document_id, $assetDocument->letter_type_document_id]);
});

it('continues rejecting SPSKG attachment labels that are not configured for the type', function () {
    [$user, $letterType] = seedSpskgAttachmentContract();
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

it('normalizes submitted SPSKG form keys and renders their values in the PDF Blade', function () {
    [, $letterType] = seedSpskgAttachmentContract();
    $request = LetterRequest::make([
        'request_code' => 'TEST-SPSKG-PDF',
        'letter_number' => 'SPSKG/TEST/X/2026',
        'applicant_name' => 'Pemohon Uji',
        'applicant_nik' => '3400000000000001',
        'applicant_address' => 'Alamat Pemohon Uji',
        'form_data' => [
            'tenant_name' => 'PT Penyewa Sintetis',
            'tenant_representative' => 'Penerima Sintetis',
            'tenant_position' => 'Kepala Unit',
            'tenant_address' => 'Alamat Penyewa Sintetis',
            'property_name' => 'Gedung Uji',
            'property_address' => 'Alamat Gedung Uji',
            // Key legacy yang benar-benar tersimpan pada request SPSKG terbaru.
            'rent_start_date' => '2026-09-30',
            'rent_price' => 'Rp 45.000.000',
            'rent_period' => '5',
        ],
    ]);
    $request->setRelation('letterType', $letterType);
    $request->setRelation('citizen', null);
    $request->setRelation('authorizedSigner', null);

    $data = app(LetterPdfService::class)->viewData($request, 'lease-offer-letter');

    expect($data['recipient'])->toBe('Penerima Sintetis')
        ->and($data['recipient_address'])->toBe('Alamat Penyewa Sintetis')
        ->and($data['lease'])->toMatchArray([
            'tenant_name' => 'PT Penyewa Sintetis',
            'tenant_representative' => 'Penerima Sintetis',
            'tenant_position' => 'Kepala Unit',
            'tenant_address' => 'Alamat Penyewa Sintetis',
            'building_name' => 'Gedung Uji',
            'building_address' => 'Alamat Gedung Uji',
            'previous_end_date' => '30 September 2026',
            'price_per_year' => 'Rp 45.000.000',
            'duration_years' => '5',
        ]);

    $bufferLevel = ob_get_level();
    try {
        $html = view('letters.lease-offer-letter', $data)->render();
    } finally {
        while (ob_get_level() > $bufferLevel) {
            ob_end_clean();
        }
    }

    expect($html)->toContain('Penerima Sintetis')
        ->and($html)->toContain('PT Penyewa Sintetis')
        ->and($html)->toContain('Kepala Unit')
        ->and($html)->toContain('Alamat Penyewa Sintetis')
        ->and($html)->toContain('Alamat Gedung Uji')
        ->and($html)->toContain('30 September 2026')
        ->and($html)->toContain('Rp 45.000.000')
        ->and($html)->toContain('lama sewa 5 tahun')
        ->and($html)->toContain('<td class="val">Penerima Sintetis</td>')
        ->and($html)->toContain('<td class="val">Kepala Unit</td>')
        ->and($html)->toContain('<td class="val">Alamat Penyewa Sintetis</td>');
});

it('normalizes the current frontend previousLeaseEndDate key', function () {
    [, $letterType] = seedSpskgAttachmentContract();
    $request = LetterRequest::make([
        'form_data' => ['previousLeaseEndDate' => '2026-09-30'],
    ]);
    $request->setRelation('letterType', $letterType);
    $request->setRelation('citizen', null);
    $request->setRelation('authorizedSigner', null);

    $data = app(LetterPdfService::class)->viewData($request, 'lease-offer-letter');

    expect($data['lease']['previous_end_date'])->toBe('30 September 2026');
});
