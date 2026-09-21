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
            'nomor' => $letterRequest->letter_number
                ?? (($letterRequest->letterType?->number_prefix ?? '') . '......'),

            'signer' => [
                'name' => $signer?->name,
                'position' => $signer?->position,
            ],

            'applicant' => [
                'name' => $letterRequest->applicant_name,
                'birth' => $this->birth($birthPlace, $birthDate),
                'nik' => $letterRequest->applicant_nik,
                'gender' => $form['gender'] ?? $citizen?->gender,
                'marital_status' => $form['marital_status'] ?? $citizen?->marital_status,
                'religion' => $form['religion'] ?? $citizen?->religion,
                'occupation' => $form['occupation'] ?? $citizen?->occupation,
                'address' => $address,
            ],

            // surat keterangan usaha
            'business' => [
                'type' => $form['business_type'] ?? null,
                'address' => $form['business_address'] ?? null,
            ],
            'purpose' => $form['purpose'] ?? null,
            'destination' => $form['destination_agency'] ?? null,

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

            // data mentah, kalau template lain butuh field di luar daftar di atas
            'form' => $form,

            'signature' => $this->signature($signer?->name, $signer?->position, $letterDate),
            'logo' => $this->asset('logo-sleman.png'),
            'kop' => $this->kopData($signer?->position),
        ];
    }


    public function sampleViewData(string $template = 'surat-keterangan-usaha'): array
    {
        $data = array_replace_recursive($this->sampleBase(), $this->sampleOverrides($template));

        $data['signature'] = $this->signature(
            $data['signer']['name'],
            $data['signer']['position'],
            $data['date'],
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
     * Data kop surat. Berbeda tergantung apakah penandatangan adalah
     * Lurah langsung, atau pejabat lain yang menandatangani "a.n. Lurah".
     */
    private function kopData(?string $position): array
    {
        $isLurah = $this->signaturePrefix($position) === [];

        return [
            'line1'   => 'PEMERINTAH KABUPATEN SLEMAN',
            'line2'   => 'KAPANEWON NGEMPLAK',
            'line3'   => $isLurah ? 'LURAH BIMOMARTANI' : 'PEMERINTAH KALURAHAN BIMOMARTANI',
            'address' => 'Jl.Prambanan Cangkringan, Km.6.5, Bimomartani. Ngemplak, Sleman, DIY',
            'contact' => 'Kode Pos : 55584   Telepon : 08112654981',
            'email'   => null, // isi kalau kalurahan sudah punya email resmi
            'aksara'  => $this->asset($isLurah ? 'aksara-lurah.png' : 'aksara-bimomartani.png'),
        ];
    }

    private function signature(?string $name, ?string $position, $date): array
    {
        $date = Carbon::parse($date);

        return [
            'city' => self::SIGNATURE_CITY,
            'date' => $date->format('d/m/Y'),                              // 16/03/2026
            'date_long' => $date->locale('id')->translatedFormat('d F Y'), // 16 Maret 2026
            'prefix' => $this->signaturePrefix($position),
            'position' => $position,
            'name' => $name,
        ];
    }

    private function signaturePrefix(?string $position): array
    {
        $p = mb_strtolower((string) $position);

        if (str_contains($p, 'urusan') || str_starts_with($p, 'kaur')) {
            return [self::SIGNATURE_LURAH, 'Carik', 'u.b.'];
        }

        if (str_contains($p, 'lurah') || str_contains($p, 'kepala desa')) {
            return [];
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
            'nomor' => '581/ 1',
            'date' => '2026-01-06',
            'signer' => [
                'name' => 'Rasyifa Anom Sudaryono Amd, Kes',
                'position' => 'Kepala Urusan Tata Laksana',
            ],
            'applicant' => [
                'name' => 'Sumini Wulandari',
                'birth' => $this->birth('Sleman', '1975-03-14'),
                'nik' => '3404015403750001',
                'gender' => 'Perempuan',
                'marital_status' => 'Cerai Mati',
                'religion' => 'Islam',
                'occupation' => 'Mengurus Rumah Tangga',
                'address' => 'Kalibulus RT 02 RW 05, Bimomartani, Ngemplak, Sleman',
            ],
            'business' => [
                'type' => 'Warung Makan',
                'address' => 'Kalibulus RT 02 RW 05 Bimomartani Ngemplak Sleman',
            ],
            'purpose' => 'Pengajuan KUR',
            'destination' => 'BRI Ngemplak 2',
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
            'form' => [],
        ];
    }

    private function sampleOverrides(string $template): array
    {
        return match ($template) {
            'surat-keterangan-domisili' => [
                'nomor' => '581/ 4',
                'date' => '2026-03-16',
                'signer' => ['name' => 'Yordan Ardi Tamara, S.Kom', 'position' => 'Ulu - Ulu'],
                'applicant' => [
                    'name' => 'Bambang Prasetyo',
                    'birth' => $this->birth('Sleman', '1978-05-15'),
                    'nik' => '3404011505780002',
                    'gender' => 'Laki-Laki',
                    'marital_status' => 'Kawin',
                    'religion' => 'Islam',
                    'occupation' => 'Wiraswasta',
                    'address' => 'Ngemplak Asem RT 02 RW 26, Bimomartani, Ngemplak, Sleman',
                ],
                'company' => [
                    'name' => 'PT Griya Nusantara Properti',
                    'activity' => 'PROPERTI',
                    'building_status' => 'Milik Sendiri',
                    'building_use' => 'Kantor',
                    'person_in_charge' => 'Bambang Prasetyo',
                    'employee_count' => '19',
                    'phone' => '081234567890',
                    'address' => 'Pondok Dawung, RT 04 RW 23, Bimomartani, Ngemplak, Sleman, DIY',
                ],
            ],

            'sktm-sekolah' => [
                'nomor' => '466/ 73',
                'date' => '2026-07-30',
                'applicant' => [
                    'name' => 'Slamet Riyadi',
                    'birth' => $this->birth('Sleman', '1980-08-17'),
                    'nik' => '3404011708800003',
                    'gender' => 'Laki-Laki',
                    'marital_status' => 'Kawin Tercatat',
                    'religion' => 'Islam',
                    'occupation' => 'Buruh Harian Lepas',
                    'address' => 'Pondok Suruh, Bimomartani, Ngemplak, Sleman',
                ],
                'income' => $this->rupiah(2000000),
                'student' => [
                    'name' => 'Rafi Aditya Riyadi',
                    'birth' => $this->birth('Sleman', '2014-02-09'),
                    'nik' => '3404010902140001',
                    'gender' => 'Laki-Laki',
                    'education' => 'SD',
                    'class' => 'Kelas 5 / Semester 1',
                    'address' => 'Pondok Suruh, Bimomartani, Ngemplak, Sleman',
                ],
            ],

            'sktm-umum' => [
                'nomor' => '470/ 74',
                'date' => '2026-08-05',
                'signer' => ['name' => 'Nanda Mutiara Dewi S.Psi.', 'position' => 'Kepala Urusan Danarta'],
                'applicant' => [
                    'name' => 'Agus Hermawan',
                    'birth' => $this->birth('Sleman', '1985-11-02'),
                    'nik' => '3404010211850004',
                    'gender' => 'Laki-Laki',
                    'marital_status' => 'Kawin Tercatat',
                    'religion' => 'Islam',
                    'occupation' => 'Karyawan Honorer',
                    'address' => 'Balong, Bimomartani, Ngemplak, Sleman',
                ],
                'purpose' => 'Persyaratan Beasiswa PIP An. Dimas Pratama',
                'destination' => 'SDN Karanganyar',
            ],

            default => [],
        };
    }
}