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

function seedBirthCertificateRequestFixtures(): void
{
    test()->seed(SignerSeeder::class);
    test()->seed(LetterTypeSeeder::class);
    test()->seed(LetterTypeFieldSeeder::class);
    test()->seed(LetterTypeDocumentSeeder::class);
    test()->seed(PermissionSeeder::class);
}

function letterManagementUser(): User
{
    $user = User::factory()->create();
    $permissionId = DB::table('permissions')->where('slug', 'pengelolaan-surat')->value('permission_id');
    DB::table('user_permissions')->insert([
        'user_id' => $user->user_id,
        'permission_id' => $permissionId,
    ]);

    return $user;
}

it('stores legacy admin uploads and returns them in letter detail', function () {
    seedBirthCertificateRequestFixtures();
    Storage::fake('public');
    $letterTypeId = LetterType::where('code', 'PAK')->value('letter_type_id');

    $response = $this->actingAs(letterManagementUser(), 'sanctum')->post('/api/letter-requests', [
        'letter_type_id' => $letterTypeId,
        'applicant_name' => 'Nama Pemohon Input',
        'applicant_nik' => '3400000000000001',
        'applicant_address' => 'Alamat Pemohon Input',
        'source' => 'Manual (Kelurahan)',
        'form_data' => [],
        'attachments' => [[
            'label' => 'Fotokopi Kartu Keluarga (KK)',
            'file' => UploadedFile::fake()->image('kk.png'),
        ]],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.applicant_name', 'Nama Pemohon Input')
        ->assertJsonPath('data.attachments.0.file_name', 'kk.png');

    $requestId = $response->json('data.letter_request_id');
    expect(LetterRequest::with('attachments')->findOrFail($requestId)->attachments)->toHaveCount(1);

    $this->getJson("/api/letter-requests/{$requestId}")
        ->assertOk()
        ->assertJsonPath('data.applicant_name', 'Nama Pemohon Input')
        ->assertJsonPath('data.attachments.0.file_name', 'kk.png');

    $pdf = $this->get("/api/letter-requests/{$requestId}/pdf");
    $pdf->assertOk();
    expect($pdf->headers->get('content-type'))->toContain('application/pdf')
        ->and(strlen($pdf->getContent()))->toBeGreaterThan(1000);
});

it('stores online birth application attachment ids and renders its existing PDF template', function () {
    seedBirthCertificateRequestFixtures();
    Storage::fake('public');
    config(['services.public_letters.test_mode' => true]);

    $letterType = LetterType::with('documents')->where('code', 'PAK')->firstOrFail();
    $document = $letterType->documents->first(fn ($item) => str_contains(mb_strtolower($item->document_name), 'kartu keluarga'));

    $response = $this->post('/api/public/letter-requests', [
        'letter_type_code' => 'PAK',
        'applicant_name' => 'Nama Pemohon Input',
        'applicant_nik' => '3400000000000001',
        'applicant_address' => 'Alamat Pemohon Input',
        'form_data' => json_encode([
            'name' => 'Nama Umum Warga',
            'applicantName' => 'Nama Pemohon Input',
            'applicantNik' => '3400000000000001',
            'applicantAddress' => 'Alamat Pemohon Input',
            'childName' => 'Nama Anak di Surat',
        ]),
        'attachments' => [UploadedFile::fake()->image('kk-online.png')],
        'attachment_document_ids' => [$document->letter_type_document_id],
    ]);

    $response->assertCreated();
    $requestCode = $response->json('data.request_code');
    $letterRequest = LetterRequest::with('letterType')->where('request_code', $requestCode)->firstOrFail();

    expect($letterRequest->applicant_name)->toBe('Nama Pemohon Input')
        ->and($letterRequest->attachments()->count())->toBe(1);

    $admin = $this->actingAs(letterManagementUser(), 'sanctum');
    $admin->getJson('/api/letter-requests/' . $letterRequest->letter_request_id)
        ->assertOk()
        ->assertJsonPath('data.applicant_name', 'Nama Pemohon Input')
        ->assertJsonPath('data.attachments.0.file_name', 'kk-online.png')
        ->assertJsonPath('data.attachments.0.letter_type_document.document_name', $document->document_name);

    $pdf = $admin->get('/api/letter-requests/' . $letterRequest->letter_request_id . '/pdf');
    $pdf->assertOk();
    expect($pdf->headers->get('content-type'))->toContain('application/pdf')
        ->and(strlen($pdf->getContent()))->toBeGreaterThan(1000);
});

it('maps selected documents, registration witnesses, and report signer/date from new and legacy form fields', function () {
    seedBirthCertificateRequestFixtures();
    $pdfService = app(LetterPdfService::class);

    $makeRequest = function (string $code, array $form) {
        $request = LetterRequest::make([
            'request_code' => 'TEST-' . $code,
            'applicant_name' => 'Nama Pemohon',
            'applicant_nik' => '3400000000000001',
            'applicant_address' => 'Alamat Pemohon',
            'form_data' => $form,
        ]);
        $request->setRelation('citizen', null);
        $request->setRelation('letterType', LetterType::with('signer')->where('code', $code)->firstOrFail());
        $request->setRelation('authorizedSigner', null);

        return $request;
    };

    $selectedDocs = $pdfService->viewData(
        $makeRequest('PAK', ['documents' => ['family_card_copy']]),
        'birth-certificate-application-form',
    );
    expect($selectedDocs['birth']['documents'])->toBe(['family_card_copy']);

    $registrationNew = $pdfService->viewData($makeRequest('LPKL', [
        'marriageCertificateNumber' => 'AK-2020-11',
        'witness1Name' => 'Saksi Baru Satu',
        'witness1Nik' => '3400000000000002',
        'witness1Age' => '42',
        'witness1Address' => 'Alamat Saksi Baru Satu',
        'witness2Name' => 'Saksi Baru Dua',
        'witness2Nik' => '3400000000000003',
        'witness2Age' => '38',
        'witness2Address' => 'Alamat Saksi Baru Dua',
    ]), 'birth-registration-report');
    expect($registrationNew['birth']['marriage']['certificate_number'])->toBe('AK-2020-11')
        ->and($registrationNew['birth']['witnesses'][0]['name'])->toBe('Saksi Baru Satu')
        ->and($registrationNew['birth']['witnesses'][1]['address'])->toBe('Alamat Saksi Baru Dua');

    $registrationLegacy = $pdfService->viewData($makeRequest('LPKL', [
        'nomor_akta_perkawinan_ortu' => 'AK-LAMA-08',
        'nama_saksi_1' => 'Saksi Lama Satu',
        'nik_saksi_1' => '3400000000000004',
        'umur_saksi_1' => '51',
        'alamat_saksi_1' => 'Alamat Saksi Lama Satu',
        'nama_saksi_2' => 'Saksi Lama Dua',
        'nik_saksi_2' => '3400000000000005',
        'umur_saksi_2' => '47',
        'alamat_saksi_2' => 'Alamat Saksi Lama Dua',
    ]), 'birth-registration-report');
    expect($registrationLegacy['birth']['marriage']['certificate_number'])->toBe('AK-LAMA-08')
        ->and($registrationLegacy['birth']['witnesses'][0]['name'])->toBe('Saksi Lama Satu')
        ->and($registrationLegacy['birth']['witnesses'][1]['address'])->toBe('Alamat Saksi Lama Dua');

    $reportNew = $pdfService->viewData($makeRequest('LK', [
        'reporterRelationship' => 'Ibu',
        'motherName' => 'Ibu Penanda Tangan',
        'motherAddress' => 'Alamat Ibu Penanda Tangan',
        'motherBirthDate' => '1990-08-08',
        'childName' => 'Bayi Baru',
        'childBirthDayName' => 'Rabu',
        'childBirthDay' => '08',
        'childBirthMonth' => 'Oktober',
        'childBirthYear' => '2025',
    ]), 'birth-report-statement');
    expect($reportNew['birth']['reporter']['name'])->toBe('Ibu Penanda Tangan')
        ->and($reportNew['birth']['reporter']['address'])->toBe('Alamat Ibu Penanda Tangan')
        ->and($reportNew['birth']['reporter']['birth_date'])->toBe('08/08/1990')
        ->and($reportNew['birth']['child']['birth_day_name'])->toBe('Rabu')
        ->and($reportNew['birth']['child']['birth_date'])->toBe('08 Oktober 2025');

    $reportFather = $pdfService->viewData($makeRequest('LK', [
        'reporterRelationship' => 'Ayah',
        'fatherName' => 'Ayah Penanda Tangan',
        'fatherAddress' => 'Alamat Ayah Penanda Tangan',
        'fatherBirthDate' => '1979-02-03',
        'motherName' => 'Ibu yang Bukan Pelapor',
        'motherAddress' => 'Alamat Ibu yang Bukan Pelapor',
        'motherBirthDate' => '1982-04-05',
        'childBirthDayName' => 'Senin',
        'childBirthDay' => '03',
        'childBirthMonth' => 'Februari',
        'childBirthYear' => '2025',
    ]), 'birth-report-statement');
    expect($reportFather['birth']['reporter']['name'])->toBe('Ayah Penanda Tangan')
        ->and($reportFather['birth']['reporter']['address'])->toBe('Alamat Ayah Penanda Tangan')
        ->and($reportFather['birth']['reporter']['birth_date'])->toBe('03/02/1979')
        ->and($reportFather['birth']['child']['birth_date'])->toBe('03 Februari 2025');

    $html = view($pdfService->viewName('birth-report-statement'), $reportNew)->render();
    expect(substr_count($html, 'Ibu Penanda Tangan'))->toBeGreaterThanOrEqual(2)
        ->and($html)->toContain('Alamat Ibu Penanda Tangan')
        ->and($html)->toContain('08/08/1990')
        ->and($html)->toContain('08 Oktober 2025');

    $reportLegacy = $pdfService->viewData($makeRequest('LK', [
        'reporter_relationship' => 'Ayah',
        'reporter_name' => 'Ayah Pelapor Lama',
        'reporter_address' => 'Alamat Ayah Pelapor Lama',
        'child_name' => 'Bayi Lama',
        'child_birth_date' => '2020-01-16',
    ]), 'birth-report-statement');
    expect($reportLegacy['birth']['reporter']['name'])->toBe('Ayah Pelapor Lama')
        ->and($reportLegacy['birth']['reporter']['address'])->toBe('Alamat Ayah Pelapor Lama')
        ->and($reportLegacy['birth']['child']['birth_date'])->toBe('16 Januari 2020');

    foreach ([
        ['PAK', 'birth-certificate-application-form', ['documents' => ['family_card_copy']]],
        ['LPKL', 'birth-registration-report', ['witness1Name' => 'Saksi PDF', 'marriageCertificateNumber' => 'AK-PDF']],
        ['LK', 'birth-report-statement', ['reporterRelationship' => 'Ibu', 'motherName' => 'Ibu PDF', 'motherAddress' => 'Alamat PDF', 'childBirthDayName' => 'Rabu', 'childBirthDay' => '08', 'childBirthMonth' => 'Oktober', 'childBirthYear' => '2025']],
    ] as [$code, $template, $form]) {
        $data = $pdfService->viewData($makeRequest($code, $form), $template);
        expect(strlen($pdfService->pdf($pdfService->viewName($template), $data)->output()))->toBeGreaterThan(1000);
    }
});
