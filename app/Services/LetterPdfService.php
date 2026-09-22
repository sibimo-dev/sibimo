<?php

namespace App\Services;

use App\Models\LetterRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Support\Carbon;


class LetterPdfService
{

    public const PAPER = 'folio';
    public const SIGNATURE_CITY = 'Bimomartani';
    public const SIGNATURE_LURAH = 'a.n LURAH BIMOMARTANI';
    public const SIGNATURE_LURAH_TITLE = 'LURAH BIMOMARTANI';

    public const VILLAGE_SUFFIX = 'Bimomartani, Ngemplak, Sleman';


    public function viewData(LetterRequest $letterRequest): array
    {
        $letterRequest->loadMissing(['citizen', 'letterType.signer', 'authorizedSigner']);

        $form = $letterRequest->form_data ?? [];
        $citizen = $letterRequest->citizen;
        $signer = $letterRequest->authorizedSigner ?? $letterRequest->letterType?->signer;
        $letterDate = $letterRequest->authorized_at ?? now();

        $birthPlace = $form['birth_place'] ?? $citizen?->birth_place;
        $birthDate = $form['birth_date'] ?? $citizen?->birth_date;
        $address = $this->fullAddress($letterRequest->applicant_address ?? $citizen?->address);

        return [
            // Dikirim dengan 2 nama key: 'number' (versi baru) dan 'nomor' (alias,
            // supaya blade lama yang masih pakai $nomor tidak error). Setelah semua
            // blade dipastikan pakai $number, alias 'nomor' ini boleh dihapus.
            'number' => $number = $letterRequest->letter_number
                ?? (($letterRequest->letterType?->number_prefix ?? '') . '......'),
            'nomor' => $number,

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
            ],

            // surat keterangan usaha
            'business' => [
                'type' => $form['business_type'] ?? null,
                'address' => $form['business_address'] ?? null,
            ],
            'purpose' => $form['purpose'] ?? null,
            'destination' => $form['destination_agency'] ?? null,

            // surat keterangan keramaian
            'event' => [
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

            // data mentah, kalau template lain butuh field di luar daftar di atas
            'form' => $form,

            // kotak digit kode wilayah untuk formulir F.1-25 / F.1-31
            'region' => $this->regionData(),
            'signature' => $this->signature($signer?->name, $signer?->position, $letterDate),
            'logo' => $this->asset('logo-sleman.png'),
            'kop' => $this->kopData($signer?->position),
        ];
    }


    public function sampleViewData(string $template = 'surat-keterangan-usaha'): array
    {
        $data = array_replace_recursive($this->sampleBase(), $this->sampleOverrides($template));

        $data['number'] = $data['nomor'] = $this->sampleNumber($template);
        $data['region'] = $this->regionData();

        $data['signature'] = $this->signature(
            $data['signer']['name'],
            $data['signer']['position'],
            $data['date'] ?? null,
        );
        $data['logo'] = $this->asset('logo-sleman.png');
        $data['kop'] = $this->kopData($data['signer']['position']);
        unset($data['date']);

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
            'surat-keterangan-jalan' => '471.21/',
            'fuel-recommendation-letter' => '471/',
            default => '581/ 1',
        };
    }

    private function signature(?string $name, ?string $position, $date): array
    {
        $parsed = $date ? Carbon::parse($date) : null;

        return [
            'city' => self::SIGNATURE_CITY,
            'date' => $parsed?->format('d/m/Y') ?? '',                              // 16/03/2026 atau ''
            'date_long' => $parsed?->locale('id')->translatedFormat('d F Y') ?? '',  // 16 Maret 2026 atau ''
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

        if (str_contains($p, 'urusan') || str_starts_with($p, 'kaur')) {
            return [self::SIGNATURE_LURAH, 'Carik', 'u.b.'];
        }

        return [self::SIGNATURE_LURAH];
    }

    private function birth(?string $place, $date): ?string
    {
        $formatted = $date ? Carbon::parse($date)->format('d/m/Y') : null;

        return collect([$place, $formatted])->filter()->implode(', ') ?: null;
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


    private function sampleBase(): array
    {
        return [
            'number' => null,
            'nomor' => null,
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
            ],
            'business' => [
                'type' => null,
                'address' => null,
            ],
            'purpose' => null,
            'destination' => null,
            'event' => [
                'date' => null, 'time' => null, 'place' => null,
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
            'reasons' => [],
            'witnesses' => [],
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

            'surat-keterangan-jalan', 'relocation-cover-letter', 'resident-arrival-form' => [],

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

            default => [],
        };
    }
}