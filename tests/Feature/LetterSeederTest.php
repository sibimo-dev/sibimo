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

it('seeds a complete and connected letter fixture set', function () {
    seedLetterFixtures();

    expect(DB::table('letter_types')->count())->toBe(46)
        ->and(DB::table('letter_type_fields')->count())->toBeGreaterThan(0)
        ->and(DB::table('letter_type_documents')->count())->toBeGreaterThanOrEqual(92)
        ->and(DB::table('letter_number_sequences')->where('year', now()->year)->count())->toBe(46)
        ->and(DB::table('letter_requests')->where('request_code', 'like', 'SEED-REQ-%')->count())->toBe(46);

    expect(DB::table('letter_types')
        ->whereIn('code', ['SKBK', 'SKU', 'SKUM', 'SKD', 'SKTM', 'SKP', 'SKK', 'SKJ', 'SKCK', 'SKDPAK', 'SBP'])
        ->whereNotNull('blade_view')
        ->count())->toBe(11);

    expect(DB::table('letter_types')->whereNotNull('blade_view')->count())->toBe(46);
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

        if ($letterType->code === 'SKKM') {
            $pdf = $deathTemplateService->pdf(
                'death-certificate',
                $deathTemplateService->dataForRequest($request),
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
        if ($request->letterType->code === 'SKKM') {
            $pdf = $deathTemplateService->pdf(
                'death-certificate',
                $deathTemplateService->dataForRequest($request),
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
