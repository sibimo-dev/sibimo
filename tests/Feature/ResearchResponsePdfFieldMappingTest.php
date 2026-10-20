<?php

use App\Models\LetterRequest;
use App\Models\LetterType;
use App\Services\LetterPdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders stored legacy SBP NIM and study program values instead of canonical dash placeholders', function () {
    $letterType = LetterType::make([
        'code' => 'SBP',
        'letter_name' => 'Surat Balasan Penelitian',
        'blade_view' => 'letters.research-response-letter',
    ]);
    $letterType->setRelation('signer', null);

    $request = LetterRequest::make([
        'letter_number' => 'SBP/UJI/X/2026',
        'applicant_name' => 'Peneliti Sintetis',
        'applicant_nik' => '',
        'form_data' => [
            'nama_mahasiswa' => 'Peneliti Sintetis',
            'nim' => '-',
            'program_studi' => '-',
            'researcher_number' => 'NIM-SINTETIS-2026',
            'researcher_study_program' => 'Program Studi Sintetis',
            'fakultas' => 'Fakultas Sintetis',
            'ditujukan_kepada' => 'Universitas Sintetis',
            'nomor_surat_asal' => '001/RISET/2026',
        ],
    ]);
    $request->setRelation('letterType', $letterType);
    $request->setRelation('citizen', null);
    $request->setRelation('authorizedSigner', null);

    $data = app(LetterPdfService::class)->viewData($request, 'research-response-letter');

    expect($data['researcher']['nim'])->toBe('NIM-SINTETIS-2026')
        ->and($data['researcher']['study_program'])->toBe('Program Studi Sintetis');

    $bufferLevel = ob_get_level();
    try {
        $html = view('letters.research-response-letter', $data)->render();
    } finally {
        while (ob_get_level() > $bufferLevel) {
            ob_end_clean();
        }
    }

    expect($html)->toContain('NIM-SINTETIS-2026')
        ->and($html)->toContain('Program Studi Sintetis')
        ->and($html)->not->toContain('<td class="val">-</td>');
});

it('continues rendering NIM and study program from current SBP backend keys', function () {
    $letterType = LetterType::make(['code' => 'SBP', 'letter_name' => 'Surat Balasan Penelitian']);
    $letterType->setRelation('signer', null);
    $request = LetterRequest::make([
        'form_data' => [
            'nim' => 'NIM-KANONIK-2026',
            'program_studi' => 'Studi Kanonik',
        ],
    ]);
    $request->setRelation('letterType', $letterType);
    $request->setRelation('citizen', null);
    $request->setRelation('authorizedSigner', null);

    $data = app(LetterPdfService::class)->viewData($request, 'research-response-letter');

    expect($data['researcher']['nim'])->toBe('NIM-KANONIK-2026')
        ->and($data['researcher']['study_program'])->toBe('Studi Kanonik');
});
