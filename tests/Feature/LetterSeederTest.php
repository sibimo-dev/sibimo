<?php

use App\Models\User;
use App\Models\LetterRequest;
use App\Models\LetterType;
use App\Services\DeathTemplateService;
use App\Services\LetterPdfService;
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

function deathTemplateForType(LetterType $letterType): ?string
{
    $bladeView = (string) ($letterType->blade_view ?? '');

    if ($letterType->code === 'SKKM') {
        return 'death-certificate';
    }

    return str_starts_with($bladeView, 'letters.death.')
        ? str($bladeView)->afterLast('.')->toString()
        : null;
}

it('seeds a complete and connected letter fixture set', function () {
    seedLetterFixtures();

    expect(DB::table('letter_types')->count())->toBe(85)
        ->and(DB::table('letter_type_fields')->count())->toBeGreaterThan(0)
        ->and(DB::table('letter_type_documents')->count())->toBeGreaterThanOrEqual(170)
        ->and(DB::table('letter_number_sequences')->where('year', now()->year)->count())->toBe(85)
        ->and(DB::table('letter_requests')->where('request_code', 'like', 'SEED-REQ-%')->count())->toBe(85);

    expect(DB::table('letter_types')
        ->whereIn('code', ['SKBK', 'SKU', 'SKUM', 'SKD', 'SKTM', 'SKP', 'SKK', 'SKJ', 'SKCK', 'SKDPAK', 'SBP'])
        ->whereNotNull('blade_view')
        ->count())->toBe(11);

    expect(DB::table('letter_types')->whereNotNull('blade_view')->count())->toBe(85);
    expect(DB::table('letter_types')->whereNull('blade_view')->count())->toBe(0);

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

it('renders a PDF for every seeded letter type', function () {
    seedLetterFixtures();

    $pdfService = app(LetterPdfService::class);
    $deathTemplateService = app(DeathTemplateService::class);
    $bufferLevel = ob_get_level();

    foreach (LetterType::with('signer')->orderBy('code')->get() as $letterType) {
        $request = LetterRequest::make([
            'request_code' => 'TEST-' . $letterType->code,
            'applicant_name' => 'Pemohon Test',
            'applicant_nik' => '3400000000000001',
            'applicant_address' => 'Bimomartani',
            'form_data' => [],
        ]);
        $request->setRelation('citizen', null);
        $request->setRelation('letterType', $letterType);
        $request->setRelation('authorizedSigner', null);

        $deathTemplate = deathTemplateForType($letterType);

        if ($deathTemplate) {
            $pdf = $deathTemplateService->pdf(
                $deathTemplate,
                $deathTemplateService->dataForRequest($request, $deathTemplate),
            );
        } else {
            $template = $pdfService->rootTemplateForType($letterType);
            expect($template)->not->toBeNull();

            $pdf = $pdfService->pdf(
                $pdfService->viewName($template),
                $pdfService->viewData($request, $template),
            );
        }

        $pdfOutput = $pdf->output();

        while (ob_get_level() > $bufferLevel) {
            ob_end_clean();
        }

        expect(strlen($pdfOutput))->toBeGreaterThan(1000);
    }
});

it('renders seeded request data without dropping template-specific fields', function () {
    seedLetterFixtures();

    $pdfService = app(LetterPdfService::class);
    $deathTemplateService = app(DeathTemplateService::class);
    $bufferLevel = ob_get_level();

    foreach (LetterRequest::with(['letterType.signer', 'citizen', 'authorizedSigner'])
        ->where('request_code', 'like', 'SEED-REQ-%')
        ->orderBy('request_code')
        ->get() as $request) {
        $deathTemplate = deathTemplateForType($request->letterType);

        if ($deathTemplate) {
            $pdf = $deathTemplateService->pdf(
                $deathTemplate,
                $deathTemplateService->dataForRequest($request, $deathTemplate),
            );
        } else {
            $template = $pdfService->rootTemplateForType($request->letterType);
            expect($template)->not->toBeNull();

            $pdf = $pdfService->pdf(
                $pdfService->viewName($template),
                $pdfService->viewData($request, $template),
            );
        }

        $pdfOutput = $pdf->output();

        while (ob_get_level() > $bufferLevel) {
            ob_end_clean();
        }

        expect(strlen($pdfOutput))->toBeGreaterThan(1000);
    }
});

it('seeds populated identity and template fields for birth and statement letters', function () {
    seedLetterFixtures();

    $forms = LetterRequest::query()
        ->with('letterType')
        ->whereIn('request_code', [
            'SEED-REQ-SMLPI',
            'SEED-REQ-FPK',
            'SEED-REQ-LK',
            'SEED-REQ-SPTMDK',
            'SEED-REQ-SKKL',
            'SEED-REQ-N1P',
            'SEED-REQ-N1L',
        ])
        ->get()
        ->keyBy(fn ($request) => $request->letterType->code)
        ->map(fn ($request) => $request->form_data);

    foreach (['FPK', 'SKKL'] as $code) {
        expect($forms[$code]['child_nik'])->toMatch('/^340000\d{10}$/')
            ->and($forms[$code]['child_name'])->not->toBeEmpty()
            ->and($forms[$code]['head_of_family_name'])->not->toBeEmpty()
            ->and($forms[$code]['hamlet_name'])->not->toBeEmpty()
            ->and($forms[$code]['registrar_name'])->not->toBeEmpty();
    }

    expect($forms['LK']['nik_pelapor'])->toMatch('/^340000\d{10}$/')
        ->and($forms['LK']['nama_anak'])->not->toBeEmpty()
        ->and($forms['SMLPI']['event_date'])->not->toBeEmpty()
        ->and($forms['SMLPI']['event_objective'])->not->toBeEmpty()
        ->and($forms['SMLPI']['tembusan'])->toBeArray()
        ->and($forms['SPTMDK']['birth_place'])->not->toBeEmpty()
        ->and($forms['SPTMDK']['birth_date'])->not->toBeEmpty()
        ->and($forms['SPTMDK']['mother_name'])->not->toBeEmpty()
        ->and($forms['SPTMDK']['father_name'])->not->toBeEmpty()
        ->and($forms['N1P']['nik_catin_putri'])->toMatch('/^340000\d{10}$/')
        ->and($forms['N1L']['nik_catin_putra'])->toMatch('/^340000\d{10}$/');
});

it('seeds data using the structure required by additional letter templates', function () {
    seedLetterFixtures();

    $forms = LetterRequest::query()
        ->with('letterType')
        ->whereIn('request_code', [
            'SEED-REQ-FBWNI',
            'SEED-REQ-RL',
            'SEED-REQ-SPBNI',
            'SEED-REQ-SPKLC',
            'SEED-REQ-DKF',
            'SEED-REQ-SPNIP',
            'SEED-REQ-SPDP',
            'SEED-REQ-SPTTS',
        ])
        ->get()
        ->keyBy(fn ($request) => $request->letterType->code)
        ->map(fn ($request) => $request->form_data);

    expect($forms['FBWNI']['members'])->toHaveCount(2)
        ->and($forms['FBWNI']['members'][0]['name'])->not->toBeEmpty()
        ->and($forms['FBWNI']['members'][0]['nik'])->toMatch('/^340000\d{10}$/')
        ->and($forms['RL']['register_year'])->toBe(now()->year)
        ->and($forms['RL']['rows'])->toHaveCount(1)
        ->and($forms['RL']['rows'][0]['name'])->not->toBeEmpty()
        ->and($forms['RL']['rows'][0]['nik'])->toMatch('/^340000\d{10}$/')
        ->and($forms['SPBNI']['other_name'])->not->toBeEmpty()
        ->and($forms['SPBNI']['other_nik'])->toMatch('/^340000\d{10}$/')
        ->and($forms['SPKLC']['letter_c_owner_name'])->not->toBeEmpty()
        ->and($forms['DKF']['nama_jenazah'])->not->toBeEmpty()
        ->and($forms['DKF']['nik_jenazah'])->toMatch('/^340000\d{10}$/')
        ->and($forms['SPNIP']['groom_name'])->not->toBeEmpty()
        ->and($forms['SPNIP']['bride_name'])->not->toBeEmpty()
        ->and($forms['SPDP']['family_members'])->toHaveCount(1)
        ->and($forms['SPTTS']['family_members'])->toHaveCount(2);

    $deathRequest = LetterRequest::with(['letterType.signer', 'authorizedSigner'])
        ->where('request_code', 'SEED-REQ-DKF')
        ->firstOrFail();
    $deathData = app(DeathTemplateService::class)->dataForRequest($deathRequest, 'death-report');

    expect($deathData['death']['reporter']['name'])->toBe($forms['DKF']['nama_pelapor'])
        ->and($deathData['death']['reporter']['nik'])->toBe($forms['DKF']['nik_pelapor'])
        ->and($deathData['death']['reporter']['occupation'])->toBe($forms['DKF']['pekerjaan_pelapor'])
        ->and($deathData['death']['reporter']['address'])->toBe($forms['DKF']['alamat_pelapor'])
        ->and($deathData['death']['deceased']['name'])->not->toBe($deathData['death']['deceased']['nik']);
});

it('seeds realistic values for the KIA and F-1.02 forms', function () {
    seedLetterFixtures();

    $kiaRequest = LetterRequest::where('request_code', 'SEED-REQ-SPKIA')->firstOrFail();
    $kia = $kiaRequest->form_data;

    expect($kia['nik_anak'])->toMatch('/^340000\d{10}$/')
        ->and($kia['nama_anak'])->not->toBeEmpty()
        ->and($kia['nama_anak'])->not->toBe($kia['nik_anak'])
        ->and($kia['tempat_lahir_anak'])->toBeIn(['Sleman', 'Yogyakarta', 'Bantul', 'Klaten'])
        ->and($kia['golongan_darah_anak'])->toBeIn(['A', 'B', 'AB', 'O'])
        ->and($kia['nomor_kk'])->toMatch('/^340400\d{10}$/')
        ->and($kia['nama_kepala_keluarga'])->not->toBeEmpty()
        ->and($kia['nomor_akta_kelahiran'])->toMatch('/^3471-LT-/')
        ->and($kia['agama_anak'])->toBe('Islam')
        ->and($kia['kewarganegaraan'])->toBe('WNI')
        ->and($kia['kelurahan'])->toBe('Bimomartani')
        ->and($kia['kecamatan'])->toBe('Ngemplak');

    $populationRequest = LetterRequest::where('request_code', 'SEED-REQ-SPKIAF')->firstOrFail();
    $population = $populationRequest->form_data;

    expect($population['applicant_name'])->not->toBeEmpty()
        ->and($population['applicant_nik'])->toMatch('/^340000\d{10}$/')
        ->and($population['kk_number'])->toMatch('/^340400\d{10}$/')
        ->and($population['application_types'])->toContain('child_card_new')
        ->and($population['attached_documents'])->toContain('old_family_card')
        ->and($population['application_date'])->not->toBeEmpty();
});

it('populates legacy marriage template blocks used by seeded previews', function () {
    seedLetterFixtures();

    $forms = LetterRequest::query()
        ->with('letterType')
        ->whereIn('request_code', ['SEED-REQ-SKKMP', 'SEED-REQ-SPNIP', 'SEED-REQ-SKTNB', 'SEED-REQ-SPOT'])
        ->get()
        ->keyBy(fn ($request) => $request->letterType->code)
        ->map(fn ($request) => $request->form_data);

    expect($forms['SKKMP']['deceased_name'])->not->toBeEmpty()
        ->and($forms['SKKMP']['spouse_name'])->not->toBeEmpty()
        ->and($forms['SKKMP']['spouse_name'])->not->toBe($forms['SKKMP']['deceased_name'])
        ->and($forms['SPNIP']['father_name'])->not->toBeEmpty()
        ->and($forms['SPNIP']['mother_name'])->not->toBeEmpty()
        ->and($forms['SKTNB']['spouse_name'])->not->toBeEmpty()
        ->and($forms['SPOT']['father_name'])->not->toBeEmpty()
        ->and($forms['SPOT']['mother_name'])->not->toBeEmpty()
        ->and($forms['SPOT']['child_name'])->not->toBeEmpty()
        ->and($forms['SPOT']['child_spouse_name'])->not->toBeEmpty();
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
