<?php

use App\Models\LetterRequest;
use App\Models\LetterType;
use App\Models\Signer;
use App\Services\LetterPdfService;
use Illuminate\Support\Carbon;

it('maps generic applicant fields from the women bundle unmarried certificate into the bride block', function () {
    $request = new LetterRequest([
        'request_code' => 'DUMMY-MARRIAGE-SKBK2',
        'applicant_name' => 'Dummy Pemohon Lama',
        'letter_number' => 'DUMMY/001/2026',
        'form_data' => [
            'name' => 'Dummy Pemohon SKBK2',
            'nik' => '3400000000000001',
            'birthPlace' => 'Kota Contoh',
            'birthDate' => '1998-04-05',
            'gender' => 'Perempuan',
            'religion' => 'Islam',
            'occupation' => 'Guru',
            'address' => 'Jalan Contoh RT 02 RW 03',
            'education' => 'SMA',
            'nationality' => 'WNI',
        ],
    ]);
    $request->authorized_at = Carbon::parse('2026-06-15');
    $request->setRelation('citizen', null);
    $request->setRelation('attachments', collect());
    $signer = new Signer(['name' => 'Dummy Signer', 'position' => 'Kamituwa']);
    $request->setRelation('authorizedSigner', $signer);
    $letterType = new LetterType([
        'code' => 'SKBK2',
        'slug' => 'unmarried-certificate',
        'letter_name' => 'Surat Keterangan Belum Pernah Menikah',
    ]);
    $letterType->setRelation('signer', $signer);
    $request->setRelation('letterType', $letterType);

    $service = app(LetterPdfService::class);
    $data = $service->viewData($request, 'unmarried-certificate');
    $html = view($service->viewName('unmarried-certificate'), $data)->render();

    expect($html)->toContain('Dummy Pemohon SKBK2')
        ->and($html)->toContain('3400000000000001')
        ->and($html)->toContain('Kota Contoh, 05 April 1998')
        ->and($html)->toContain('Guru')
        ->and($html)->toContain('Jalan Contoh RT 02 RW 03')
        ->and($html)->toContain('Jabatan</td>')
        ->and($html)->toContain('Kamituwa')
        ->and($html)->not->toContain('Dummy Pemohon Lama');
});

it('uses the request applicant name when a legacy SKBK2 form omitted the name field', function () {
    $request = new LetterRequest([
        'request_code' => 'DUMMY-MARRIAGE-SKBK2-LEGACY',
        'applicant_name' => 'Dummy Legacy Applicant',
        'letter_number' => 'DUMMY/LEGACY/2026',
        'form_data' => [
            'birth_place' => 'Kota Lama', 'birth_date' => '1996-02-03',
            'gender' => 'Perempuan', 'religion' => 'Islam',
            'occupation' => 'Pedagang', 'address' => 'Jalan Lama RT 04 RW 05',
        ],
    ]);
    $request->authorized_at = Carbon::parse('2026-06-15');
    $request->setRelation('citizen', null);
    $request->setRelation('attachments', collect());
    $signer = new Signer(['name' => 'Dummy Signer', 'position' => 'Kamituwa']);
    $request->setRelation('authorizedSigner', $signer);
    $letterType = new LetterType([
        'code' => 'SKBK2', 'slug' => 'unmarried-certificate',
        'letter_name' => 'Surat Keterangan Belum Pernah Menikah',
    ]);
    $letterType->setRelation('signer', $signer);
    $request->setRelation('letterType', $letterType);

    $service = app(LetterPdfService::class);
    $html = view($service->viewName('unmarried-certificate'), $service->viewData($request, 'unmarried-certificate'))->render();
    expect($html)->toContain('Dummy Legacy Applicant')
        ->and($html)->toContain('Kota Lama, 03 Februari 1996')
        ->and($html)->toContain('Jalan Lama RT 04 RW 05');
});

it('renders every template in both marriage bundles with submitted dummy field values', function () {
    $templates = [
        'marriage-application-letter' => ['SPNIK', 'Dummy Groom Fresh'],
        'marriage-registration-data-sheet' => ['FPDN', 'Dummy Groom Fresh'],
        'marriage-introduction-letter' => ['SPNIP', 'Dummy Applicant Fresh'],
        'general-certificate-letter' => ['SKUMN', 'Dummy Applicant Fresh'],
        'marriage-lodging-certificate-letter' => ['SKTNB', 'Dummy Applicant Fresh'],
        'never-married-certificate-letter' => ['SKBKN', 'Dummy Applicant Fresh'],
        'not-remarried-statement-letter' => ['SPBML', 'Dummy Applicant Fresh'],
        'death-certificate-for-marriage-letter' => ['SKKMP', 'Dummy Deceased Fresh'],
        'bride-groom-consent-letter' => ['SPCCP', 'Dummy Groom Fresh'],
        'parental-consent-letter' => ['SPOT', 'Dummy Father Fresh'],
        'n2' => ['N2P', 'Dummy Groom Fresh'],
        'registration-form' => ['PNP', 'Dummy Bride Fresh'],
        'n1' => ['N1P', 'Dummy Bride Fresh'],
        'health-referral' => ['SPTKP', 'Dummy Health Applicant'],
        'marriage-lodging' => ['SKNNP', 'Dummy Bride Fresh'],
        'unmarried-certificate' => ['SKBK2', 'Dummy Bride Fresh'],
        'unmarried-statement' => ['SPBMLP', 'Dummy Statement Applicant'],
        'n6' => ['N6P', 'Dummy Ex Husband'],
        'n4' => ['N4P', 'Dummy Groom Fresh'],
        'n5' => ['N5P', 'Dummy Bride Fresh'],
        'guardian-statement' => ['SKWNP', 'Dummy Guardian Fresh'],
        'judge-guardian' => ['SKWHP', 'Wali nasab tidak diketahui'],
    ];

    $form = [
        'name' => 'Dummy Applicant Fresh', 'nik' => '3400000000000001',
        'address' => 'Jalan Dummy RT 01 RW 02', 'birthPlace' => 'Kota Dummy',
        'birthDate' => '1997-03-14', 'gender' => 'Perempuan', 'maritalStatus' => 'Perawan',
        'religion' => 'Islam', 'occupation' => 'Guru', 'education' => 'SMA', 'nationality' => 'WNI',
        'groomName' => 'Dummy Groom Fresh', 'groomNik' => '3400000000000002',
        'groomBirthPlace' => 'Kota Groom', 'groomBirthDate' => '1995-02-11',
        'groomNationality' => 'WNI', 'groomReligion' => 'Islam', 'groomOccupation' => 'Petani',
        'groomEducation' => 'SMA', 'groomAddress' => 'Jalan Groom RT 03 RW 04', 'groomBin' => 'Ayah Groom',
        'brideName' => 'Dummy Bride Fresh', 'brideNik' => '3400000000000003',
        'brideBirthPlace' => 'Kota Bride', 'brideBirthDate' => '1998-04-15',
        'brideNationality' => 'WNI', 'brideReligion' => 'Islam', 'brideOccupation' => 'Bidan',
        'brideEducation' => 'D3', 'brideAddress' => 'Jalan Bride RT 05 RW 06', 'brideBinti' => 'Ayah Bride',
        'brideStatus' => 'Perawan', 'groomStatus' => 'Jejaka',
        'akadDate' => '2026-08-20', 'akadTime' => '09:00', 'akadPlace' => 'Balai Nikah Dummy',
        'fatherName' => 'Dummy Father Fresh', 'fatherNik' => '3400000000000004',
        'fatherBirthPlace' => 'Kota Father', 'fatherBirthDate' => '1968-01-02',
        'fatherNationality' => 'WNI', 'fatherReligion' => 'Islam', 'fatherOccupation' => 'Petani',
        'fatherAddress' => 'Jalan Father RT 07 RW 08', 'fatherBin' => 'Kakek Dummy',
        'motherName' => 'Dummy Mother Fresh', 'motherNik' => '3400000000000005',
        'motherBirthPlace' => 'Kota Mother', 'motherBirthDate' => '1971-05-06',
        'motherNationality' => 'WNI', 'motherReligion' => 'Islam', 'motherOccupation' => 'Ibu Rumah Tangga',
        'motherAddress' => 'Jalan Mother RT 09 RW 10', 'motherBinti' => 'Kakek Dummy',
        'groomFatherName' => 'Dummy Groom Father', 'groomFatherNik' => '3400000000000021',
        'groomFatherBirthPlace' => 'Kota Groom Father', 'groomFatherBirthDate' => '1960-02-03',
        'groomFatherNationality' => 'WNI', 'groomFatherReligion' => 'Islam',
        'groomFatherOccupation' => 'Petani', 'groomFatherAddress' => 'Jalan Groom Father RT 21 RW 22',
        'groomFatherBin' => 'Dummy Groom Grandfather',
        'groomMotherName' => 'Dummy Groom Mother', 'groomMotherNik' => '3400000000000022',
        'groomMotherBirthPlace' => 'Kota Groom Mother', 'groomMotherBirthDate' => '1962-03-04',
        'groomMotherNationality' => 'WNI', 'groomMotherReligion' => 'Islam',
        'groomMotherOccupation' => 'Pedagang', 'groomMotherAddress' => 'Jalan Groom Mother RT 23 RW 24',
        'brideFatherName' => 'Dummy Bride Father', 'brideFatherNik' => '3400000000000023',
        'brideFatherBirthPlace' => 'Kota Bride Father', 'brideFatherBirthDate' => '1963-04-05',
        'brideFatherNationality' => 'WNI', 'brideFatherReligion' => 'Islam',
        'brideFatherOccupation' => 'Petani', 'brideFatherAddress' => 'Jalan Bride Father RT 25 RW 26',
        'brideFatherBin' => 'Dummy Bride Grandfather',
        'brideMotherName' => 'Dummy Bride Mother', 'brideMotherNik' => '3400000000000024',
        'brideMotherBirthPlace' => 'Kota Bride Mother', 'brideMotherBirthDate' => '1964-05-06',
        'brideMotherNationality' => 'WNI', 'brideMotherReligion' => 'Islam',
        'brideMotherOccupation' => 'Ibu Rumah Tangga', 'brideMotherAddress' => 'Jalan Bride Mother RT 27 RW 28',
        'brideMotherBinti' => 'Dummy Bride Maternal Grandfather',
        'deceasedName' => 'Dummy Deceased Fresh', 'deceasedNik' => '3400000000000006',
        'deceasedBirthPlace' => 'Kota Deceased', 'deceasedBirthDate' => '1980-06-07',
        'deceasedNationality' => 'WNI', 'deceasedReligion' => 'Islam', 'deceasedOccupation' => 'Pedagang',
        'deceasedAddress' => 'Jalan Deceased RT 11 RW 12', 'deathDate' => '2024-01-10', 'deathPlace' => 'Kota Dummy',
        'spouseName' => 'Dummy Spouse Fresh', 'spouseNik' => '3400000000000007',
        'spouseBirthPlace' => 'Kota Spouse', 'spouseBirthDate' => '1999-03-04',
        'spouseNationality' => 'WNI', 'spouseReligion' => 'Islam', 'spouseOccupation' => 'Guru',
        'spouseAddress' => 'Jalan Spouse RT 13 RW 14', 'applicantBin' => 'Orang Tua Pemohon',
        'spouseBin' => 'Orang Tua Pasangan',
        'deceasedBinti' => 'Ibu Deceased',
        'childName' => 'Dummy Child Fresh', 'childNik' => '3400000000000008',
        'childBirthPlace' => 'Kota Child', 'childBirthDate' => '2004-02-03',
        'childSpouseName' => 'Dummy Child Spouse Fresh', 'childSpouseNik' => '3400000000000009',
        'childSpouseBirthPlace' => 'Kota Child Spouse', 'childSpouseBirthDate' => '2005-04-06',
        'childNationality' => 'WNI', 'childReligion' => 'Islam', 'childOccupation' => 'Pelajar',
        'childAddress' => 'Jalan Child RT 35 RW 36',
        'childSpouseNationality' => 'WNI', 'childSpouseReligion' => 'Islam',
        'childSpouseOccupation' => 'Guru', 'childSpouseAddress' => 'Jalan Child Spouse RT 29 RW 30',
        'guardianName' => 'Dummy Guardian Fresh', 'guardianNik' => '3400000000000010',
        'guardianBirthPlace' => 'Kota Guardian', 'guardianBirthDate' => '1965-09-08',
        'guardianNationality' => 'WNI', 'guardianReligion' => 'Kristen', 'guardianOccupation' => 'Wiraswasta',
        'guardianAddress' => 'Jalan Guardian RT 15 RW 16', 'guardianBin' => 'Kakek Guardian',
        'guardianRelation' => 'Paman', 'guardianReason' => 'Wali nasab berhalangan',
        'judgeGuardianReason' => 'Wali nasab tidak diketahui keberadaannya',
        'conduct' => 'Baik', 'destination' => 'Puskesmas Dummy', 'need' => 'Pemeriksaan kesehatan',
        'validDate' => '2026-07-01', 'note' => 'Catatan dummy',
        'purpose' => 'Pengurusan pernikahan', 'behavior' => 'Baik', 'validUntil' => '2026-09-01',
        'additionalNote' => 'Keterangan tambahan dummy', 'registrationNumber' => 'REG-DUMMY-01',
        'registrationDate' => '2026-06-01', 'previousSpouseName' => 'Dummy Previous Spouse',
        'groomPrevName' => 'Dummy Groom Previous', 'groomPrevNik' => '3400000000000012',
        'groomPrevBirthPlace' => 'Kota Prev', 'groomPrevBirthDate' => '1990-03-04',
        'groomPrevNationality' => 'WNI', 'groomPrevReligion' => 'Islam',
        'groomPrevOccupation' => 'Pedagang', 'groomPrevAddress' => 'Jalan Prev RT 17 RW 18',
        'groomPrevParentName' => 'Dummy Parent Previous', 'groomPrevDeathDate' => '2023-03-02',
        'groomPrevDeathPlace' => 'Kota Meninggal',
        'bridePrevName' => 'Dummy Bride Previous', 'bridePrevNik' => '3400000000000013',
        'bridePrevBin' => 'Dummy Bride Previous Parent', 'bridePrevDeathDate' => '2022-04-03',
        'bridePrevDeathPlace' => 'Kota Bride Previous Death',
        'exHusbandName' => 'Dummy Ex Husband', 'exHusbandNik' => '3400000000000011',
        'exHusbandBin' => 'Dummy Ex Husband Father', 'exHusbandBirthPlace' => 'Kota Ex Husband',
        'exHusbandBirthDate' => '1979-07-08', 'exHusbandNationality' => 'WNI',
        'exHusbandReligion' => 'Kristen', 'exHusbandOccupation' => 'Wiraswasta',
        'exHusbandAddress' => 'Jalan Ex Husband RT 37 RW 38',
        'exHusbandDiedAt' => '2024-02-01', 'exHusbandDiedPlace' => 'Kota Lama',
        'numpangLetterNumber' => 'NUM-DUMMY-01', 'numpangDate' => '2026-06-02',
    ];

    $service = app(LetterPdfService::class);
    foreach ($templates as $slug => [$code, $expected]) {
        $caseForm = $form;
        $caseApplicantName = 'Dummy Applicant Fresh';
        if ($slug === 'registration-form') {
            // Fixture ini menguji render data suami terdahulu; status harus Janda.
            $caseForm['brideStatus'] = 'Janda';
        }
        if ($slug === 'health-referral') {
            foreach (array_keys($caseForm) as $key) {
                if (str_starts_with($key, 'bride')) {
                    unset($caseForm[$key]);
                }
            }
            $caseForm['name'] = 'Dummy Health Applicant';
            $caseForm['nik'] = '3400000000000091';
            $caseForm['address'] = 'Jalan Health RT 31 RW 32';
            $caseApplicantName = 'Dummy Health Applicant';
        } elseif ($slug === 'unmarried-statement') {
            foreach (array_keys($caseForm) as $key) {
                if (str_starts_with($key, 'bride')) {
                    unset($caseForm[$key]);
                }
            }
            $caseForm['name'] = 'Dummy Statement Applicant';
            $caseForm['nik'] = '3400000000000092';
            $caseForm['address'] = 'Jalan Statement RT 33 RW 34';
            $caseApplicantName = 'Dummy Statement Applicant';
        }
        $request = new LetterRequest([
            'request_code' => "DUMMY-MARRIAGE-{$code}",
            'applicant_name' => $caseApplicantName,
            'letter_number' => "DUMMY/{$code}/2026",
            'form_data' => $caseForm,
        ]);
        $request->authorized_at = Carbon::parse('2026-06-15');
        $request->setRelation('citizen', null);
        $request->setRelation('attachments', collect());
        $signer = new Signer(['name' => 'Dummy Signer', 'position' => 'Kamituwa']);
        $request->setRelation('authorizedSigner', $signer);
        $letterType = new LetterType([
            'code' => $code, 'slug' => $slug, 'letter_name' => $slug,
        ]);
        $letterType->setRelation('signer', $signer);
        $request->setRelation('letterType', $letterType);

        $data = $service->viewData($request, $slug);
        $view = $service->viewName($slug);
        $html = $service->previewHtml($view, $data);

        expect($html)->toBeString()->not->toBeEmpty()
            ->and(mb_strtoupper(strip_tags($html)))->toContain(mb_strtoupper($expected));

        $pdf = $service->pdf($view, $data)->output();
        expect($pdf)->toStartWith('%PDF-');

        if ($slug === 'health-referral') {
            $plain = mb_strtoupper(strip_tags($html));
            expect($plain)->toContain('PUSKESMAS DUMMY')
                ->and($plain)->toContain('3400000000000091')
                ->and($plain)->toContain('JALAN HEALTH RT 31 RW 32')
                ->and($plain)->toContain('PEMERIKSAAN KESEHATAN')
                ->and($plain)->toContain('CATATAN DUMMY')
                ->and($plain)->toContain('01 JULI 2026');
            expect($html)->toContain('Dummy Health Applicant')
                ->and($html)->toContain('Jalan Health RT 31 RW 32');
            expect($data['marriage']['bride']['nik'])->toBe('3400000000000091')
                ->and($data['marriage']['bride']['address'])->toBe('Jalan Health RT 31 RW 32');
        }

        if ($slug === 'general-certificate-letter') {
            $plain = mb_strtoupper(strip_tags($html));
            expect($plain)->toContain('PUSKESMAS DUMMY')
                ->and($plain)->toContain('3400000000000001')
                ->and($plain)->toContain('PENGURUSAN PERNIKAHAN')
                ->and($plain)->toContain('KETERANGAN TAMBAHAN DUMMY')
                ->and($plain)->toContain('01 SEPTEMBER 2026')
                ->and($plain)->toContain('BAIK');
        }

        if ($slug === 'marriage-registration-data-sheet') {
            $plain = mb_strtoupper(strip_tags($html));
            expect($plain)->toContain('DUMMY GROOM PREVIOUS')
                ->and($plain)->toContain('DUMMY PARENT PREVIOUS')
                ->and($plain)->toContain('02 MARET 2023')
                ->and($plain)->toContain('KOTA MENINGGAL')
                ->and($plain)->toContain('DUMMY GROOM FATHER')
                ->and($plain)->toContain('DUMMY GROOM MOTHER')
                ->and($plain)->toContain('DUMMY BRIDE FATHER')
                ->and($plain)->toContain('DUMMY BRIDE MOTHER')
                ->and($plain)->toContain('DUMMY BRIDE PREVIOUS')
                ->and($plain)->toContain('DUMMY BRIDE PREVIOUS PARENT')
                ->and($plain)->toContain('03 APRIL 2022')
                ->and($plain)->toContain('KOTA BRIDE PREVIOUS DEATH');
        }

        if ($slug === 'marriage-application-letter') {
            $plain = mb_strtoupper(strip_tags($html));
            expect($plain)->toContain('DUMMY BRIDE FRESH')
                ->and($plain)->toContain('20 AGUSTUS 2026')
                ->and($plain)->toContain('09:00')
                ->and($plain)->toContain('BALAI NIKAH DUMMY');
            expect($data['marriage']['akad']['day'])->toBe('Kamis')
                ->and($data['marriage']['bride_father']['name'])->toBe('Dummy Bride Father')
                ->and($data['marriage']['bride_father']['nik'])->toBe('3400000000000023')
                ->and($data['marriage']['bride_father']['citizenship'])->toBe('WNI')
                ->and($data['marriage']['bride_father']['occupation'])->toBe('Petani')
                ->and($data['marriage']['bride_father']['address'])->toBe('Jalan Bride Father RT 25 RW 26')
                ->and($data['marriage']['bride_mother']['name'])->toBe('Dummy Bride Mother')
                ->and($data['marriage']['bride_mother']['nik'])->toBe('3400000000000024')
                ->and($data['marriage']['bride_mother']['citizenship'])->toBe('WNI')
                ->and($data['marriage']['bride_mother']['occupation'])->toBe('Ibu Rumah Tangga')
                ->and($data['marriage']['bride_mother']['address'])->toBe('Jalan Bride Mother RT 27 RW 28')
                ->and($data['marriage']['guardian']['name'])->toBe('Dummy Guardian Fresh')
                ->and($data['marriage']['guardian']['nik'])->toBe('3400000000000010')
                ->and($data['marriage']['guardian']['citizenship'])->toBe('WNI')
                ->and($data['marriage']['guardian']['religion'])->toBe('Kristen')
                ->and($data['marriage']['guardian']['occupation'])->toBe('Wiraswasta')
                ->and($data['marriage']['guardian']['address'])->toBe('Jalan Guardian RT 15 RW 16');
            expect($data['marriage']['ex_husband']['bin'])->toBe('Dummy Ex Husband Father')
                ->and($data['marriage']['ex_husband']['nik'])->toBe('3400000000000011')
                ->and($data['marriage']['ex_husband']['birth'])->toBe('Kota Ex Husband, 08 Juli 1979')
                ->and($data['marriage']['ex_husband']['citizenship'])->toBe('WNI')
                ->and($data['marriage']['ex_husband']['religion'])->toBe('Kristen')
                ->and($data['marriage']['ex_husband']['occupation'])->toBe('Wiraswasta')
                ->and($data['marriage']['ex_husband']['address'])->toBe('Jalan Ex Husband RT 37 RW 38')
                ->and($data['marriage']['ex_husband']['died_at'])->toBe('2024-02-01')
                ->and($data['marriage']['ex_husband']['died_place'])->toBe('Kota Lama');
        }

        if ($slug === 'death-certificate-for-marriage-letter') {
            $plain = mb_strtoupper(strip_tags($html));
            expect($plain)->toContain('DUMMY SPOUSE FRESH')
                ->and($plain)->toContain('3400000000000007')
                ->and($plain)->toContain('KOTA SPOUSE, 04 MARET 1999');
        }

        if ($slug === 'parental-consent-letter') {
            $plain = mb_strtoupper(strip_tags($html));
            expect($plain)->toContain('DUMMY MOTHER FRESH')
                ->and($plain)->toContain('DUMMY CHILD FRESH')
                ->and($plain)->toContain('DUMMY CHILD SPOUSE FRESH');
            expect($html)->toContain('WNI')
                ->and($html)->toContain('Jalan Child RT 35 RW 36');
            expect($data['father']['nationality'])->toBe('WNI')
                ->and($data['mother']['nationality'])->toBe('WNI')
                ->and($data['child']['nationality'])->toBe('WNI')
                ->and($data['child_spouse']['nationality'])->toBe('WNI');
        }

        if ($slug === 'marriage-lodging-certificate-letter') {
            expect($html)->toContain('Orang Tua Pemohon')
                ->and($html)->toContain('Orang Tua Pasangan')
                ->and($data['applicant']['bin_or_binti'])->toBe('Orang Tua Pemohon')
                ->and($data['spouse']['binti'])->toBe('Orang Tua Pasangan');
        }

        if (in_array($slug, ['n1', 'n5'], true)) {
            $plain = mb_strtoupper(strip_tags($html));
            expect($plain)->toContain('DUMMY BRIDE FATHER')
                ->and($plain)->toContain('DUMMY BRIDE MOTHER');
        }

        if ($slug === 'registration-form') {
            $plain = mb_strtoupper(strip_tags($html));
            expect($plain)->toContain('DUMMY GROOM FRESH')
                ->and($plain)->toContain('DUMMY BRIDE FRESH')
                ->and($plain)->toContain('DUMMY BRIDE FATHER')
                ->and($plain)->toContain('DUMMY GUARDIAN FRESH');
            expect($plain)->toContain('KAMIS')
                ->and($plain)->toContain('WNI');
            expect($html)->toContain('Dummy Bride Father')
                ->and($html)->toContain('Dummy Bride Mother')
                ->and($html)->toContain('Dummy Guardian Fresh')
                ->and($html)->toContain('Dummy Ex Husband Father')
                ->and($html)->toContain('3400000000000011')
                ->and($html)->toContain('Kota Ex Husband, 08 Juli 1979')
                ->and($html)->toContain('Kristen')
                ->and($html)->toContain('Wiraswasta')
                ->and($html)->toContain('Jalan Ex Husband RT 37 RW 38')
                ->and($html)->toContain('2024-02-01')
                ->and($html)->toContain('Kota Lama');
        }

        if ($slug === 'guardian-statement') {
            $plain = mb_strtoupper(strip_tags($html));
            expect($plain)->toContain('DUMMY BRIDE FRESH')
                ->and($plain)->toContain('PAMAN')
                ->and($plain)->toContain('WALI NASAB BERHALANGAN');
            expect($plain)->toContain('WNI')
                ->and($plain)->toContain('WIRASWASTA');
            expect($html)->toContain('WNI')
                ->and($html)->toContain('Kristen')
                ->and($html)->toContain('Wiraswasta');
        }

        if ($slug === 'marriage-lodging') {
            expect($html)->toContain('Ayah Bride')
                ->and($data['marriage']['bride']['bin'])->toBe('Ayah Bride');
        }

        if ($slug === 'death-certificate-for-marriage-letter') {
            expect($html)->toContain('Ibu Deceased')
                ->and($data['deceased']['binti'])->toBe('Ibu Deceased');
        }

        if ($slug === 'unmarried-statement') {
            expect($data['marriage']['bride']['citizenship'])->toBe('WNI')
                ->and($data['marriage']['bride']['occupation'])->toBe('Guru')
                ->and($data['marriage']['bride']['nik'])->toBe('3400000000000092')
                ->and($data['marriage']['bride']['address'])->toBe('Jalan Statement RT 33 RW 34');
            expect($html)->toContain('3400000000000092')
                ->and($html)->toContain('Jalan Statement RT 33 RW 34');
        }

        if ($slug === 'n6') {
            expect($html)->toContain('Ayah Bride')
                ->and($html)->toContain('Dummy Ex Husband Father')
                ->and($html)->toContain('Jalan Ex Husband RT 37 RW 38')
                ->and($data['marriage']['ex_husband']['bin'])->toBe('Dummy Ex Husband Father')
                ->and($data['marriage']['ex_husband']['nik'])->toBe('3400000000000011')
                ->and($data['marriage']['ex_husband']['birth'])->toBe('Kota Ex Husband, 08 Juli 1979')
                ->and($data['marriage']['ex_husband']['citizenship'])->toBe('WNI')
                ->and($data['marriage']['ex_husband']['religion'])->toBe('Kristen')
                ->and($data['marriage']['ex_husband']['occupation'])->toBe('Wiraswasta')
                ->and($data['marriage']['ex_husband']['address'])->toBe('Jalan Ex Husband RT 37 RW 38')
                ->and($data['marriage']['ex_husband']['died_at'])->toBe('2024-02-01')
                ->and($data['marriage']['ex_husband']['died_place'])->toBe('Kota Lama');
        }
    }
});

it('keeps PNP groom separate from former husband and renders the expanded FPN data', function () {
    $render = function (array $form): array {
        $request = new LetterRequest([
            'request_code' => 'DUMMY-PNP-FORM',
            'applicant_name' => 'Dummy Applicant',
            'letter_number' => 'DUMMY/PNP/2026',
            'form_data' => $form,
        ]);
        $request->authorized_at = Carbon::parse('2026-06-15');
        $request->setRelation('citizen', null);
        $request->setRelation('attachments', collect());
        $signer = new Signer(['name' => 'Dummy Signer', 'position' => 'Kamituwa']);
        $request->setRelation('authorizedSigner', $signer);
        $letterType = new LetterType(['code' => 'PNP', 'slug' => 'registration-form', 'letter_name' => 'FPN']);
        $letterType->setRelation('signer', $signer);
        $request->setRelation('letterType', $letterType);

        $service = app(LetterPdfService::class);
        $data = $service->viewData($request, 'registration-form');

        $view = $service->viewName('registration-form');

        return [$data, $service->previewHtml($view, $data), $service->pdf($view, $data)->output()];
    };

    [$perawan, $perawanHtml, $perawanPdf] = $render([
        'groom_name' => 'Dummy Calon Suami',
        'groom_bin' => 'Dummy Ayah Calon Suami',
        'groom_nik' => '0000000000000001',
        'groom_birth_place' => 'Kota Contoh',
        'groom_birth_date' => '1995-02-11',
        'groom_citizenship' => 'WNI',
        'groom_religion' => 'Islam',
        'groom_occupation' => 'Guru',
        'groom_address' => 'Alamat Calon Suami Dummy',
        'bride_status' => 'Perawan',
        'ex_husband_name' => 'JANGAN TAMPIL Dummy Mantan Suami',
        'ex_husband_nik' => '0000000000000099',
        'ex_husband_nationality' => 'FORMER-NATIONALITY-MUST-NOT-PRINT',
        'kewarganegaraan_suami_terdahulu' => 'LEGACY-NATIONALITY-MUST-NOT-PRINT',
        'akad_date' => '2026-08-20',
        'akad_day' => '-',
    ]);

    expect($perawan['marriage']['groom']['name'])->toBe('Dummy Calon Suami')
        ->and($perawan['marriage']['ex_husband']['name'])->toBeNull()
        ->and($perawan['marriage']['ex_husband']['nik'])->toBeNull()
        ->and($perawan['marriage']['ex_husband']['citizenship'])->toBeNull()
        ->and($perawan['marriage']['akad']['day'])->toBe('Kamis')
        ->and($perawanHtml)->toContain('Dummy Calon Suami')
        ->and($perawanHtml)->not->toContain('JANGAN TAMPIL Dummy Mantan Suami')
        ->and($perawanHtml)->not->toContain('FORMER-NATIONALITY-MUST-NOT-PRINT')
        ->and($perawanHtml)->not->toContain('LEGACY-NATIONALITY-MUST-NOT-PRINT')
        ->and($perawanPdf)->toStartWith('%PDF-');

    [$fatherGuardian, $fatherGuardianHtml, $fatherGuardianPdf] = $render([
        'bride_status' => 'Perawan',
        'guardian_relation' => 'Ayah Kandung',
        'guardian_name' => 'MUST-NOT-PRINT-FATHER-GUARDIAN-BLOCK',
        'guardian_nik' => '0000000000000066',
        'guardian_citizenship' => 'WNI',
        'guardian_nationality' => 'WNI',
        'guardianNationality' => 'WNI',
        'kewarganegaraan_wali' => 'WNI',
        'guardian_occupation' => 'Petani',
        'guardian_reason' => 'Tidak digunakan untuk ayah kandung',
    ]);
    $guardianStart = strpos($fatherGuardianHtml, 'DATA WALI NIKAH');
    $groomStart = strpos($fatherGuardianHtml, 'DATA CATIN PUTRA', $guardianStart ?: 0);
    $fatherGuardianBlock = $guardianStart !== false && $groomStart !== false
        ? substr($fatherGuardianHtml, $guardianStart, $groomStart - $guardianStart)
        : $fatherGuardianHtml;
    expect($fatherGuardian['marriage']['guardian']['name'])->toBeNull()
        ->and($fatherGuardian['marriage']['guardian']['nik'])->toBeNull()
        ->and($fatherGuardian['marriage']['guardian']['citizenship'])->toBeNull()
        ->and($fatherGuardian['marriage']['guardian']['relation'])->toBe('Ayah Kandung')
        ->and($fatherGuardian['marriage']['guardian']['reason'])->toBeNull()
        ->and($fatherGuardianBlock)->not->toContain('WNI')
        ->and($fatherGuardianBlock)->not->toContain('MUST-NOT-PRINT-FATHER-GUARDIAN-BLOCK')
        ->and($fatherGuardianPdf)->toStartWith('%PDF-');

    [$janda, $jandaHtml, $jandaPdf] = $render([
        'groom_name' => 'Dummy Calon Suami',
        'bride_status' => 'Janda',
        'ex_husband_name' => 'Dummy Mantan Suami',
        'ex_husband_bin' => 'Dummy Ayah Mantan',
        'ex_husband_nik' => '0000000000000002',
        'ex_husband_birth_place' => 'Kota Mantan',
        'ex_husband_birth_date' => '1988-03-04',
        'ex_husband_citizenship' => 'Synthetic citizenship Janda',
        'ex_husband_religion' => 'Islam',
        'ex_husband_occupation' => 'Pedagang',
        'ex_husband_address' => 'Alamat Mantan Dummy',
        'ex_husband_died_at' => '2024-01-02',
        'ex_husband_died_place' => 'Kota Meninggal Dummy',
        'bride_father_name' => 'Dummy Ayah Calon Istri',
        'bride_father_bin' => 'Dummy Kakek Calon Istri',
        'bride_father_nik' => '0000000000000003',
        'bride_father_birth_place' => 'TTL Ayah Dummy',
        'bride_father_citizenship' => 'WNI',
        'bride_father_religion' => 'Islam',
        'bride_father_occupation' => 'Petani',
        'bride_father_address' => 'Alamat Ayah Dummy',
        'bride_mother_name' => 'Dummy Ibu Calon Istri',
        'bride_mother_bin' => 'Dummy Kakek Ibu',
        'bride_mother_nik' => '0000000000000004',
        'bride_mother_birth_place' => 'TTL Ibu Dummy',
        'bride_mother_citizenship' => 'WNI',
        'bride_mother_religion' => 'Islam',
        'bride_mother_occupation' => 'Pedagang',
        'bride_mother_address' => 'Alamat Ibu Dummy',
        'guardian_name' => 'Dummy Wali',
        'guardian_bin' => 'Dummy Orang Tua Wali',
        'guardian_nik' => '0000000000000005',
        'guardian_birth_place' => 'TTL Wali Dummy',
        'guardian_citizenship' => 'Synthetic guardian citizenship',
        'guardian_religion' => 'Islam',
        'guardian_occupation' => 'Wiraswasta',
        'guardian_address' => 'Alamat Wali Dummy',
        'guardian_relation' => 'Paman',
        'guardian_reason' => 'Wali nasab berhalangan',
        'judge_guardian_reason' => 'Wali hakim sesuai ketetapan',
    ]);

    expect($janda['marriage']['groom']['name'])->toBe('Dummy Calon Suami')
        ->and($janda['marriage']['ex_husband']['name'])->toBe('Dummy Mantan Suami')
        ->and($janda['marriage']['ex_husband']['citizenship'])->toBe('Synthetic citizenship Janda')
        ->and($janda['marriage']['bride_father']['name'])->toBe('Dummy Ayah Calon Istri')
        ->and($janda['marriage']['bride_father']['birth'])->toBe('TTL Ayah Dummy')
        ->and($janda['marriage']['bride_mother']['name'])->toBe('Dummy Ibu Calon Istri')
        ->and($janda['marriage']['bride_mother']['birth'])->toBe('TTL Ibu Dummy')
        ->and($janda['marriage']['guardian']['name'])->toBe('Dummy Wali')
        ->and($janda['marriage']['guardian']['nik'])->toBe('0000000000000005')
        ->and($janda['marriage']['guardian']['occupation'])->toBe('Wiraswasta')
        ->and($janda['marriage']['guardian']['address'])->toBe('Alamat Wali Dummy')
        ->and($janda['marriage']['guardian']['relation'])->toBe('Paman')
        ->and($janda['marriage']['guardian']['reason'])->toBe('Wali nasab berhalangan')
        ->and($jandaHtml)->toContain('Dummy Calon Suami')
        ->and($jandaHtml)->toContain('Dummy Mantan Suami')
        ->and($jandaHtml)->toContain('Dummy Ayah Calon Istri')
        ->and($jandaHtml)->toContain('Dummy Ibu Calon Istri')
        ->and($jandaHtml)->toContain('Dummy Wali')
        ->and($jandaHtml)->toContain('Synthetic guardian citizenship')
        ->and($jandaPdf)->toStartWith('%PDF-');
});

it('maps legacy Indonesian guardian, parent, and former-husband keys into marriage templates', function () {
    $legacyForm = [
        'nama_catin_putri' => 'Dummy Legacy Bride', 'nik_catin_putri' => '3400000000000101',
        'tempat_lahir_catin_putri' => 'Kota Legacy Bride', 'tanggal_lahir_catin_putri' => '1998-01-12',
        'kewarganegaraan_catin_putri' => 'WNI', 'agama_catin_putri' => 'Islam',
        'pekerjaan_catin_putri' => 'Bidan', 'pendidikan_catin_putri' => 'D3',
        'alamat_catin_putri' => 'Jalan Legacy Bride RT 01 RW 02', 'status_catin_putri' => 'Janda',
        'nama_catin_putra' => 'Dummy Legacy Groom', 'nik_catin_putra' => '3400000000000102',
        'tempat_lahir_catin_putra' => 'Kota Legacy Groom', 'tanggal_lahir_catin_putra' => '1995-02-13',
        'kewarganegaraan_catin_putra' => 'WNI', 'agama_catin_putra' => 'Islam',
        'pekerjaan_catin_putra' => 'Petani', 'alamat_catin_putra' => 'Jalan Legacy Groom RT 03 RW 04',
        'nama_ayah_putri' => 'Dummy Legacy Bride Father', 'bin_ayah_putri' => 'Kakek Legacy Bride',
        'nik_ayah_putri' => '3400000000000103', 'ttl_ayah_putri' => 'Kota Ayah Legacy, 02 Maret 1960',
        'kewarganegaraan_ayah_putri' => 'WNI', 'agama_ayah_putri' => 'Islam',
        'pekerjaan_ayah_putri' => 'Petani', 'alamat_ayah_putri' => 'Jalan Ayah Legacy RT 05 RW 06',
        'nama_ibu_putri' => 'Dummy Legacy Bride Mother', 'binti_ibu_putri' => 'Kakek Ibu Legacy',
        'nik_ibu_putri' => '3400000000000104', 'ttl_ibu_putri' => 'Kota Ibu Legacy, 04 April 1962',
        'kewarganegaraan_ibu_putri' => 'WNI', 'agama_ibu_putri' => 'Islam',
        'pekerjaan_ibu_putri' => 'Pedagang', 'alamat_ibu_putri' => 'Jalan Ibu Legacy RT 07 RW 08',
        'nama_wali' => 'Dummy Legacy Guardian', 'bin_wali' => 'Kakek Wali Legacy',
        'nik_wali' => '3400000000000105', 'ttl_wali' => 'Kota Wali Legacy, 06 Mei 1965',
        'kewarganegaraan_wali' => 'WNI', 'agama_wali' => 'Kristen',
        'pekerjaan_wali' => 'Wiraswasta', 'alamat_wali' => 'Jalan Wali Legacy RT 09 RW 10',
        'hubungan_wali' => 'Paman', 'sebab_wali_bukan_ayah' => 'Ayah kandung berhalangan',
        'sebab_wali_hakim' => 'Wali hakim karena ayah tidak diketahui',
        'nama_suami_terdahulu' => 'Dummy Legacy Former Husband',
        'bin_suami_terdahulu' => 'Ayah Legacy Former Husband',
        'nik_suami_terdahulu' => '3400000000000106',
        'ttl_suami_terdahulu' => 'Kota Mantan, 08 Juli 1979',
        'kewarganegaraan_suami_terdahulu' => 'WNI', 'agama_suami_terdahulu' => 'Kristen',
        'pekerjaan_suami_terdahulu' => 'Wiraswasta',
        'alamat_suami_terdahulu' => 'Jalan Mantan RT 11 RW 12',
        'suami_terdahulu_meninggal_pada' => '2024-02-01',
        'suami_terdahulu_meninggal_di' => 'Kota Lama',
    ];

    $render = function (string $code, string $slug, ?array $form = null) use ($legacyForm): array {
        $request = new LetterRequest([
            'request_code' => "DUMMY-LEGACY-{$code}", 'applicant_name' => 'Dummy Legacy Applicant',
            'letter_number' => "DUMMY/LEGACY/{$code}", 'form_data' => $form ?? $legacyForm,
        ]);
        $request->authorized_at = Carbon::parse('2026-06-15');
        $request->setRelation('citizen', null);
        $request->setRelation('attachments', collect());
        $signer = new Signer(['name' => 'Dummy Legacy Signer', 'position' => 'Kamituwa']);
        $request->setRelation('authorizedSigner', $signer);
        $letterType = new LetterType(['code' => $code, 'slug' => $slug, 'letter_name' => $slug]);
        $letterType->setRelation('signer', $signer);
        $request->setRelation('letterType', $letterType);

        $service = app(LetterPdfService::class);
        $data = $service->viewData($request, $slug);
        $html = $service->previewHtml($service->viewName($slug), $data);
        $pdf = $service->pdf($service->viewName($slug), $data)->output();

        return [$data, $html, $pdf];
    };

    [$registration, $registrationHtml, $registrationPdf] = $render('PNP', 'registration-form');
    expect($registration['marriage']['bride_father']['name'])->toBe('Dummy Legacy Bride Father')
        ->and($registration['marriage']['bride_father']['bin'])->toBe('Kakek Legacy Bride')
        ->and($registration['marriage']['bride_mother']['name'])->toBe('Dummy Legacy Bride Mother')
        ->and($registration['marriage']['bride_mother']['bin'])->toBe('Kakek Ibu Legacy')
        ->and($registration['marriage']['guardian']['name'])->toBe('Dummy Legacy Guardian')
        ->and($registration['marriage']['guardian']['religion'])->toBe('Kristen')
        ->and($registration['marriage']['guardian']['occupation'])->toBe('Wiraswasta')
        ->and($registration['marriage']['guardian']['reason'])->toBe('Ayah kandung berhalangan')
        ->and($registration['marriage']['judge_guardian_reason'])->toBe('Wali hakim karena ayah tidak diketahui')
        ->and($registration['marriage']['ex_husband']['bin'])->toBe('Ayah Legacy Former Husband')
        ->and($registration['marriage']['ex_husband']['nik'])->toBe('3400000000000106')
        ->and($registration['marriage']['ex_husband']['birth'])->toBe('Kota Mantan, 08 Juli 1979')
        ->and($registration['marriage']['ex_husband']['citizenship'])->toBe('WNI')
        ->and($registration['marriage']['ex_husband']['religion'])->toBe('Kristen')
        ->and($registration['marriage']['ex_husband']['occupation'])->toBe('Wiraswasta')
        ->and($registration['marriage']['ex_husband']['address'])->toBe('Jalan Mantan RT 11 RW 12')
        ->and($registration['marriage']['ex_husband']['died_at'])->toBe('2024-02-01')
        ->and($registration['marriage']['ex_husband']['died_place'])->toBe('Kota Lama')
        ->and($registrationHtml)->toContain('Dummy Legacy Guardian')
        ->and($registrationHtml)->toContain('Wiraswasta')
        ->and($registrationHtml)->toContain('Dummy Legacy Former Husband')
        ->and($registrationPdf)->toStartWith('%PDF-');

    [$guardian, $guardianHtml, $guardianPdf] = $render('SKWNP', 'guardian-statement');
    expect($guardian['marriage']['guardian']['name'])->toBe('Dummy Legacy Guardian')
        ->and($guardian['marriage']['guardian']['bin'])->toBe('Kakek Wali Legacy')
        ->and($guardian['marriage']['guardian']['nik'])->toBe('3400000000000105')
        ->and($guardian['marriage']['guardian']['birth'])->toBe('Kota Wali Legacy, 06 Mei 1965')
        ->and($guardian['marriage']['guardian']['citizenship'])->toBe('WNI')
        ->and($guardian['marriage']['guardian']['religion'])->toBe('Kristen')
        ->and($guardian['marriage']['guardian']['occupation'])->toBe('Wiraswasta')
        ->and($guardian['marriage']['guardian']['address'])->toBe('Jalan Wali Legacy RT 09 RW 10')
        ->and($guardian['marriage']['guardian']['relation'])->toBe('Paman')
        ->and($guardian['marriage']['guardian']['reason'])->toBe('Ayah kandung berhalangan')
        ->and($guardianHtml)->toContain('Wiraswasta')
        ->and($guardianHtml)->toContain('Ayah kandung berhalangan')
        ->and($guardianPdf)->toStartWith('%PDF-');

    $partialGuardianForm = $legacyForm;
    unset(
        $partialGuardianForm['nama_wali'],
        $partialGuardianForm['hubungan_wali'],
        $partialGuardianForm['sebab_wali_bukan_ayah'],
        $partialGuardianForm['sebab_wali_hakim'],
    );
    [$partialGuardian] = $render('SKWNP', 'guardian-statement', $partialGuardianForm);
    expect($partialGuardian['marriage']['guardian']['name'])->toBeNull()
        ->and($partialGuardian['marriage']['guardian']['religion'])->toBe('Kristen')
        ->and($partialGuardian['marriage']['guardian']['occupation'])->toBe('Wiraswasta')
        ->and($partialGuardian['marriage']['guardian']['relation'])->toBeNull()
        ->and($partialGuardian['marriage']['guardian']['reason'])->toBeNull();

    // Hybrid key set copied from the actual stored SKWNP payload shape;
    // values are synthetic so no personal data enters the regression fixture.
    $actualShape = [
        'guardian_name' => 'Dummy Actual Shape Guardian',
        'guardian_bin' => 'Dummy Actual Shape Guardian Father',
        'guardian_nik' => '3400000000000107',
        'guardian_birth_place' => 'Kota Actual Shape Guardian',
        'guardian_birth_date' => '1964-07-09',
        'guardian_occupation' => 'Guru',
        'guardian_address' => 'Jalan Actual Shape Guardian RT 13 RW 14',
        'guardian_relation' => 'Paman',
        'guardian_reason' => 'Ayah kandung berhalangan',
        'nama_wali' => 'Dummy Actual Shape Guardian',
        'bin_wali' => 'Dummy Actual Shape Guardian Father',
        'nik_wali' => '3400000000000107',
        'ttl_wali' => 'Kota Actual Shape Guardian, 09 Juli 1964',
        'kewarganegaraan_wali' => 'WNI',
        'agama_wali' => 'Katolik',
        'pekerjaan_wali' => 'Guru',
        'alamat_wali' => 'Jalan Actual Shape Guardian RT 13 RW 14',
        'hubungan_wali_nasab' => 'Paman',
    ];
    [$actualGuardian, $actualGuardianHtml, $actualGuardianPdf] = $render('SKWNP', 'guardian-statement', $actualShape);
    expect($actualGuardian['marriage']['guardian']['name'])->toBe('Dummy Actual Shape Guardian')
        ->and($actualGuardian['marriage']['guardian']['bin'])->toBe('Dummy Actual Shape Guardian Father')
        ->and($actualGuardian['marriage']['guardian']['nik'])->toBe('3400000000000107')
        ->and($actualGuardian['marriage']['guardian']['birth'])->toBe('Kota Actual Shape Guardian, 09 Juli 1964')
        ->and($actualGuardian['marriage']['guardian']['citizenship'])->toBe('WNI')
        ->and($actualGuardian['marriage']['guardian']['religion'])->toBe('Katolik')
        ->and($actualGuardian['marriage']['guardian']['occupation'])->toBe('Guru')
        ->and($actualGuardian['marriage']['guardian']['address'])->toBe('Jalan Actual Shape Guardian RT 13 RW 14')
        ->and($actualGuardian['marriage']['guardian']['relation'])->toBe('Paman')
        ->and($actualGuardianHtml)->toContain('Dummy Actual Shape Guardian')
        ->and($actualGuardianHtml)->toContain('3400000000000107')
        ->and($actualGuardianHtml)->toContain('Kota Actual Shape Guardian, 09 Juli 1964')
        ->and($actualGuardianHtml)->toContain('WNI')
        ->and($actualGuardianHtml)->toContain('Katolik')
        ->and($actualGuardianHtml)->toContain('Guru')
        ->and($actualGuardianHtml)->toContain('Jalan Actual Shape Guardian RT 13 RW 14')
        ->and($actualGuardianPdf)->toStartWith('%PDF-');

    // Bentuk payload yang diproduksi form baru: dua input TTL telah digabung
    // ke satu field katalog `ttl_wali` sebelum disimpan sebagai form_data.
    $mappedUiPayload = [
        'nama_wali' => 'Synthetic UI Guardian',
        'bin_wali' => 'Synthetic UI Parent',
        'nik_wali' => '0000000000000091',
        'ttl_wali' => 'Kota Sintetis 09-07-1964',
        'kewarganegaraan_wali' => 'Synthetic citizenship',
        'agama_wali' => 'Islam',
        'pekerjaan_wali' => 'Synthetic occupation',
        'alamat_wali' => 'Synthetic address',
        'hubungan_wali_nasab' => 'Paman',
    ];
    [$mappedUiGuardian, $mappedUiHtml, $mappedUiPdf] = $render('SKWNP', 'guardian-statement', $mappedUiPayload);
    expect($mappedUiGuardian['marriage']['guardian']['birth'])->toBe('Kota Sintetis 09-07-1964')
        ->and($mappedUiGuardian['marriage']['guardian']['citizenship'])->toBe('Synthetic citizenship')
        ->and($mappedUiGuardian['marriage']['guardian']['occupation'])->toBe('Synthetic occupation')
        ->and($mappedUiHtml)->toContain('Kota Sintetis 09-07-1964')
        ->and($mappedUiHtml)->toContain('Synthetic citizenship')
        ->and($mappedUiHtml)->toContain('Synthetic occupation')
        ->and($mappedUiPdf)->toStartWith('%PDF-');

    [$judgeGuardian, $judgeGuardianHtml, $judgeGuardianPdf] = $render('SKWHP', 'judge-guardian', [
        'nama_mempelai_wanita' => 'Synthetic Bride',
        'binti_mempelai_wanita' => 'Synthetic Parent Bride',
        'nik_mempelai_wanita' => '0000000000000092',
        'ttl_mempelai_wanita' => 'Tanjung Pinang Wed Mar 10 1993 00:00:00 GMT+0700 (Western Indonesia Time)',
        'kewarganegaraan_mempelai_wanita' => 'Synthetic citizenship bride',
        'agama_mempelai_wanita' => 'Islam',
        'pekerjaan_mempelai_wanita' => 'Synthetic occupation bride',
        'alamat_mempelai_wanita' => 'Synthetic address bride',
        'nama_mempelai_pria' => 'Synthetic Groom',
        'bin_mempelai_pria' => 'Synthetic Parent Groom',
        'nik_mempelai_pria' => '0000000000000093',
        'ttl_mempelai_pria' => 'Kota Sintetis, 10 Maret 1993',
        'kewarganegaraan_mempelai_pria' => 'Synthetic citizenship groom',
        'agama_mempelai_pria' => 'Islam',
        'pekerjaan_mempelai_pria' => 'Synthetic occupation groom',
        'alamat_mempelai_pria' => 'Synthetic address groom',
    ]);
    expect($judgeGuardian['marriage']['bride']['birth'])->toBe('Tanjung Pinang, 10 Maret 1993')
        ->and($judgeGuardian['marriage']['bride']['citizenship'])->toBe('Synthetic citizenship bride')
        ->and($judgeGuardian['marriage']['bride']['occupation'])->toBe('Synthetic occupation bride')
        ->and($judgeGuardian['marriage']['groom']['birth'])->toBe('Kota Sintetis, 10 Maret 1993')
        ->and($judgeGuardian['marriage']['groom']['citizenship'])->toBe('Synthetic citizenship groom')
        ->and($judgeGuardian['marriage']['groom']['occupation'])->toBe('Synthetic occupation groom')
        ->and($judgeGuardianHtml)->toContain('Tanjung Pinang, 10 Maret 1993')
        ->and($judgeGuardianHtml)->not->toContain('Western Indonesia Time')
        ->and($judgeGuardianHtml)->toContain('Synthetic citizenship bride')
        ->and($judgeGuardianHtml)->toContain('Synthetic occupation bride')
        ->and($judgeGuardianHtml)->toContain('Synthetic citizenship groom')
        ->and($judgeGuardianHtml)->toContain('Synthetic occupation groom')
        ->and($judgeGuardianPdf)->toStartWith('%PDF-');

    $legacyN6 = $legacyForm;
    foreach ([
        'nama_suami_terdahulu', 'bin_suami_terdahulu', 'nik_suami_terdahulu',
        'ttl_suami_terdahulu', 'kewarganegaraan_suami_terdahulu', 'agama_suami_terdahulu',
        'pekerjaan_suami_terdahulu', 'alamat_suami_terdahulu',
        'suami_terdahulu_meninggal_pada', 'suami_terdahulu_meninggal_di',
    ] as $key) {
        unset($legacyN6[$key]);
    }
    $legacyN6 += [
        'nama_almarhum' => 'Dummy Legacy Deceased Former Husband',
        'bin_binti_almarhum' => 'Ayah Legacy Deceased', 'nik_almarhum' => '3400000000000107',
        'ttl_almarhum' => 'Kota Almarhum, 10 Oktober 1978',
        'kewarganegaraan_almarhum' => 'WNI', 'agama_almarhum' => 'Kristen',
        'pekerjaan_almarhum' => 'Wiraswasta', 'alamat_almarhum' => 'Jalan Almarhum RT 13 RW 14',
        'tanggal_meninggal' => '2023-03-02', 'tempat_meninggal' => 'Kota Almarhum Meninggal',
        'nama_pasangan' => 'Dummy Legacy Widow', 'nik_pasangan' => '3400000000000108',
        'ttl_pasangan' => 'Kota Janda, 11 November 1980',
        'kewarganegaraan_pasangan' => 'WNI', 'agama_pasangan' => 'Islam',
        'pekerjaan_pasangan' => 'Bidan', 'alamat_pasangan' => 'Jalan Janda RT 15 RW 16',
    ];
    [$legacyN6Data, $legacyN6Html, $legacyN6Pdf] = $render('N6P', 'n6', $legacyN6);
    expect($legacyN6Data['marriage']['ex_husband']['name'])->toBe('Dummy Legacy Deceased Former Husband')
        ->and($legacyN6Data['marriage']['ex_husband']['bin'])->toBe('Ayah Legacy Deceased')
        ->and($legacyN6Data['marriage']['ex_husband']['nik'])->toBe('3400000000000107')
        ->and($legacyN6Data['marriage']['ex_husband']['birth'])->toBe('Kota Almarhum, 10 Oktober 1978')
        ->and($legacyN6Data['marriage']['ex_husband']['citizenship'])->toBe('WNI')
        ->and($legacyN6Data['marriage']['ex_husband']['religion'])->toBe('Kristen')
        ->and($legacyN6Data['marriage']['ex_husband']['occupation'])->toBe('Wiraswasta')
        ->and($legacyN6Data['marriage']['ex_husband']['address'])->toBe('Jalan Almarhum RT 13 RW 14')
        ->and($legacyN6Data['marriage']['ex_husband']['died_at'])->toBe('2023-03-02')
        ->and($legacyN6Data['marriage']['ex_husband']['died_place'])->toBe('Kota Almarhum Meninggal')
        ->and($legacyN6Html)->toContain('Dummy Legacy Deceased Former Husband')
        ->and($legacyN6Html)->toContain('Kota Almarhum Meninggal')
        ->and($legacyN6Pdf)->toStartWith('%PDF-');
});
