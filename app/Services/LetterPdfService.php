<?php

namespace App\Services;

use App\Models\LetterRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Support\Carbon;

/**
 * MERGE NOTE:
 * - Dasar file ini adalah hasil merge sebelumnya (fitur kamu + fitur teman:
 *   kodeForm/form_code, researcher, recipient/ref_number, lease, tembusan,
 *   date_parts, condition, KOP_ALWAYS_KALURAHAN_SLUGS, signaturePrefix()
 *   versi Carik-tanpa-u.b., husband/wife, family_members, attorney/deceased,
 *   dst).
 * - Di atas itu ditambahkan revisi terbarumu:
 *   1) BLANK_PLACEHOLDER + signature() memakai placeholder titik-titik
 *      (bukan string kosong) kalau surat belum authorized_at.
 *   2) $letterDate tidak lagi fallback ke now() di viewData() — tanggal TTD
 *      betul-betul kosong/placeholder kalau surat belum resmi ditandatangani.
 *      ('year' dan 'date_parts' TETAP fallback ke tanggal hari ini karena
 *      dipakai kalimat pembuka surat kuasa antar-warga yang tidak melalui
 *      proses authorized_at kalurahan — Carbon::parse(null) = now()).
 *   3) 3 section baru: travelOrder (SPPD), stayApplication (Permohonan
 *      Tinggal Sementara), residentRequest (SKTS) + helper method masing-
 *      masing + entri di sampleNumber()/sampleBase()/sampleOverrides().
 */
class LetterPdfService
{

    public const PAPER = 'folio';
    public const SIGNATURE_CITY = 'Bimomartani';
    public const SIGNATURE_LURAH = 'a.n LURAH BIMOMARTANI';
    public const SIGNATURE_LURAH_TITLE = 'LURAH BIMOMARTANI';

    public const VILLAGE_SUFFIX = 'Bimomartani, Ngemplak, Sleman';

    /** Placeholder titik-titik untuk field yang belum terisi (tanggal TTD, dll). */
    private const BLANK_PLACEHOLDER = '..........................';

    /**
     * Slug letter_type yang kopnya HARUS selalu format "PEMERINTAH KALURAHAN
     * BIMOMARTANI" biasa, walau signer surat ini di-assign sebagai Lurah
     * langsung (yang biasanya bikin kopData() beralih ke kop "LURAH
     * BIMOMARTANI" pribadi). Dipakai supaya kop dan TTD bisa berbeda: TTD
     * tetap ikut jabatan signer asli (bisa langsung Lurah, tanpa a.n/Carik),
     * tapi kop tidak ikut berubah.
     * ASUMSI: $letterType punya kolom/accessor 'slug' — sesuaikan nama
     * kolomnya kalau berbeda di model LetterType milikmu.
     */
    private const KOP_ALWAYS_KALURAHAN_SLUGS = [
        'permit-followup-letter',
        'surat-tindak-lanjut-izin',
        'lease-offer-letter',
        'surat-penawaran-sewa',
    ];


    public function viewData(LetterRequest $letterRequest): array
    {
        $letterRequest->loadMissing(['citizen', 'letterType.signer', 'authorizedSigner']);

        $form = $letterRequest->form_data ?? [];
        $citizen = $letterRequest->citizen;
        $signer = $letterRequest->authorizedSigner ?? $letterRequest->letterType?->signer;

        // Tidak lagi fallback ke now(): kalau surat belum resmi ditandatangani
        // (authorized_at masih null), tanggal pada blok TTD tampil placeholder
        // titik-titik (lihat signature()), bukan tanggal hari ini.
        $letterDate = $letterRequest->authorized_at;

        $birthPlace = $form['birth_place'] ?? $citizen?->birth_place;
        $birthDate = $form['birth_date'] ?? $citizen?->birth_date;
        $address = $this->fullAddress($letterRequest->applicant_address ?? $citizen?->address);

        // Kode formulir tetap (mis. "F-1.06" / "F.1.07"). Diisi ke dua nama
        // key ('form_code' dan 'kodeForm') karena blade lama & blade baru
        // pakai nama variabel yang berbeda untuk hal yang sama.
        $formCode = $letterRequest->letterType?->form_code ?? null;

        return [
            // Dikirim dengan 2 nama key: 'number' (versi baru) dan 'nomor' (alias,
            // supaya blade lama yang masih pakai $nomor tidak error). Setelah semua
            // blade dipastikan pakai $number, alias 'nomor' ini boleh dihapus.
            'number' => $number = $letterRequest->letter_number
                ?? (($letterRequest->letterType?->number_prefix ?? '') . '......'),
            'nomor' => $number,

            'form_code' => $formCode,
            'kodeForm' => $formCode,

            'signer' => [
                'name' => $signer?->name,
                'position' => $signer?->position,
            ],

            'applicant' => [
                'name' => $letterRequest->applicant_name,
                'birth' => $this->birth($birthPlace, $birthDate),
                'nik' => $letterRequest->applicant_nik,
                'kk_number' => $form['kk_number'] ?? $citizen?->kk_number,
                'gender' => $form['gender'] ?? $citizen?->gender,
                'marital_status' => $form['marital_status'] ?? $citizen?->marital_status,
                'religion' => $form['religion'] ?? $citizen?->religion,
                'occupation' => $form['occupation'] ?? $citizen?->occupation,
                'address' => $address,
                'rt' => $form['rt'] ?? $citizen?->rt,
                'rw' => $form['rw'] ?? $citizen?->rw,
                'phone' => $form['phone'] ?? $citizen?->phone,
                // surat pernyataan tidak memiliki dokumen kependudukan (F.1-04)
                'mother_name' => $form['mother_name'] ?? $citizen?->mother_name,
                'father_name' => $form['father_name'] ?? $citizen?->father_name,
            ],

            // surat keterangan usaha
            'business' => [
                'type' => $form['business_type'] ?? null,
                'address' => $form['business_address'] ?? null,
            ],
            'purpose' => $form['purpose'] ?? null,
            'destination' => $form['destination_agency'] ?? null,

            // surat keterangan keramaian + surat tindak lanjut permohonan izin
            'event' => [
                'day_name' => isset($form['event_date'])
                    ? Carbon::parse($form['event_date'])->locale('id')->translatedFormat('l')
                    : null,
                'date' => $form['event_date'] ?? null,
                'time' => $form['event_time'] ?? null,
                'place' => $form['event_place'] ?? null,
                'participants' => $form['event_participants'] ?? null,
                'objective' => $form['event_objective'] ?? null,
            ],
            'responsible_person' => $form['responsible_person'] ?? null,

            // sktm umum + sktm sekolah
            'income' => $this->rupiah($form['income'] ?? null),
            'category' => $form['category'] ?? null,
            'kkm_number' => $form['kkm_number'] ?? null,

            // sktm sekolah: data siswa (alamat default = alamat orangtua)
            'student' => [
                'name' => $form['student_name'] ?? null,
                'birth' => $this->birth($form['student_birth_place'] ?? null, $form['student_birth_date'] ?? null),
                'nik' => $form['student_nik'] ?? null,
                'gender' => $form['student_gender'] ?? null,
                'education' => $form['student_education'] ?? null,
                'class' => $form['student_class'] ?? null,
                'address' => isset($form['student_address'])
                    ? $this->fullAddress($form['student_address'])
                    : $address,
            ],

            // surat keterangan domisili (perusahaan/yayasan); pemilik = $applicant
            'company' => [
                'name' => $form['company_name'] ?? null,
                'activity' => $form['business_activity'] ?? null,
                'building_status' => $form['building_status'] ?? null,
                'building_use' => $form['building_use'] ?? null,
                'person_in_charge' => $form['person_in_charge'] ?? null,
                'employee_count' => $form['employee_count'] ?? null,
                'phone' => $form['phone'] ?? null,
                'address' => $form['domicile_address'] ?? null,
            ],

            // surat gugat cerai: pasangan yang digugat, alasan gugatan, dua saksi
            'spouse' => [
                'name' => $form['spouse_name'] ?? null,
                'birth' => $this->birth($form['spouse_birth_place'] ?? null, $form['spouse_birth_date'] ?? null),
                'occupation' => $form['spouse_occupation'] ?? null,
                'address' => isset($form['spouse_address']) ? $this->fullAddress($form['spouse_address']) : null,
                'marriage_cert_number' => $form['marriage_cert_number'] ?? null,
            ],
            'reasons' => $form['divorce_reasons'] ?? [],
            'witnesses' => collect($form['witnesses'] ?? [])->map(fn ($w) => [
                'name' => $w['name'] ?? null,
                'birth' => $this->birth($w['birth_place'] ?? null, $w['birth_date'] ?? null),
                'religion' => $w['religion'] ?? null,
                'occupation' => $w['occupation'] ?? null,
                'address' => isset($w['address']) ? $this->fullAddress($w['address']) : null,
            ])->all(),

            // surat pernyataan beda nama/identitas: identitas lain yang berbeda
            // dari data KTP pemohon (mis. tercantum di ijazah/akta, dsb)
            'other_identity' => [
                'name' => $form['other_name'] ?? null,
                'address' => isset($form['other_address']) ? $this->fullAddress($form['other_address']) : null,
                'nik' => $form['other_nik'] ?? null,
            ],

            // surat pernyataan tanggung jawab mutlak perkawinan belum tercatat
            'husband' => [
                'name' => $form['husband_name'] ?? null,
                'nik' => $form['husband_nik'] ?? null,
                'birth' => $this->birth($form['husband_birth_place'] ?? null, $form['husband_birth_date'] ?? null),
                'occupation' => $form['husband_occupation'] ?? null,
                'address' => isset($form['husband_address']) ? $this->fullAddress($form['husband_address']) : null,
            ],
            'wife' => [
                'name' => $form['wife_name'] ?? null,
                'nik' => $form['wife_nik'] ?? null,
                'birth' => $this->birth($form['wife_birth_place'] ?? null, $form['wife_birth_date'] ?? null),
                'occupation' => $form['wife_occupation'] ?? null,
                'address' => isset($form['wife_address']) ? $this->fullAddress($form['wife_address']) : null,
            ],
            'marriage_date' => $form['marriage_date'] ?? null,
            'marriage_witnesses' => collect($form['marriage_witnesses'] ?? [])->map(fn ($w) => [
                'name' => $w['name'] ?? null,
                'nik' => $w['nik'] ?? null,
            ])->all(),
            'children' => collect($form['children'] ?? [])->map(fn ($c) => [
                'name' => $c['name'] ?? null,
                'birth_cert_number' => $c['birth_cert_number'] ?? null,
                'shdk' => $c['shdk'] ?? null,
            ])->all(),

            // F-1.06 (population-document-statement-letter): rincian anggota
            // keluarga (sesuai KK) yang ditampilkan di tabel "dengan rincian
            // KK sebagai berikut".
            'family_members' => collect($form['family_members'] ?? [])->map(fn ($m) => [
                'name' => $m['name'] ?? null,
                'nik' => $m['nik'] ?? null,
                'shdk' => $m['shdk'] ?? null,
                'note' => $m['note'] ?? null,
            ])->all(),

            // F-1.06: rincian perubahan elemen "Pendidikan Terakhir" & "Pekerjaan"
            // (tabel A).
            'education_job_changes' => collect($form['education_job_changes'] ?? [])->map(fn ($c) => [
                'education' => [
                    'before' => $c['education_before'] ?? null,
                    'after' => $c['education_after'] ?? null,
                    'basis' => $c['education_basis'] ?? null,
                ],
                'job' => [
                    'before' => $c['job_before'] ?? null,
                    'after' => $c['job_after'] ?? null,
                    'basis' => $c['job_basis'] ?? null,
                ],
                'note' => $c['note'] ?? null,
            ])->all(),

            // F-1.06: rincian perubahan elemen "Agama" & elemen "Lainnya"
            // (tabel B). Nama elemen "Lainnya" (mis. "Status Perkawinan")
            // diisi lewat 'other_element_label', bukan per-baris, karena di
            // formulir aslinya cuma ditulis sekali di judul kolom.
            'religion_other_changes' => collect($form['religion_other_changes'] ?? [])->map(fn ($c) => [
                'religion' => [
                    'before' => $c['religion_before'] ?? null,
                    'after' => $c['religion_after'] ?? null,
                    'basis' => $c['religion_basis'] ?? null,
                ],
                'other' => [
                    'before' => $c['other_before'] ?? null,
                    'after' => $c['other_after'] ?? null,
                    'basis' => $c['other_basis'] ?? null,
                ],
                'note' => $c['note'] ?? null,
            ])->all(),
            'other_element_label' => $form['other_element_label'] ?? null,

            // surat balasan penelitian
            'researcher' => [
                'name' => $form['researcher_name'] ?? null,
                'nim' => $form['researcher_nim'] ?? null,
                'study_program' => $form['study_program'] ?? null,
                'faculty' => $form['faculty'] ?? null,
            ],

            // dipakai untuk "Kepada Yth" (surat balasan penelitian, surat tindak
            // lanjut permohonan izin, surat penawaran sewa, dll). 'recipient_address'
            // -> baris "Di :".
            'recipient' => $form['recipient_name'] ?? null,
            'recipient_address' => $form['recipient_address'] ?? null,
            'ref_number' => $form['ref_letter_number'] ?? null,

            // surat penawaran sewa kontrak gedung
            'lease' => [
                'building_name' => $form['lease_building_name'] ?? null,
                'building_address' => $form['lease_building_address'] ?? null,
                'duration_years' => $form['lease_duration_years'] ?? null,
            ],

            // daftar "Tembusan Dikirim Kepada" (mis. surat tindak lanjut
            // permohonan izin). Array bebas, dirender @foreach di blade
            // masing-masing surat lewat @section('tembusan').
            'tembusan' => $form['tembusan'] ?? [],

            // surat kuasa waris (heir-power-of-attorney-letter) & surat kuasa
            // pelayanan administrasi kependudukan (population-service-
            // authorization-letter) sama-sama pakai key 'attorney', jadi
            // field-nya digabung: 'gender' dipakai surat kuasa waris,
            // 'occupation' dipakai surat kuasa administrasi kependudukan.
            'attorney' => [
                'name' => $form['attorney_name'] ?? null,
                'nik' => $form['attorney_nik'] ?? null,
                'birth' => $this->birth($form['attorney_birth_place'] ?? null, $form['attorney_birth_date'] ?? null),
                'gender' => $form['attorney_gender'] ?? null,
                'occupation' => $form['attorney_occupation'] ?? null,
                'address' => isset($form['attorney_address']) ? $this->fullAddress($form['attorney_address']) : null,
            ],
            // surat kuasa waris: data almarhum/almarhumah pewaris
            'deceased' => [
                'name' => $form['deceased_name'] ?? null,
                'death_place' => $form['death_place'] ?? null,
            ],
            // surat kuasa administrasi kependudukan: alasan/kondisi pemberi
            // kuasa tidak bisa hadir sendiri
            'condition' => $form['condition'] ?? null,

            // pecahan hari/tanggal/bulan/tahun untuk kalimat pembuka surat kuasa
            // (mis. "Pada hari ini Rabu, Tanggal 23 Bulan September Tahun 2026").
            // Sengaja tetap fallback ke tanggal hari ini walau $letterDate null
            // (surat kuasa antar-warga tidak melalui proses authorized_at
            // kalurahan) — lihat dateParts().
            'date_parts' => $this->dateParts($letterDate),

            // tahun berjalan untuk baris tanggal yang sengaja dikosongkan
            // (diisi tangan) pada surat kuasa waris. Carbon::parse(null) = now(),
            // jadi tetap terisi tahun berjalan walau surat belum authorized_at.
            'year' => Carbon::parse($letterDate)->format('Y'),

            // surat perintah perjalanan dinas (SPPD)
            'travelOrder' => $this->travelOrderFromForm($form),

            // permohonan tinggal sementara
            'stayApplication' => $this->stayApplicationFromForm($form),

            // surat permohonan menjadi penduduk sementara (SKTS)
            'residentRequest' => $this->residentRequestFromForm($form),

            // data mentah, kalau template lain butuh field di luar daftar di atas
            'form' => $form,

            // nama kalurahan lengkap, dipakai di klausul "KHUSUS" surat kuasa
            'village' => self::VILLAGE_SUFFIX,

            // kotak digit kode wilayah untuk formulir F.1-25 / F.1-31
            'region' => $this->regionData(),
            'signature' => $this->signature($signer?->name, $signer?->position, $letterDate),
            'logo' => $this->asset('logo-sleman.png'),
            'kop' => $this->kopData(
                in_array($letterRequest->letterType?->slug, self::KOP_ALWAYS_KALURAHAN_SLUGS, true)
                    ? null
                    : $signer?->position
            ),
        ];
    }


    public function sampleViewData(string $template = 'surat-keterangan-usaha'): array
    {
        $data = array_replace_recursive($this->sampleBase(), $this->sampleOverrides($template));

        $data['number'] = $data['nomor'] = $this->sampleNumber($template);

        // Kode formulir: sampleFormCode() dulu (dipakai template lama seperti
        // population-document-statement-letter / unregistered-marriage-
        // responsibility-letter); kalau template tidak punya entri di situ,
        // pakai apa pun yang sudah di-set lewat sampleOverrides() (mis.
        // 'kodeForm' => 'F.1.07' untuk population-service-authorization-letter).
        $formCode = $this->sampleFormCode($template) ?? $data['form_code'] ?? $data['kodeForm'] ?? null;
        $data['form_code'] = $formCode;
        $data['kodeForm'] = $formCode;

        $data['region'] = $this->regionData();
        $data['village'] = self::VILLAGE_SUFFIX;
        $data['year'] = now()->format('Y');

        // Untuk preview, TTD/kop bisa pakai contoh jabatan berbeda dari yang
        // ditampilkan di body surat (mis. body sengaja dikosongkan menunggu
        // input asli, tapi TTD tetap perlu contoh lengkap untuk cek rantai
        // a.n Lurah/Carik/u.b.). Set 'signature_position' di sampleOverrides()
        // kalau butuh perbedaan ini; kalau tidak diset, keduanya tetap sama
        // seperti sebelumnya.
        $signaturePosition = $data['signature_position'] ?? $data['signer']['position'];

        // Kop surat dan TTD sebenarnya dua hal terpisah: sebuah surat bisa saja
        // ditandatangani langsung oleh Lurah (TTD tanpa "a.n") tapi kopnya tetap
        // memakai format "PEMERINTAH KALURAHAN BIMOMARTANI" biasa, bukan format
        // kop "LURAH BIMOMARTANI" pribadi. Default-nya kop tetap mengikuti
        // $signaturePosition seperti sebelumnya; set 'kop_position' di
        // sampleOverrides() kalau surat ini butuh kop yang berbeda dari TTD-nya.
        $kopPosition = $data['kop_position'] ?? $signaturePosition;

        $data['signature'] = $this->signature(
            $data['signer']['name'],
            $signaturePosition,
            $data['date'] ?? null,
        );
        $data['logo'] = $this->asset('logo-sleman.png');
        $data['kop'] = $this->kopData($kopPosition);
        $data['date_parts'] = $this->dateParts($data['date'] ?? null);
        unset($data['date'], $data['signature_position'], $data['kop_position']);

        return $data;
    }

    /** PDF asli hasil DomPDF (ini yang dicetak/di-download). */
    public function pdf(string $view, array $data): DomPdf
    {
        return Pdf::loadView($view, $data)->setPaper(self::PAPER, 'portrait');
    }


    public function previewHtml(string $view, array $data): string
    {
        $html = view($view, $data)->render();

        $screen = '<style>'
            . 'html{background:#d9d9d9}'
            . 'body{box-sizing:border-box;width:21.59cm;min-height:33.02cm;margin:20px auto;'
            . 'padding:2cm;background:#fff;box-shadow:0 0 8px rgba(0,0,0,.3)}'
            . '</style>';

        return str_replace('</head>', $screen . '</head>', $html);
    }

    /**
     * Kode wilayah administrasi untuk kotak digit di formulir F.1-25 / F.1-31
     * (@include('letters.region-code-grid')).
     */
    private function regionData(): array
    {
        return [
            'province_code'  => str_split('34'),
            'province_name'  => 'DI YOGYAKARTA',

            'regency_code'   => str_split('04'),
            'regency_name'   => 'SLEMAN',

            'district_code'  => str_split('11'),
            'district_name'  => 'NGEMPLAK',

            'village_code'   => str_split('2002'),
            'village_name'   => 'BIMOMARTANI',
        ];
    }


    /**
     * Data kop surat. Berbeda tergantung apakah penandatangan adalah
     * Lurah langsung, atau pejabat lain yang menandatangani "a.n. Lurah".
     */
    private function kopData(?string $position): array
    {
        $isLurah = $this->signedByLurah($position);

        return [
            'line1'   => 'PEMERINTAH KABUPATEN SLEMAN',
            'line2'   => 'KAPANEWON NGEMPLAK',
            'line3'   => $isLurah ? 'LURAH BIMOMARTANI' : 'PEMERINTAH KALURAHAN BIMOMARTANI',
            'address' => 'Jl.Prambanan Cangkringan, Km.6.5, Bimomartani. Ngemplak, Sleman, DIY',
            'contact' => 'Kode Pos : 55584   Telepon : 08112654981',
            'email'   => null, // isi kalau kalurahan sudah punya email resmi
            'is_lurah' => $isLurah, // dipakai base.blade untuk lebar gambar aksara (aksara-lurah.png lebih pendek)
            'aksara'  => $this->asset($isLurah ? 'aksara-lurah.png' : 'aksara-bimomartani.png'),
        ];
    }

    /**
     * Nomor contoh per-template untuk preview. Mendukung slug lama (bahasa
     * Indonesia) maupun slug baru (bahasa Inggris) — sesuaikan/rapikan daftar
     * ini kalau salah satu himpunan slug sudah tidak dipakai lagi.
     */
    private function sampleNumber(string $template): ?string
    {
        return match ($template) {
            'domicile-certificate', 'surat-keterangan-domisili' => '581/ 4',
            'sktm-school', 'sktm-sekolah' => '466/ 73',
            'sktm-general', 'sktm-umum' => '470/ 74',
            'relocation-cover-letter',
            'resident-arrival-form',
            'surat-keterangan-jalan',
            'travel-permit-letter',
            'travel-permit-letter-gov',
            'surat-keterangan-jalan-lurah',
            'travel-permit-letter-lurah',
            'travel-permit-letter-vill' => '471.21/',
            'fuel-recommendation-letter' => '471/',
            'population-document-statement-letter' => null, // pakai form_code, bukan nomor urut
            'heir-power-of-attorney-letter' => null, // surat kuasa antar-warga, tidak bernomor
            'research-response-letter' => null, // kosong, diisi manual/dari sibimo publik
            'marriage-certificate-duplicate-letter' => null, // kosong, diisi manual/dari sibimo publik
            'general-cover-letter' => null, // kosong, diisi manual/dari sibimo publik
            'permit-followup-letter', 'surat-tindak-lanjut-izin' => '010/',
            'lease-offer-letter', 'surat-penawaran-sewa' => '.........................',
            // Ketiga template baru punya nomor sendiri di dalam array
            // travelOrder/stayApplication/residentRequest masing-masing,
            // jadi $number/$nomor top-level ini tidak dipakai oleh view-nya.
            'duty-travel-order-letter',
            'temporary-stay-application-form',
            'temporary-resident-request' => null,
            default => '581/ 1',
        };
    }

    /**
     * Kode formulir tetap (mis. "F-1.06") untuk preview surat model formulir
     * resmi. Kembalikan null untuk surat keterangan bernomor urut biasa
     * (termasuk yang kode formulirnya di-set langsung lewat sampleOverrides(),
     * seperti population-service-authorization-letter).
     */
    private function sampleFormCode(string $template): ?string
    {
        return match ($template) {
            // FIX: kode formulir yang benar (sesuai contoh cetakan) adalah
            // "F-1.06", bukan "F.1-04".
            'population-document-statement-letter' => 'F-1.06',
            'unregistered-marriage-responsibility-letter' => 'F.1.07',
            default => null,
        };
    }

    private function signature(?string $name, ?string $position, $date): array
    {
        $parsed = $date ? Carbon::parse($date) : null;

        return [
            'city' => self::SIGNATURE_CITY,
            'date' => $parsed?->format('d/m/Y') ?? self::BLANK_PLACEHOLDER,
            'date_long' => $parsed?->locale('id')->translatedFormat('d F Y') ?? self::BLANK_PLACEHOLDER,
            'prefix' => $this->signaturePrefix($position),
            'position' => $this->signedByLurah($position) ? self::SIGNATURE_LURAH_TITLE : $position,
            'name' => $name,
        ];
    }

    /** true jika Lurah menandatangani sendiri (bukan "a.n LURAH" oleh Carik/Kaur/dll). */
    private function signedByLurah(?string $position): bool
    {
        $p = mb_strtolower((string) $position);

        if (str_contains($p, 'urusan') || str_starts_with($p, 'kaur')) {
            return false;
        }

        return str_contains($p, 'lurah') || str_contains($p, 'kepala desa');
    }

    private function signaturePrefix(?string $position): array
    {
        if ($this->signedByLurah($position)) {
            return [];
        }

        $p = mb_strtolower((string) $position);

        // Carik menandatangani langsung a.n. Lurah, tanpa rantai "u.b." lagi.
        if (str_contains($p, 'carik')) {
            return [self::SIGNATURE_LURAH];
        }

        // Semua pejabat lain (Kaur, Kamituwa, Jogoboyo, Ulu-Ulu, dst.) berada
        // di bawah Carik, sehingga rantainya: a.n Lurah -> Carik -> u.b. [jabatan].
        return [self::SIGNATURE_LURAH, 'Carik', 'u.b.'];
    }

    private function birth(?string $place, $date): ?string
    {
        $formatted = $date ? Carbon::parse($date)->format('d/m/Y') : null;

        return collect([$place, $formatted])->filter()->implode(', ') ?: null;
    }

    /**
     * Pecah satu tanggal jadi nama hari, tanggal, nama bulan, dan tahun
     * (semua berbahasa Indonesia) untuk kalimat pembuka surat kuasa.
     * $date null -> jatuh ke tanggal hari ini (surat kuasa antar-warga tidak
     * melalui proses authorized_at kalurahan).
     */
    private function dateParts($date): array
    {
        $parsed = $date ? Carbon::parse($date) : now();

        return [
            'day_name' => $parsed->locale('id')->translatedFormat('l'),    // contoh: Rabu
            'date_day' => $parsed->format('d'),                            // contoh: 23
            'month_name' => $parsed->locale('id')->translatedFormat('F'),  // contoh: September
            'year' => $parsed->format('Y'),                                // contoh: 2026
        ];
    }

    private function rupiah($value): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        return $digits === '' ? null : 'Rp. ' . number_format((int) $digits, 0, ',', '.') . ',-';
    }


    private function fullAddress(?string $address): ?string
    {
        $address = trim((string) $address);

        if ($address === '') {
            return null;
        }

        return stripos($address, 'bimomartani') !== false
            ? $address
            : $address . ', ' . self::VILLAGE_SUFFIX;
    }


    private function asset(string $file): string
    {
        $path = public_path("images/letters/{$file}");

        if (! is_file($path)) {
            throw new \RuntimeException("Aset surat tidak ditemukan: {$path}");
        }

        return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
    }

    /**
     * Petakan $form (form_data dari LetterRequest) ke struktur $travelOrder
     * yang dipakai duty-travel-order-letter.blade.php. Sesuaikan nama field
     * form ('travel_*') dengan skema form_data yang sebenarnya dipakai.
     */
    private function travelOrderFromForm(array $form): array
    {
        return [
            'issuingOfficial'     => $form['issuing_official'] ?? null,
            'employeeName'        => $form['employee_name'] ?? null,
            'employeeRank'        => $form['employee_rank'] ?? null,
            'employeePosition'    => $form['employee_position'] ?? null,
            'travelLevel'         => $form['travel_level'] ?? 'Biasa',
            'purpose'             => $form['travel_purpose'] ?? null,
            'transport'           => $form['transport'] ?? null,
            'departurePlace'      => $form['departure_place'] ?? null,
            'destinationPlace'    => $form['destination_place'] ?? null,
            'duration'            => $form['duration'] ?? null,
            'departureDate'       => $form['departure_date'] ?? null,
            'returnDueDate'       => $form['return_due_date'] ?? null,
            'companions'          => $form['companions'] ?? null,
            'budgetAgency'        => $form['budget_agency'] ?? null,
            'budgetItem'          => $form['budget_item'] ?? null,
            'notes'               => $form['travel_notes'] ?? null,
            'number'              => $form['sppd_number'] ?? null,
            'departureFrom'       => $form['departure_from'] ?? null,
            'departureTo'         => $form['departure_to'] ?? null,
            'departureOrderDate'  => $form['departure_order_date'] ?? null,
            'departureSigner'     => $form['departure_signer'] ?? null,
            'arrivalAt'           => $form['arrival_at'] ?? null,
            'arrivalFrom'         => $form['arrival_from'] ?? null,
            'arrivalDate'         => $form['arrival_date'] ?? null,
            'returnDepartureFrom' => $form['return_departure_from'] ?? null,
            'returnDepartureTo'   => $form['return_departure_to'] ?? null,
            'returnDepartureDate' => $form['return_departure_date'] ?? null,
            'arrivalSigner'       => $form['arrival_signer'] ?? null,
            'returnSigner'        => $form['return_signer'] ?? null,
            'returnArrivalAt'     => $form['return_arrival_at'] ?? null,
            'returnArrivalDate'   => $form['return_arrival_date'] ?? null,
        ];
    }

    /**
     * Petakan $form ke struktur $stayApplication yang dipakai
     * temporary-stay-application-form.blade.php.
     */
    private function stayApplicationFromForm(array $form): array
    {
        return [
            'regency'  => $form['regency'] ?? 'SLEMAN',
            'district' => $form['district'] ?? 'NGEMPLAK',
            'village'  => $form['village'] ?? 'BIMOMARTANI',

            'applicantName'    => $form['applicant_name'] ?? null,
            'applicantNik'     => $form['applicant_nik'] ?? null,
            'familyCardNumber' => $form['kk_number'] ?? null,

            'originAddress'    => $form['origin_address'] ?? null,
            'originVillage'    => $form['origin_village'] ?? null,
            'originDistrict'   => $form['origin_district'] ?? null,
            'originRegency'    => $form['origin_regency'] ?? null,
            'originProvince'   => $form['origin_province'] ?? null,

            'reason' => $form['reason'] ?? null,

            'destinationAddressLine1' => $form['destination_address_1'] ?? null,
            'destinationAddressLine2' => $form['destination_address_2'] ?? null,

            'parentName'     => $form['parent_name'] ?? null,
            'parentAddress'  => $form['parent_address'] ?? null,
            'parentVillage'  => $form['parent_village'] ?? null,
            'parentDistrict' => $form['parent_district'] ?? null,
            'parentRegency'  => $form['parent_regency'] ?? null,
            'parentProvince' => $form['parent_province'] ?? null,

            'guarantorName'     => $form['guarantor_name'] ?? null,
            'guarantorNik'      => $form['guarantor_nik'] ?? null,
            'guarantorAddress'  => $form['guarantor_address'] ?? null,
            'guarantorVillage'  => $form['guarantor_village'] ?? null,
            'guarantorDistrict' => $form['guarantor_district'] ?? null,
            'guarantorRegency'  => $form['guarantor_regency'] ?? null,
            'guarantorProvince' => $form['guarantor_province'] ?? null,

            'familyMembers' => $form['family_members'] ?? [],

            'letterDate' => $form['letter_date'] ?? null,

            'applicantSignerName' => $form['applicant_name'] ?? null,
            'applicantNumber'     => $form['applicant_number'] ?? null,
            'applicantDate'       => $form['applicant_date'] ?? null,

            'hamletHeadNumber' => $form['hamlet_head_number'] ?? null,
            'hamletHeadDate'   => $form['hamlet_head_date'] ?? null,
        ];
    }

    /**
     * Petakan $form ke struktur $residentRequest yang dipakai
     * temporary-resident-request.blade.php.
     */
    private function residentRequestFromForm(array $form): array
    {
        return [
            'applicantName' => $form['applicant_name'] ?? null,
            'applicantNik'  => $form['applicant_nik'] ?? null,
            'birthInfo'     => $this->birth($form['birth_place'] ?? null, $form['birth_date'] ?? null)
                ?? $form['birth_place'] ?? null,
            'gender'     => $form['gender'] ?? null,
            'occupation' => $form['occupation'] ?? null,
            'education'  => $form['education'] ?? null,

            'originAddress'  => $form['origin_address'] ?? null,
            'originVillage'  => $form['origin_village'] ?? null,
            'originDistrict' => $form['origin_district'] ?? null,
            'originRegency'  => $form['origin_regency'] ?? null,
            'originProvince' => $form['origin_province'] ?? null,

            'religion'      => $form['religion'] ?? null,
            'maritalStatus' => $form['marital_status'] ?? null,
            'reason'        => $form['reason'] ?? null,

            'destinationAddress' => $form['destination_address'] ?? null,
            'rt'     => $form['rt'] ?? null,
            'rw'     => $form['rw'] ?? null,
            'hamlet' => $form['hamlet'] ?? null,
            'village'  => $form['village'] ?? 'Bimomartani',
            'district' => $form['district'] ?? 'Ngemplak',

            'hostName'             => $form['host_name'] ?? null,
            'hostFamilyCardNumber' => $form['host_kk_number'] ?? null,
            'familyMemberCount'    => $form['family_member_count'] ?? null,

            'familyMembers' => $form['family_members'] ?? [],

            'letterDate' => $form['letter_date'] ?? null,

            'applicantSignerName' => $form['applicant_name'] ?? null,
            'applicantNumber'     => $form['applicant_number'] ?? null,
            'applicantDate'       => $form['applicant_date'] ?? null,

            'hamletHeadNumber' => $form['hamlet_head_number'] ?? null,
            'hamletHeadDate'   => $form['hamlet_head_date'] ?? null,
        ];
    }


    private function sampleBase(): array
    {
        return [
            'number' => null,
            'nomor' => null,
            'form_code' => null,
            'kodeForm' => null,
            'date' => now()->toDateString(),
            'signer' => [
                'name' => null,
                // Default Kaur (bukan Lurah) — surat yang tidak override 'signer'
                // (surat-keterangan-jalan, surat-keterangan-penghasilan,
                // sktm-sekolah) mengandalkan default ini supaya kop tampil
                // "PEMERINTAH KALURAHAN BIMOMARTANI" dan tanda tangan memakai
                // rantai 3-tingkat (a.n LURAH BIMOMARTANI / Carik / u.b. /
                // Kepala Urusan ...), BUKAN kop+ttd Lurah langsung.
                'position' => 'Kepala Urusan Tata Laksana',
            ],
            'applicant' => [
                'name' => null,
                'birth' => null,
                'nik' => null,
                'kk_number' => null,
                'gender' => null,
                'marital_status' => null,
                'religion' => null,
                'occupation' => null,
                'address' => null,
                'rt' => null,
                'rw' => null,
                'phone' => null,
                'mother_name' => null,
                'father_name' => null,
            ],
            'business' => [
                'type' => null,
                'address' => null,
            ],
            'purpose' => null,
            'destination' => null,
            'event' => [
                'day_name' => null, 'date' => null, 'time' => null, 'place' => null,
                'participants' => null, 'objective' => null,
            ],
            'responsible_person' => null,
            'income' => null,
            'category' => null,
            'kkm_number' => null,
            'student' => [
                'name' => null, 'birth' => null, 'nik' => null, 'gender' => null,
                'education' => null, 'class' => null, 'address' => null,
            ],
            'company' => [
                'name' => null, 'activity' => null, 'building_status' => null, 'building_use' => null,
                'person_in_charge' => null, 'employee_count' => null, 'phone' => null, 'address' => null,
            ],
            'spouse' => [
                'name' => null, 'birth' => null, 'occupation' => null,
                'address' => null, 'marriage_cert_number' => null,
            ],
            'other_identity' => [
                'name' => null, 'address' => null, 'nik' => null,
            ],
            'husband' => [
                'name' => null, 'nik' => null, 'birth' => null, 'occupation' => null, 'address' => null,
            ],
            'wife' => [
                'name' => null, 'nik' => null, 'birth' => null, 'occupation' => null, 'address' => null,
            ],
            'marriage_date' => null,
            'marriage_witnesses' => [null, null],
            'children' => [],
            'reasons' => [],
            'witnesses' => [],
            'researcher' => [
                'name' => null, 'nim' => null, 'study_program' => null, 'faculty' => null,
            ],
            'recipient' => null,
            'recipient_address' => null,
            'ref_number' => null,
            'tembusan' => [],
            'lease' => [
                'building_name' => null, 'building_address' => null, 'duration_years' => null,
            ],

            // F-1.06 (population-document-statement-letter)
            'family_members' => [],
            'education_job_changes' => [],
            'religion_other_changes' => [],
            'other_element_label' => null,

            // attorney digabung: 'gender' dipakai heir-power-of-attorney-letter,
            // 'occupation' dipakai population-service-authorization-letter.
            'attorney' => [
                'name' => null, 'nik' => null, 'birth' => null,
                'gender' => null, 'occupation' => null, 'address' => null,
            ],
            'deceased' => [
                'name' => null, 'death_place' => null,
            ],
            'condition' => null,

            // default kosong untuk 3 template baru; diisi penuh lewat
            // sampleOverrides() saat $template cocok.
            'travelOrder' => null,
            'stayApplication' => null,
            'residentRequest' => null,

            'form' => [],
        ];
    }

    private function sampleOverrides(string $template): array
    {
        return match ($template) {
            'surat-keterangan-domisili', 'domicile-certificate' => [
                'signer' => ['position' => 'Ulu - Ulu'],
            ],

            'sktm-sekolah', 'sktm-school' => [],

            'sktm-umum', 'sktm-general' => [
                'signer' => ['position' => 'Kepala Urusan Danarta'],
            ],

            'surat-keterangan-jalan', 'travel-permit-letter', 'travel-permit-letter-gov',
            'relocation-cover-letter', 'resident-arrival-form' => [],

            // Versi 2 Surat Keterangan Jalan: ditandatangani langsung oleh Lurah
            // (bukan a.n. LURAH oleh Kaur). kopData()/signature() otomatis pakai
            // kop+ttd Lurah begitu $signer['position'] mengandung 'lurah'.
            'surat-keterangan-jalan-lurah', 'travel-permit-letter-lurah',
            'travel-permit-letter-vill' => [
                'signer' => ['position' => 'Lurah'],
            ],

            'fuel-recommendation-letter' => [],

            // FIX: ditambahkan alias slug 'event-permit-letter' supaya
            // signer-nya jadi Jogoboyo, bukan jatuh ke default Kaur.
            'surat-keterangan-keramaian', 'event-permit-letter' => [
                'signer' => ['position' => 'Jogoboyo'],
            ],

            'surat-keterangan-penghasilan' => [],

            // FIX: ditambahkan alias slug 'business-permit-letter' supaya
            // signer-nya jadi Kamituwa, bukan jatuh ke default Kaur.
            'surat-keterangan-usaha', 'business-permit-letter' => [
                'signer' => ['position' => 'Kamituwa'],
            ],

            'divorce-lawsuit-letter' => [
                'signer' => ['position' => 'Carik'],
                // 2 slot kosong supaya form tetap menampilkan 2 baris saksi
                'witnesses' => [null, null],
            ],

            'unmarried-status-letter' => [
                'signer' => ['position' => 'Lurah'],
            ],

            'skck-referral-letter' => [
                'signer' => ['position' => 'Lurah'],
            ],

            'general-statement-letter' => [
                'signer' => ['position' => 'Lurah'],
            ],

            // Surat pernyataan sendiri oleh warga (bukan diterbitkan/ditandatangani
            // pejabat kalurahan) -- tidak perlu override 'signer'. Blade-nya
            // standalone (tidak extends letters.layouts.base) jadi otomatis
            // tanpa kop, tidak butuh flag apa pun di sini.
            'population-document-statement-letter' => [],
            'identity-discrepancy-statement-letter' => [],
            'unregistered-marriage-responsibility-letter' => [],

            // Surat kuasa antar-warga (pemberi & penerima kuasa) untuk mengurus
            // sidang waris -- standalone juga, tidak butuh kop/signer pejabat.
            'heir-power-of-attorney-letter' => [],

            // signer pakai default 'Kepala Urusan Tata Laksana' dari sampleBase(),
            // sesuai contoh surat (an Lurah Bimomartani / Carik / u.b. / Kaur Tata Laksana).
            // recipient, ref_number, dan researcher sengaja dikosongkan (null) —
            // data ini nanti datang dari inputan form sibimo publik, bukan data contoh.
            'research-response-letter' => [],

            // surat permohonan duplikat buku nikah — Jabatan di body sengaja
            // dikosongkan (null) karena nanti diisi dari inputan form sibimo
            // publik. TTD tetap pakai contoh 'Kamituwa' (via signature_position)
            // supaya rantai a.n Lurah/Carik/u.b. kelihatan lengkap di preview;
            // pada surat asli nanti keduanya otomatis sama-sama terisi dari
            // data signer yang sebenarnya.
            'marriage-certificate-duplicate-letter' => [
                'signer' => ['position' => null],
                'signature_position' => 'Kamituwa',
            ],

            // surat pengantar umum — Jabatan di body sengaja dikosongkan (null),
            // TTD tetap pakai contoh default 'Kepala Urusan Tata Laksana' (via
            // signature_position) supaya rantai a.n Lurah/Carik/u.b. kelihatan
            // lengkap di preview, sesuai contoh surat.
            'general-cover-letter' => [
                'signer' => ['position' => null],
                'signature_position' => 'Kepala Urusan Tata Laksana',
            ],

            // surat kuasa dalam pelayanan administrasi kependudukan — tidak
            // memakai kop/TTD berjenjang (bukan ditandatangani pejabat kalurahan),
            // jadi cukup set kode formulirnya saja. applicant, attorney, dan
            // condition sengaja dikosongkan (null), diisi dari inputan sibimo publik.
            'population-service-authorization-letter' => [
                'kodeForm' => 'F.1.07',
            ],

            // surat tindak lanjut permohonan izin — balasan Lurah atas surat
            // permohonan izin keramaian/acara warga. Body (Yth, No. surat
            // saudara, Hari/Tanggal/Waktu/Acara/Tempat, tembusan) sengaja
            // dikosongkan (null/[]), diisi dari inputan form sibimo publik.
            // Ditandatangani langsung oleh Lurah (bukan "a.n LURAH .. / Carik"),
            // jadi signer position di-set 'Lurah' supaya signedByLurah() true
            // dan TTD cukup menampilkan "LURAH BIMOMARTANI" tanpa baris a.n/Carik.
            // Tapi kop suratnya TETAP format "PEMERINTAH KALURAHAN BIMOMARTANI"
            // biasa (bukan kop "LURAH BIMOMARTANI" pribadi), jadi 'kop_position'
            // sengaja diisi posisi non-Lurah supaya kopData() tidak ikut
            // terpengaruh oleh signer TTD di atas.
            'permit-followup-letter', 'surat-tindak-lanjut-izin' => [
                'signer' => ['position' => 'Lurah'],
                'kop_position' => 'Kepala Urusan Tata Laksana',
            ],

            // surat penawaran sewa kontrak gedung — Kalurahan menawarkan
            // perpanjangan sewa sebuah gedung miliknya ke pihak penyewa lama.
            // Body (Yth, nama/jabatan/alamat penandatangan, tanggal berakhir
            // sewa, nominal & terbilang) sengaja TIDAK diambil dari $form —
            // sesuai arahan, bagian-bagian itu cukup ditulis dot placeholder
            // langsung di blade-nya. Ditandatangani langsung Lurah (tanpa
            // a.n/Carik), kop tetap format "PEMERINTAH KALURAHAN BIMOMARTANI".
            'lease-offer-letter', 'surat-penawaran-sewa' => [
                'signer' => ['position' => 'Lurah'],
                'kop_position' => 'Kepala Urusan Tata Laksana',
            ],

            // ============================================================
            // Surat Perintah Perjalanan Dinas (SPPD)
            // TEMPLATE KOSONG (tidak ada data contoh yang ditampilkan) —
            // semua field string di-set ke '' (bukan null, supaya blade yang
            // menulis {{ $x }} tanpa "?? ''" tidak error), dan tetap mengirim
            // array 'travelOrder' penuh (bukan dihapus) supaya key-key di
            // dalamnya tetap ada saat blade mengaksesnya.
            // ============================================================
            'duty-travel-order-letter' => [
                'signer' => ['position' => 'Kepala Urusan Tata Laksana'],
                'travelOrder' => [
                    'issuingOfficial'     => '',

                    'employeeName'        => '',
                    'employeeRank'        => '',
                    'employeePosition'    => '',
                    'travelLevel'         => '',

                    'purpose'             => '',
                    'transport'           => '',

                    'departurePlace'      => '',
                    'destinationPlace'    => '',

                    'duration'            => '',
                    'departureDate'       => '',
                    'returnDueDate'       => '',

                    'companions'          => '',

                    'budgetAgency'        => '',
                    'budgetItem'          => '',

                    'notes'               => '',

                    'number'              => '',
                    'departureFrom'       => '',
                    'departureTo'         => '',
                    'departureOrderDate'  => '',
                    'departureSigner'     => '',

                    'arrivalAt'           => '',
                    'arrivalFrom'         => '',
                    'arrivalDate'         => '',
                    'returnDepartureFrom' => '',
                    'returnDepartureTo'   => '',
                    'returnDepartureDate' => '',
                    'arrivalSigner'       => '',
                    'returnSigner'        => '',

                    'returnArrivalAt'     => '',
                    'returnArrivalDate'   => '',
                ],
            ],

            // ============================================================
            // Permohonan Tinggal Sementara — TEMPLATE KOSONG.
            // Semua field diisi '' (bukan dihapus / null), termasuk yang
            // sebelumnya punya default (regency/district/village) —
            // sengaja dikosongkan juga supaya benar-benar tidak ada data
            // apa pun yang tampil, murni kotak-kotak kosong template.
            // 'familyMembers' tetap array kosong [] (blade sudah menangani
            // 5 baris kosong lewat @for di template).
            // ============================================================
            'temporary-stay-application-form' => [
                'stayApplication' => [
                    'regency'  => '',
                    'district' => '',
                    'village'  => '',

                    'applicantName'    => '',
                    'applicantNik'     => '',
                    'familyCardNumber' => '',

                    'originAddress'    => '',
                    'originVillage'    => '',
                    'originDistrict'   => '',
                    'originRegency'    => '',
                    'originProvince'   => '',

                    'reason' => '',

                    'destinationAddressLine1' => '',
                    'destinationAddressLine2' => '',

                    'parentName'     => '',
                    'parentAddress'  => '',
                    'parentVillage'  => '',
                    'parentDistrict' => '',
                    'parentRegency'  => '',
                    'parentProvince' => '',

                    'guarantorName'     => '',
                    'guarantorNik'      => '',
                    'guarantorAddress'  => '',
                    'guarantorVillage'  => '',
                    'guarantorDistrict' => '',
                    'guarantorRegency'  => '',
                    'guarantorProvince' => '',

                    'familyMembers' => [],

                    'letterDate' => '',

                    'applicantSignerName' => '',
                    'applicantNumber'     => '',
                    'applicantDate'       => '',

                    'hamletHeadNumber' => '',
                    'hamletHeadDate'   => '',
                ],
            ],

            // ============================================================
            // Surat Permohonan Menjadi Penduduk Sementara (SKTS) —
            // TEMPLATE KOSONG, sama seperti di atas.
            // ============================================================
            'temporary-resident-request' => [
                'residentRequest' => [
                    'applicantName' => '',
                    'applicantNik'  => '',
                    'birthInfo'     => '',
                    'gender'        => '',
                    'occupation'    => '',
                    'education'     => '',

                    'originAddress'  => '',
                    'originVillage'  => '',
                    'originDistrict' => '',
                    'originRegency'  => '',
                    'originProvince' => '',

                    'religion'      => '',
                    'maritalStatus' => '',
                    'reason'        => '',

                    'destinationAddress' => '',
                    'rt'     => '',
                    'rw'     => '',
                    'hamlet' => '',
                    'village'  => '',
                    'district' => '',

                    'hostName'             => '',
                    'hostFamilyCardNumber' => '',
                    'familyMemberCount'    => '',

                    'familyMembers' => [],

                    'letterDate' => '',

                    'applicantSignerName' => '',
                    'applicantNumber'     => '',
                    'applicantDate'       => '',

                    'hamletHeadNumber' => '',
                    'hamletHeadDate'   => '',
                ],
            ],

            default => [],
        };
    }
}