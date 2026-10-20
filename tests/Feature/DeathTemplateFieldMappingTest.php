<?php

use App\Models\LetterRequest;
use App\Models\LetterType;
use App\Services\DeathTemplateService;

function deathTemplateRequest(string $code, array $form): LetterRequest
{
    $type = LetterType::make([
        'code' => $code,
        'letter_name' => 'Surat Kematian Uji',
        'number_prefix' => $code,
    ]);
    $type->setRelation('signer', null);

    $request = LetterRequest::make([
        'request_code' => 'TEST-' . $code,
        'applicant_name' => 'Nama Pemohon Uji',
        'applicant_nik' => '3400000000000001',
        'applicant_address' => 'Alamat Pemohon Uji',
        'applicant_phone' => '081234567890',
        'form_data' => $form,
    ]);
    $request->setRelation('letterType', $type);
    $request->setRelation('authorizedSigner', null);

    return $request;
}

it('maps modern death bundle form fields to their templates and renders every PDF', function () {
    $service = app(DeathTemplateService::class);
    $forms = [
        'death-certificate-application' => [
            'code' => 'DKA',
            'form' => [
                'familyCardNumber' => 'KK-NEW-01', 'headOfFamilyName' => 'Kepala Baru',
                'deceasedNik' => 'NIK-JENAZAH-NEW', 'deceasedName' => 'Jenazah Baru',
                'deceasedBirthPlace' => 'Kota Lahir Baru', 'deceasedBirthDate' => '1980-04-23',
                'deathPlace' => 'RS Kota Baru', 'deathDate' => '2024-03-10', 'deceasedGender' => 'Perempuan',
                'motherName' => 'Ibu Baru', 'motherNik' => 'NIK-IBU', 'motherAddress' => 'Alamat Ibu',
                'motherRt' => '004', 'motherRw' => '009', 'fatherName' => 'Ayah Baru',
                'fatherNik' => 'NIK-AYAH', 'fatherAddress' => 'Alamat Ayah',
            ],
            'contains' => ['Kota Lahir Baru, 23-04-1980', 'RS Kota Baru / 10-03-2024', 'Alamat Ibu, RT 004 / RW 009', '081234567890'],
        ],
        'death-report-form' => [
            'code' => 'DKF',
            'form' => [
                'familyCardNumber' => 'KK-NEW-02', 'headOfFamilyName' => 'Kepala Form Baru',
                'deceasedNik' => 'NIK-FORM', 'deceasedName' => 'Jenazah Form Baru',
                'deceasedGender' => 'Laki-laki', 'deceasedBirthPlace' => 'Kota Form',
                'deceasedBirthDate' => '1971-01-02', 'deceasedAge' => '53',
                'deceasedReligion' => 'Islam', 'deceasedOccupation' => 'Petani', 'deceasedAddress' => 'Alamat Jenazah',
                'deathDate' => '2024-05-06', 'deathTime' => '09:30', 'deathCause' => 'Sakit biasa/tua',
                'deathPlace' => 'Rumah Baru', 'deathCertifiedBy' => 'Dokter Baru',
                'motherName' => 'Ibu Form', 'motherBirthPlace' => 'Kota Ibu', 'motherBirthDate' => '1940-01-01',
                'motherNationality' => 'WNI', 'fatherName' => 'Ayah Form', 'fatherNationality' => 'WNI',
                'reporterName' => 'Pelapor Form', 'reporterBirthPlace' => 'Kota Pelapor',
                'reporterBirthDate' => '1985-08-09', 'reporterOccupation' => 'Guru', 'reporterAddress' => 'Alamat Pelapor',
            ],
            'contains' => ['Rumah Baru', 'WNI', 'Kota Form', '02-01-1971', 'Kota Pelapor, 09-08-1985'],
        ],
        'death-registration-report' => [
            'code' => 'DKC',
            'form' => [
                'familyCardNumber' => 'KK-NEW-03', 'headOfFamilyName' => 'Kepala Catat Baru',
                'deceasedNik' => 'NIK-CATAT', 'deceasedName' => 'Jenazah Catat Baru',
                'deceasedGender' => 'Perempuan', 'deceasedChildOrder' => 'Ketiga',
                'deceasedBirthPlace' => 'Kota Catat', 'deceasedBirthDate' => '1960-02-03', 'deceasedAge' => '64',
                'deathDate' => '2024-06-07', 'deathTime' => '11:12', 'deathCause' => 'Lainnya',
                'deathPlace' => 'Tempat Catat Baru', 'deathInformant' => 'Tenaga Kesehatan',
                'motherName' => 'Ibu Catat', 'motherBirthPlace' => 'Kota Ibu', 'motherBirthDate' => '1930-01-01',
                'motherAge' => '94', 'motherAddress' => 'Alamat Ibu', 'motherRt' => '002', 'motherRw' => '008',
                'witness1Name' => 'Saksi Satu', 'witness1Nik' => 'NIK-S1', 'witness1BirthDate' => '1980-01-01',
                'witness1Age' => '44', 'witness1Address' => 'Alamat Saksi',
            ],
            'contains' => ['Tempat Catat Baru', 'Kota Catat, 03-02-1960', 'Umur: 64', 'Alamat Ibu, RT 002 / RW 008'],
        ],
        'death-report' => [
            'code' => 'DKR',
            'form' => [
                'reporterNik' => 'NIK-PELAPOR', 'reporterName' => 'Pelapor Baru',
                'reporterBirthDate' => '1985-08-09', 'reporterAge' => '39', 'reporterOccupation' => 'Guru',
                'reporterAddress' => 'Alamat Pelapor', 'reporterRelation' => 'Anak',
                'deceasedName' => 'Jenazah Laporan', 'deceasedNik' => 'NIK-LAPORAN',
                'familyCardNumber' => 'KK-NEW-04', 'deceasedGender' => 'Laki-laki',
                'deceasedBirthPlace' => 'Kota Jenazah', 'deceasedReligion' => 'Islam',
                'deceasedOccupation' => 'Pensiunan', 'deceasedAddress' => 'Alamat Jenazah',
                'deceasedChildOrder' => 'Kedua', 'deathDate' => '2024-07-08', 'deathTime' => '12:30',
                'deathCause' => 'Lainnya', 'deathCauseDetail' => 'Rincian sebab baru',
                'deathInformant' => 'Dokter', 'deathPlace' => 'Rumah Sakit Baru', 'deathRegency' => 'Sleman Baru',
                'hamletHeadName' => 'Nama Kepala Dukuh Baru',
            ],
            'contains' => ['Anak', 'Rincian sebab baru', 'Rumah Sakit Baru', 'Sleman Baru', 'Nama Kepala Dukuh Baru', 'Pelapor Baru'],
        ],
        'death-certificate' => [
            'code' => 'SKKM',
            'form' => [
                'familyCardNumber' => 'KK-NEW-05', 'headOfFamilyName' => 'Kepala Sertifikat',
                'deceasedNik' => 'NIK-SKKM', 'deceasedName' => 'Jenazah Sertifikat',
                'deceasedBirthPlace' => 'Kota Sertifikat', 'deceasedBirthDate' => '1950-09-10',
                'deathDate' => '2024-08-09', 'deathTime' => '13:00', 'deathPlace' => 'Tempat Meninggal Baru',
                'deathLocation' => 'Tempat Kematian Baru', 'deathRegency' => 'Kabupaten Baru',
                'deathCause' => 'Sakit biasa/tua', 'deathCertifiedBy' => 'Dokter',
                'motherName' => 'Ibu Sertifikat', 'motherNationality' => 'WNI',
                'fatherName' => 'Ayah Sertifikat', 'fatherNationality' => 'WNI',
            ],
            'contains' => ['Jumat 09-08-2024', 'Tempat Meninggal Baru / Kabupaten Baru', 'Tempat Kematian Baru', 'WNI'],
        ],
        'death-general-statement' => [
            'code' => 'DKU',
            'form' => [
                'nama' => 'Nama Surat Umum', 'tempat_lahir' => 'Kota Surat Umum', 'tanggal_lahir' => '1991-03-04',
                'nik' => 'NIK-SURAT-UMUM', 'jenis_kelamin' => 'Perempuan', 'status_perkawinan' => 'Kawin',
                'agama' => 'Islam', 'pekerjaan' => 'Wiraswasta', 'alamat' => 'Alamat Surat Umum',
                'pergi_ke' => 'Kantor Dukcapil', 'deceasedName' => 'Jenazah Untuk Surat',
            ],
            'contains' => ['Kota Surat Umum, 04-03-1991', 'Perempuan', 'Kawin', 'Islam', 'Wiraswasta', 'Kantor Dukcapil'],
        ],
        'death-power-of-attorney' => [
            'code' => 'DKP',
            'form' => [
                'name' => 'Pemberi Kuasa Baru', 'occupation' => 'Pegawai', 'address' => 'Alamat Pemberi Kuasa',
                'attorneyName' => 'Penerima Kuasa Baru', 'attorneyOccupation' => 'Advokat',
                'attorneyAddress' => 'Alamat Penerima Kuasa', 'deceasedName' => 'Jenazah Kuasa',
            ],
            'contains' => ['Pemberi Kuasa Baru', 'Pegawai', 'Alamat Pemberi Kuasa', 'Penerima Kuasa Baru', 'Advokat', 'Alamat Penerima Kuasa'],
        ],
    ];

    foreach ($forms as $template => $fixture) {
        $request = deathTemplateRequest($fixture['code'], $fixture['form']);
        $data = $service->dataForRequest($request, $template);
        $html = view($service->viewName($template), $data)->render();
        foreach ($fixture['contains'] as $value) {
            expect($html)->toContain($value);
        }
        expect(strlen($service->pdf($template, $data)->output()))->toBeGreaterThan(1000);
    }
});

it('keeps legacy Indonesian death form keys mapped in every bundle template', function () {
    $service = app(DeathTemplateService::class);
    $forms = [
        'death-certificate-application' => ['DKA', [
            'nomor_kk' => 'KK-OLD-01', 'nama_kepala_keluarga' => 'Kepala Lama',
            'nik_jenazah' => 'NIK-LAMA', 'nama_jenazah' => 'Jenazah Lama',
            'ttl_jenazah' => 'Kota Lama, 12-01-1970', 'tempat_meninggal' => 'RS Lama',
            'tanggal_meninggal' => '2020-02-03', 'jenis_kelamin_jenazah' => 'Laki-laki',
            'nama_ibu' => 'Ibu Lama', 'alamat_ibu' => 'Alamat Ibu Lama', 'rt_ibu' => '001', 'rw_ibu' => '002',
        ], ['Kota Lama, 12-01-1970', 'RS Lama / 03-02-2020', 'Alamat Ibu Lama, RT 001 / RW 002']],
        'death-report-form' => ['DKF', [
            'nomor_kk' => 'KK-OLD-02', 'nama_kepala_keluarga' => 'Kepala Form Lama',
            'nik_jenazah' => 'NIK-FORM-LAMA', 'nama_jenazah' => 'Jenazah Form Lama',
            'jenis_kelamin_jenazah' => 'Perempuan', 'ttl_jenazah' => 'Kota Form Lama, 01-01-1960',
            'meninggal_hari_tanggal' => '2021-02-03', 'lokasi_meninggal' => 'Rumah Form Lama',
            'nama_ibu' => 'Ibu Form Lama', 'kewarganegaraan_ibu' => 'WNI',
        ], ['Rumah Form Lama', 'WNI', 'Kota Form Lama', '01-01-1960']],
        'death-registration-report' => ['DKC', [
            'nama_jenazah' => 'Jenazah Pencatatan Lama', 'jenis_kelamin_jenazah' => 'Laki-laki',
            'ttl_jenazah' => 'Kota Catat Lama, 02-02-1950', 'umur_jenazah' => '74',
            'tempat_kematian' => 'Lokasi Catat Lama', 'alamat_ibu' => 'Alamat Ibu PCK Lama',
            'rt_ibu' => '003', 'rw_ibu' => '007',
        ], ['Lokasi Catat Lama', 'Kota Catat Lama, 02-02-1950', 'Umur: 74', 'Alamat Ibu PCK Lama, RT 003 / RW 007']],
        'death-report' => ['DKR', [
            'nama_pelapor' => 'Pelapor Laporan Lama', 'ttl_pelapor' => 'Kota Pelapor Lama, 03-03-1980',
            'pekerjaan_pelapor' => 'Guru', 'hubungan_dengan_yang_meninggal' => 'Istri',
            'nama_jenazah' => 'Jenazah Laporan Lama', 'sebab_kematian' => 'Lainnya',
            'rincian_sebab' => 'Rincian sebab lama', 'tempat_meninggal' => 'Tempat Lama',
            'kab_kota_meninggal' => 'Kabupaten Lama', 'dukuh_name' => 'Dukuh Lama',
        ], ['Pelapor Laporan Lama', 'Istri', 'Rincian sebab lama', 'Tempat Lama', 'Kabupaten Lama', 'Dukuh Lama']],
        'death-certificate' => ['SKKM', [
            'nama_jenazah' => 'Jenazah SKKM Lama', 'meninggal_hari_tanggal' => '2022-04-05',
            'lokasi_meninggal' => 'Tempat Lama', 'tempat_kematian' => 'Lokasi Lama',
            'kota_tempat_meninggal' => 'Kabupaten Lama', 'nama_ibu' => 'Ibu SKKM Lama',
            'kewarganegaraan_ibu' => 'WNI',
        ], ['Selasa 05-04-2022', 'Tempat Lama / Kabupaten Lama', 'Lokasi Lama', 'WNI']],
        'death-general-statement' => ['DKU', [
            'nama' => 'Nama Umum Lama', 'tempat_lahir' => 'Kota Umum Lama', 'tanggal_lahir' => '1988-06-07',
            'jenis_kelamin' => 'Perempuan', 'status_perkawinan' => 'Cerai Hidup', 'agama' => 'Kristen',
            'pekerjaan' => 'Pedagang', 'pergi_ke' => 'Dukcapil Lama',
        ], ['Kota Umum Lama, 07-06-1988', 'Perempuan', 'Cerai Hidup', 'Kristen', 'Pedagang', 'Dukcapil Lama']],
        'death-power-of-attorney' => ['DKP', [
            'nama_pemberi_kuasa' => 'Pemberi Lama', 'pekerjaan_pemberi_kuasa' => 'Petani',
            'alamat_pemberi_kuasa' => 'Alamat Pemberi Lama', 'nama_penerima_kuasa' => 'Penerima Lama',
            'pekerjaan_penerima_kuasa' => 'Advokat', 'alamat_penerima_kuasa' => 'Alamat Penerima Lama',
        ], ['Pemberi Lama', 'Petani', 'Alamat Pemberi Lama', 'Penerima Lama', 'Advokat', 'Alamat Penerima Lama']],
    ];

    foreach ($forms as $template => [$code, $form, $expected]) {
        $request = deathTemplateRequest($code, $form);
        $data = $service->dataForRequest($request, $template);
        $html = view($service->viewName($template), $data)->render();
        foreach ($expected as $value) {
            expect($html)->toContain($value);
        }
        expect(strlen($service->pdf($template, $data)->output()))->toBeGreaterThan(1000);
    }
});

it('normalizes browser timezone TTL and maps the remaining death report fields for new and legacy requests', function () {
    $service = app(DeathTemplateService::class);
    $cases = [
        ['death-certificate-application', 'DKA', [
            'deceasedBirthPlaceDate' => 'Sleman, Wed Mar 10 1993 00:00:00 GMT+0700 (Western Indonesia Time)',
            'fatherAddress' => "Jl. Mawar 5\nRT 03 / RW 07", 'fatherRt' => '03', 'fatherRw' => '07',
        ], ['Sleman, 10-03-1993', 'Jl. Mawar 5 RT 03 / RW 07', 'Permohonan Akta Kematian']],
        ['death-registration-report', 'DKC', [
            'deceasedBirthPlaceDate' => 'Bantul, Wed Mar 10 1993 00:00:00 GMT+0700 (Western Indonesia Time)',
            'deceasedAge' => '64',
        ], ['Bantul, 10-03-1993', 'Umur: 64']],
        ['death-report', 'DKR', [
            'reporterRelation' => 'Anak',
        ], ['Hubungan dengan Yang Meninggal', 'Anak']],
        ['death-certificate', 'SKKM', [
            'reporterName' => 'Pelapor Baru', 'reporterBirthPlace' => 'Ngemplak', 'reporterBirthDate' => '1985-08-09',
        ], ['Ngemplak, 09-08-1985']],
        ['death-general-statement', 'DKU', [
            'nama' => 'Pelapor Lama', 'pergi_ke' => 'Dukcapil Bimomartani',
        ], ['Dukcapil Bimomartani']],
        ['death-certificate-application', 'DKA', [
            'ttl_jenazah' => 'Sleman, Wed Mar 10 1993 00:00:00 GMT+0700 (Western Indonesia Time)',
            'alamat_ayah' => "Jl. Kenanga 8\nRT 01 / RW 02", 'rt_ayah' => '01', 'rw_ayah' => '02',
        ], ['Sleman, 10-03-1993', 'Jl. Kenanga 8 RT 01 / RW 02']],
        ['death-registration-report', 'DKC', [
            'ttl_jenazah' => 'Bantul, Wed Mar 10 1993 00:00:00 GMT+0700 (Western Indonesia Time)', 'umur_jenazah' => '74',
        ], ['Bantul, 10-03-1993', 'Umur: 74']],
        ['death-report', 'DKR', [
            'hubungan_dengan_yang_meninggal' => 'Istri',
        ], ['Istri']],
        ['death-certificate', 'SKKM', [
            'nama_pelapor' => 'Pelapor Lama', 'ttl_pelapor' => 'Ngemplak, 09-08-1985',
        ], ['Ngemplak, 09-08-1985']],
        ['death-general-statement', 'DKU', [
            'nama' => 'Pelapor Lama', 'pergi_ke' => 'Dukcapil Lama',
        ], ['Dukcapil Lama']],
    ];

    foreach ($cases as [$template, $code, $form, $expected]) {
        $data = $service->dataForRequest(deathTemplateRequest($code, $form), $template);
        $html = view($service->viewName($template), $data)->render();
        foreach ($expected as $value) {
            expect($html)->toContain($value);
        }
        expect($html)->not->toContain('Western Indonesia Time');
        expect($html)->not->toContain('GMT+0700');
        expect(strlen($service->pdf($template, $data)->output()))->toBeGreaterThan(1000);
    }
});

it('renders sanitized snapshots of persisted legacy death request key layouts', function () {
    $service = app(DeathTemplateService::class);
    $records = [
        ['death-certificate-application', 'DKA', [
            'ttl_jenazah' => 'Batu Tue Jan 01 1980 00:00:00 GMT+0700 (Western Indonesia Time)',
            'nama_ayah' => 'Ayah Lama', 'alamat_ayah' => 'Alamat Ayah Lama, RT 01 / RW 02',
        ], ['Batu, 01-01-1980', 'Ayah Lama', 'RT 01 / RW 02']],
        ['death-registration-report', 'DKC', [
            'ttl_jenazah' => 'Batu Tue Jan 01 1980 00:00:00 GMT+0700 (Western Indonesia Time)',
            'umur_jenazah' => 74, 'deceased_age' => '74',
            'nama_ayah' => 'Ayah DKC', 'father_name' => 'Ayah DKC', 'alamat_ayah' => 'Alamat Ayah DKC',
            'nama_pelapor' => 'Pelapor DKC', 'reporter_name' => 'Pelapor DKC',
            'ttl_pelapor' => 'Sleman, 09-08-1985', 'reporter_birth_date' => '1985-08-09',
            'alamat_pelapor' => 'Alamat Pelapor DKC', 'reporter_address' => 'Alamat Pelapor DKC',
        ], ['Batu, 01-01-1980', 'Umur: 74', 'Ayah DKC', 'Pelapor DKC', 'Sleman, 09-08-1985']],
        ['death-certificate', 'SKKM', [
            'nama_pelapor' => 'Pelapor SKKM Lama', 'ttl_pelapor' => 'Sleman, 09-08-1985',
            'reporter_birth_date' => '1985-08-09', 'reporter_birth_place' => 'Sleman',
        ], ['Pelapor SKKM Lama', 'Sleman, 09-08-1985']],
        ['death-general-statement', 'DKU', [
            'name' => 'Pemohon DKU Lama', 'gender' => 'Perempuan', 'birth_place' => 'Sleman',
            'birth_date' => '1985-08-09', 'travel_to' => 'Dukcapil Bimomartani',
            'purpose' => 'Pengurusan akta kematian',
        ], ['Pemohon DKU Lama', 'Dukcapil Bimomartani']],
    ];

    foreach ($records as [$template, $code, $form, $expected]) {
        $data = $service->dataForRequest(deathTemplateRequest($code, $form), $template);
        $html = view($service->viewName($template), $data)->render();
        foreach ($expected as $value) {
            expect($html)->toContain($value);
        }
        expect($html)->not->toContain('GMT+0700');
        expect($html)->not->toContain('Western Indonesia Time');
        expect(strlen($service->pdf($template, $data)->output()))->toBeGreaterThan(1000);
    }
});

it('renders reporter identity fields in legacy SKKM and SPTJM records', function () {
    $service = app(DeathTemplateService::class);
    $records = [
        ['death-certificate', 'SKKM', [
            'nama_pelapor' => 'Pelapor SKKM Lama', 'ttl_pelapor' => 'Batu, 01-01-1980',
            'umur_pelapor' => 46, 'pekerjaan_pelapor' => 'Wiraswasta',
        ], ['Pelapor SKKM Lama', 'Batu, 01-01-1980']],
        ['death-data-statement', 'DKS', [
            'nik_pelapor' => 'NIK-P', 'nama_pelapor' => 'Penanda Tangan Lama',
            'ttl_pelapor' => 'Batu, 01-01-1980', 'umur_pelapor' => 46,
            'pekerjaan_pelapor' => 'Wiraswasta', 'alamat_pelapor' => 'Alamat Pelapor Lama',
            'nik_jenazah' => 'NIK-J', 'nama_jenazah' => 'Jenazah DKS Lama',
            'ttl_jenazah' => 'Sleman, 02-02-1940', 'meninggal_hari_tanggal' => '2026-10-18',
        ], ['Penanda Tangan Lama', 'Batu, 01-01-1980', '46', 'Wiraswasta', 'Jenazah DKS Lama']],
    ];

    foreach ($records as [$template, $code, $form, $expected]) {
        $data = $service->dataForRequest(deathTemplateRequest($code, $form), $template);
        $html = view($service->viewName($template), $data)->render();
        foreach ($expected as $value) {
            expect($html)->toContain($value);
        }
        expect(strlen($service->pdf($template, $data)->output()))->toBeGreaterThan(1000);
    }
});

it('uses current SPTJM signer form fields and derives age when the form has no age field', function () {
    $service = app(DeathTemplateService::class);
    $request = deathTemplateRequest('DKS', [
        'nik' => 'NIK-P', 'name' => 'Penanda Tangan Baru', 'birthPlace' => 'Batu',
        'birthDate' => '1990-01-01', 'occupation' => 'Guru', 'address' => 'Alamat Penanda Tangan Baru',
        'deceasedNik' => 'NIK-J', 'deceasedName' => 'Jenazah DKS Baru',
        'deceasedBirthPlace' => 'Sleman', 'deceasedBirthDate' => '1940-02-02',
        'deathDate' => '2026-10-18',
    ]);
    $request->authorized_at = \Illuminate\Support\Carbon::parse('2026-10-19');
    $data = $service->dataForRequest($request, 'death-data-statement');
    $html = view($service->viewName('death-data-statement'), $data)->render();

    expect($html)->toContain('Penanda Tangan Baru')
        ->toContain('Batu, 01-01-1990')
        ->toContain('36')
        ->toContain('Guru')
        ->toContain('Jenazah DKS Baru')
        ->and(strlen($service->pdf('death-data-statement', $data)->output()))->toBeGreaterThan(1000);
});
