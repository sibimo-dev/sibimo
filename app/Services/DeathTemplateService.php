<?php

namespace App\Services;

use App\Models\LetterRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Support\Carbon;

/**
 * Rendering service khusus dokumen persuratan kematian.
 *
 * Service ini sengaja berdiri sendiri supaya data dan template kematian tidak
 * mengubah kontrak LetterPdfService yang dipakai modul pernikahan.
 */
class DeathTemplateService
{
    public const PAPER = 'folio';

    private const TEMPLATES = [
        'death-report-form' => 'Formulir Pelaporan Kematian',
        'death-certificate' => 'Surat Keterangan Kematian',
        'death-report' => 'Laporan Kematian',
        'death-general-statement' => 'Surat Keterangan',
        'death-certificate-application' => 'Permohonan Akta Kematian',
        'death-registration-report' => 'Pelaporan Pencatatan Kematian',
        'death-power-of-attorney' => 'Surat Kuasa',
        'death-commemoration-calculation' => 'Perhitungan Selamatan Kematian',
        'death-data-statement' => 'SPTJM Kebenaran Data Kematian',
    ];

    private const NO_KOP_TEMPLATES = [
        'death-report-form',
        'death-report',
        'death-certificate-application',
        'death-registration-report',
        'death-power-of-attorney',
        'death-commemoration-calculation',
        'death-data-statement',
    ];

    public function hasTemplate(string $template): bool
    {
        return array_key_exists($template, self::TEMPLATES);
    }

    public function viewName(string $template): string
    {
        abort_unless($this->hasTemplate($template), 404);

        return 'letters.death.' . $template;
    }

    public function includeKop(string $template): bool
    {
        return ! in_array($template, self::NO_KOP_TEMPLATES, true);
    }

    public function pdf(string $template, array $data): DomPdf
    {
        return Pdf::loadHtml($this->renderDocumentHtml($template, $data))
            ->setPaper(self::PAPER, 'portrait');
    }

    /** Data kosong untuk melihat bentuk formulir sebelum ada request. */
    public function sampleData(string $template = 'death-certificate'): array
    {
        return [
            'document_title' => self::TEMPLATES[$template] ?? 'Surat Kematian',
            'title' => self::TEMPLATES[$template] ?? 'Surat Kematian',
            'logo' => $this->asset('logo-sleman.png'),
            'include_kop' => $this->includeKop($template),
            'kop' => [
                'line1' => 'PEMERINTAH KABUPATEN SLEMAN',
                'line2' => 'KAPANEWON NGEMPLAK',
                'line3' => 'PEMERINTAH KALURAHAN BIMOMARTANI',
                'address' => 'Jl.Prambanan Cangkringan, Km.6.5, Bimomartani. Ngemplak, Sleman, DIY',
                'contact' => 'Kode Pos : 55584   Telepon : 08112654981',
                'email' => null,
                'aksara' => $this->asset('aksara-bimomartani.png'),
            ],
            'number' => '',
            'nomor' => '',
            'request' => [
                'code' => '',
                'source' => '',
                'notes' => '',
            ],
            'death' => $this->blankDeathData(),
            'signature' => [
                'city' => 'Bimomartani',
                'date' => '',
                'prefix' => ['a.n LURAH BIMOMARTANI'],
                'position' => '',
                'name' => '',
            ],
        ];
    }

    /**
     * Normalisasi form_data request ke struktur yang dipakai semua view death.
     * Alias bahasa Indonesia dan Inggris sengaja diterima agar kompatibel dengan
     * data seeder maupun request dari admin.
     */
    public function dataForRequest(LetterRequest $request, string $template = 'death-certificate'): array
    {
        $request->loadMissing(['letterType.signer', 'authorizedSigner']);
        $data = $this->sampleData($this->hasTemplate($template) ? $template : 'death-certificate');
        $form = $request->form_data ?? [];
        $form = $this->normalizeKeys($form);
        $death = $data['death'];

        $death['family'] = [
            'kk_number' => $this->value($form, ['nomor_kk', 'kk_number']),
            'head_name' => $this->value($form, ['nama_kepala_keluarga', 'head_name']),
        ];
        $death['deceased'] = [
            // NIK jenazah harus memakai field khusus jenazah terlebih dahulu.
            // NIK pemohon hanya menjadi fallback apabila data jenazah belum diisi.
            'nik' => $this->value($form, ['nik_jenazah', 'deceased_nik']) ?: $request->applicant_nik,
            'name' => $this->value($form, ['nama_jenazah', 'deceased_name']) ?: $request->applicant_name,
            'gender' => $this->value($form, ['jenis_kelamin_jenazah', 'deceased_gender']),
            'birth_place' => $this->value($form, ['tempat_kelahiran_jenazah', 'deceased_birth_place', 'birth_place_deceased']),
            'birth_date' => $this->date($this->value($form, ['tanggal_lahir_jenazah', 'deceased_birth_date', 'birth_date_deceased'])),
            'birth_place_date' => $this->birthPlaceDate($form, 'jenazah', ['deceased', 'deceased_']),
            'age' => $this->value($form, ['umur_jenazah', 'deceased_age', 'age_jenazah']),
            'religion' => $this->value($form, ['agama_jenazah', 'deceased_religion']),
            'occupation' => $this->value($form, ['pekerjaan_jenazah', 'deceased_occupation']),
            'address' => $this->value($form, ['alamat_jenazah', 'deceased_address']),
            'birth_order' => $this->value($form, ['anak_ke_jenazah', 'deceased_birth_order']),
            'death_date' => $this->date($this->value($form, ['meninggal_hari_tanggal', 'tanggal_meninggal', 'death_date', 'deceased_death_date'])),
            'death_day' => $this->value($form, ['death_day', 'hari_meninggal']),
            'death_time' => $this->value($form, ['jam_meninggal', 'deceased_death_time']),
            'death_place' => $this->value($form, ['death_place', 'lokasi_meninggal', 'tempat_meninggal', 'tempat_kematian', 'deceased_death_place']),
            'death_location' => $this->value($form, ['death_location', 'tempat_kematian', 'deceased_death_location'])
                ?: $this->value($form, ['death_place', 'lokasi_meninggal', 'deceased_death_place']),
            'death_city' => $this->value($form, ['death_city', 'death_regency', 'kota_tempat_meninggal', 'kab_kota_meninggal', 'deceased_death_city']),
            'cause' => $this->value($form, ['death_cause', 'sebab_kematian', 'deceased_death_cause']),
            'cause_detail' => $this->value($form, ['death_cause_detail', 'rincian_sebab', 'detail_sebab_kematian']),
            'relation' => $this->value($form, ['death_informant', 'death_certified_by', 'yang_menerangkan', 'deceased_relation']),
        ];
        $death['mother'] = $this->person($form, 'ibu', ['mother']);
        $death['father'] = $this->person($form, 'ayah', ['father']);
        foreach (['mother', 'father'] as $parent) {
            $death[$parent]['birth_place_date'] = $this->birthPlaceDate(
                $form,
                $parent === 'mother' ? 'ibu' : 'ayah',
                [$parent, $parent === 'mother' ? 'ibu' : 'ayah'],
                $death[$parent],
            );
            $legacyParent = $parent === 'mother' ? 'ibu' : 'ayah';
            $death[$parent]['rt'] = $death[$parent]['rt'] ?: $this->value($form, [$parent . '_rt', 'rt_' . $parent, 'rt_' . $legacyParent]);
            $death[$parent]['rw'] = $death[$parent]['rw'] ?: $this->value($form, [$parent . '_rw', 'rw_' . $parent, 'rw_' . $legacyParent]);
            $death[$parent]['address_with_rt_rw'] = $this->addressWithRtRw($death[$parent]['address'] ?? '', $death[$parent]['rt'], $death[$parent]['rw']);
        }
        $death['deceased']['birth_place_date'] = $this->birthPlaceDate($form, 'jenazah', ['deceased', 'deceased_'], $death['deceased']);
        if (blank($death['deceased']['birth_place']) || blank($death['deceased']['birth_date'])) {
            [$legacyBirthPlace, $legacyBirthDate] = $this->splitBirthPlaceDate($death['deceased']['birth_place_date']);
            $death['deceased']['birth_place'] = $death['deceased']['birth_place'] ?: $legacyBirthPlace;
            $death['deceased']['birth_date'] = $death['deceased']['birth_date'] ?: $legacyBirthDate;
        }
        $death['deceased']['nationality'] = $this->value($form, ['kewarganegaraan_jenazah', 'deceased_nationality']);
        $death['deceased']['death_day'] = $death['deceased']['death_day'] ?: $this->weekday($death['deceased']['death_date']);
        $death['hamlet_name'] = $this->value($form, ['hamlet_name', 'nama_padukuhan']);
        $death['hamlet_head_name'] = $this->value($form, ['hamlet_head_name', 'dukuh_name', 'nama_dukuh', 'nama_kepala_dukuh']);
        $death['reporter'] = $this->person($form, 'pelapor', ['reporter']);
        foreach ([
            'name' => ['nama', 'name'],
            'birth_place' => ['tempat_lahir', 'birth_place'],
            'birth_date' => ['tanggal_lahir', 'birth_date'],
            'gender' => ['jenis_kelamin', 'gender'],
            'marital_status' => ['status_perkawinan', 'marital_status'],
            'religion' => ['agama', 'religion'],
            'occupation' => ['pekerjaan', 'occupation'],
            'address' => ['alamat', 'address'],
            'destination' => ['travel_to', 'pergi_ke', 'destination'],
        ] as $field => $keys) {
            if (blank($death['reporter'][$field] ?? null)) {
                $death['reporter'][$field] = $this->value($form, $keys);
            }
        }
        $death['reporter']['birth_date'] = $this->date($death['reporter']['birth_date'] ?? null);
        $death['reporter']['birth_place_date'] = $this->birthPlaceDate(
            $form,
            'pelapor',
            ['reporter', 'pelapor'],
            $death['reporter'],
        );
        $death['reporter']['phone'] = $this->value($form, ['reporter_phone', 'applicant_phone', 'phone', 'phone_number', 'nomor_hp', 'kontak_pelapor'])
            ?: $request->applicant_phone;
        $death['reporter']['email'] = $this->value($form, ['reporter_email', 'applicant_email', 'email', 'email_pelapor']);
        $death['reporter']['relationship'] = $this->value($form, ['reporter_relation', 'reporter_relationship', 'hubungan_pelapor', 'hubungan_dengan_yang_meninggal', 'deceased_relation']);
        $death['reporter']['rt'] = $death['reporter']['rt'] ?: $this->value($form, ['rt_pelapor', 'reporter_rt', 'rt']);
        $death['reporter']['rw'] = $death['reporter']['rw'] ?: $this->value($form, ['rw_pelapor', 'reporter_rw', 'rw']);
        $death['reporter']['address_with_rt_rw'] = $this->addressWithRtRw($death['reporter']['address'] ?? '', $death['reporter']['rt'], $death['reporter']['rw']);
        $death['reporter']['marital_status'] = $death['reporter']['marital_status'] ?: $this->value($form, ['status_perkawinan']);
        $death['reporter']['destination'] = $death['reporter']['destination'] ?: $this->value($form, ['travel_to', 'pergi_ke', 'destination']);
        foreach ([
            'nik' => $request->applicant_nik,
            'name' => $request->applicant_name,
            'address' => $request->applicant_address,
        ] as $key => $fallback) {
            if (blank($death['reporter'][$key] ?? null)) {
                $death['reporter'][$key] = $fallback;
            }
        }
        $death['reporter']['address_with_rt_rw'] = $this->addressWithRtRw(
            $death['reporter']['address'] ?? '',
            $death['reporter']['rt'] ?? '',
            $death['reporter']['rw'] ?? '',
        );
        $death['reporter']['report_date'] = $this->value($form, ['tanggal_lapor', 'report_date']);
        $death['witnesses'] = [
            $this->person($form, 'saksi_1', ['witness_1', 'witness1']),
            $this->person($form, 'saksi_2', ['witness_2', 'witness2']),
        ];
        $death['attorney'] = [
            'grantor' => $this->person($form, 'pemberi_kuasa', ['grantor']),
            'attorney' => $this->person($form, 'penerima_kuasa', ['attorney']),
        ];
        foreach (['name' => ['name', 'nama'], 'occupation' => ['occupation', 'pekerjaan'], 'address' => ['address', 'alamat']] as $field => $keys) {
            if (blank($death['attorney']['grantor'][$field] ?? null)) {
                $death['attorney']['grantor'][$field] = $this->value($form, $keys);
            }
        }
        $statementBirthPlaceDate = $this->value($form, [
            'ttl_pemberi_pernyataan', 'pemberi_pernyataan_ttl', 'ttl_pelapor', 'pelapor_ttl',
            'statement_birth_place_date', 'birth_place_date_statement',
        ]) ?: ($death['reporter']['birth_place_date'] ?? '');
        $statementAge = $this->value($form, [
            'umur_pemberi_pernyataan', 'pemberi_pernyataan_umur', 'umur_pelapor', 'pelapor_umur',
            'reporter_age', 'age',
        ]) ?: ($death['reporter']['age'] ?? '');
        if (blank($statementAge) && filled($death['reporter']['birth_date'] ?? null)) {
            try {
                $birthDate = Carbon::createFromFormat('!d-m-Y', (string) $death['reporter']['birth_date']);
                $asOf = $request->authorized_at ?? now();
                $statementAge = $birthDate->diff($asOf)->y;
            } catch (\Throwable) {
                $statementAge = '';
            }
        }
        $death['statement'] = [
            'name' => $this->value($form, ['nama_pemberi_pernyataan', 'nama_pelapor', 'statement_name'])
                ?: ($death['reporter']['name'] ?? $request->applicant_name),
            'nik' => $this->value($form, ['nik_pemberi_pernyataan', 'nik_pelapor', 'statement_nik'])
                ?: ($death['reporter']['nik'] ?? $request->applicant_nik),
            'birth_place_date' => $this->normalizeBirthPlaceDate($statementBirthPlaceDate),
            'age' => $statementAge,
            'occupation' => $this->value($form, [
                'pekerjaan_pemberi_pernyataan', 'pemberi_pernyataan_pekerjaan', 'pekerjaan_pelapor',
                'pelapor_pekerjaan', 'reporter_occupation', 'occupation',
            ]) ?: ($death['reporter']['occupation'] ?? ''),
            'address' => $this->value($form, ['alamat_pemberi_pernyataan', 'alamat_pelapor', 'statement_address'])
                ?: ($death['reporter']['address'] ?? $request->applicant_address),
            'relationship' => $this->value($form, ['hubungan_pelapor']),
        ];

        $now = $request->authorized_at ?? now();
        $code = rtrim($request->letterType?->number_prefix ?: ($request->letterType?->code ?? ''), '/');
        $draftNumber = $code !== '' ? "{$code}/.../{$this->toRomanMonth($now->month)}/{$now->year}" : '';
        $data['number'] = $data['nomor'] = $request->letter_number ?? $draftNumber;
        $data['document_title'] = $request->letterType?->letter_name
            ?? $data['document_title'];
        $data['request'] = [
            'code' => $request->request_code,
            'source' => $request->source,
            'notes' => $request->notes,
        ];
        $signer = $request->authorizedSigner ?? $request->letterType?->signer;
        $data['death'] = $death;
        $data['signature']['date'] = $request->authorized_at?->format('d-m-Y') ?? '';
        $data['signature']['position'] = $signer?->position ?? '';
        $data['signature']['name'] = $signer?->name ?? '';
        $data['signature']['image'] = $this->signatureImageBase64($signer?->signature_image);

        return $data;
    }

    private function signatureImageBase64(?string $pathOrUrl): ?string
    {
        if (blank($pathOrUrl)) {
            return null;
        }

        if (str_starts_with($pathOrUrl, 'data:')) {
            return $pathOrUrl;
        }

        $clean = strtok($pathOrUrl, '?#');

        if (str_starts_with($clean, '/')) {
            $absPath = public_path(ltrim($clean, '/'));
        } elseif (preg_match('#^https?://#', $clean)) {
            $parsed  = parse_url($clean);
            $absPath = public_path(ltrim($parsed['path'] ?? '', '/'));
        } else {
            $absPath = $clean;
        }

        if (! is_file($absPath) || ! is_readable($absPath)) {
            return null;
        }

        $mime = mime_content_type($absPath) ?: 'image/png';

        return "data:{$mime};base64," . base64_encode(file_get_contents($absPath));
    }

    public function toRomanMonth(int $month): string
    {
        return match ($month) {
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
            default => (string) $month,
        };
    }

    /** Samakan metadata title PDF dengan nama service type. */
    private function renderDocumentHtml(string $template, array $data): string
    {
        $html = view($this->viewName($template), $data)->render();
        $title = trim((string) ($data['document_title'] ?? ''));

        if ($title === '') {
            return $html;
        }

        $safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $titleTag = '<title>' . $safeTitle . '</title>';

        if (preg_match('/<title\b[^>]*>.*?<\/title>/is', $html)) {
            return (string) preg_replace(
                '/<title\b[^>]*>.*?<\/title>/is',
                $titleTag,
                $html,
                1,
            );
        }

        return (string) preg_replace('/(<head\b[^>]*>)/i', '$1' . $titleTag, $html, 1);
    }

    private function blankDeathData(): array
    {
        $person = [
            'nik' => '', 'name' => '', 'gender' => '', 'birth_place' => '',
            'birth_date' => '', 'birth_place_date' => '', 'age' => '',
            'religion' => '', 'occupation' => '', 'nationality' => '', 'marital_status' => '',
            'address' => '', 'address_with_rt_rw' => '', 'rt' => '', 'rw' => '',
            'birth_order' => '', 'report_date' => '', 'phone' => '', 'email' => '',
            'relationship' => '', 'destination' => '',
        ];

        return [
            'family' => ['kk_number' => '', 'head_name' => ''],
            'deceased' => array_merge($person, [
                'death_date' => '', 'death_day' => '', 'death_time' => '', 'death_place' => '',
                'death_location' => '', 'death_city' => '', 'cause' => '', 'cause_detail' => '', 'relation' => '',
            ]),
            'mother' => $person,
            'father' => $person,
            'reporter' => $person,
            'witnesses' => [$person, $person],
            'attorney' => ['grantor' => $person, 'attorney' => $person],
            'selamatan' => array_fill_keys(['3', '7', '40', '100', '365', '730', '1000'], ''),
            'statement' => [
                'name' => '', 'nik' => '', 'birth_place_date' => '', 'age' => '',
                'occupation' => '', 'address' => '', 'relationship' => '',
            ],
            'hamlet_name' => '',
            'hamlet_head_name' => '',
        ];
    }

    private function person(array $form, string $prefix, array $aliases = []): array
    {
        $prefixes = array_values(array_unique([$prefix, ...$aliases]));
        $keysFor = static function (array $fieldNames) use ($prefixes): array {
            $keys = [];
            foreach ($prefixes as $candidatePrefix) {
                foreach ($fieldNames as $fieldName) {
                    $keys[] = $candidatePrefix . '_' . $fieldName;
                    $keys[] = $fieldName . '_' . $candidatePrefix;
                }
            }

            return $keys;
        };
        $get = fn (array $fieldNames): mixed => $this->value($form, $keysFor($fieldNames));
        $birthPlace = $get(['birth_place', 'birthplace', 'tempat_lahir', 'tempat_kelahiran']);
        $birthDate = $this->date($get(['birth_date', 'tanggal_lahir']));
        $address = $get(['address', 'alamat']);
        $rt = $get(['rt']);
        $rw = $get(['rw']);

        return [
            'nik' => $get(['nik', 'national_id']),
            'name' => $get(['name', 'nama', 'full_name']),
            'gender' => $get(['gender', 'jenis_kelamin']),
            'birth_place' => $birthPlace,
            'birth_date' => $birthDate,
            'birth_place_date' => $get(['birth_place_date', 'birthplace_date', 'ttl', 'tempat_tanggal_lahir'])
                ?: $this->joinBirthPlaceDate($birthPlace, $birthDate),
            'age' => $get(['age', 'umur']),
            'religion' => $get(['religion', 'agama']),
            'occupation' => $get(['occupation', 'pekerjaan']),
            'nationality' => $get(['nationality', 'citizenship', 'kewarganegaraan']),
            'marital_status' => $get(['marital_status', 'status_perkawinan']),
            'address' => $address,
            'address_with_rt_rw' => $this->addressWithRtRw($address, $rt, $rw),
            'rt' => $rt,
            'rw' => $rw,
            'phone' => $get(['phone', 'phone_number', 'nomor_hp', 'kontak']),
            'email' => $get(['email']),
            'relationship' => $get(['relationship', 'relation', 'hubungan']),
            'destination' => $get(['destination', 'pergi_ke']),
            'birth_order' => $get(['birth_order', 'anak_ke']),
            'report_date' => $get(['report_date', 'tanggal_lapor']),
        ] + ($aliases ? ['role' => $aliases[0]] : []);
    }

    private function birthPlaceDate(array $form, string $legacyPrefix, array $prefixes, array $person = []): string
    {
        $keys = [];
        $keys[] = 'ttl_' . $legacyPrefix;
        $keys[] = $legacyPrefix . '_ttl';
        $keys[] = 'birth_place_date_' . $legacyPrefix;
        $keys[] = $legacyPrefix . '_birth_place_date';
        foreach ($prefixes as $prefix) {
            $keys[] = 'ttl_' . $prefix;
            $keys[] = $prefix . '_ttl';
            $keys[] = 'birth_place_date_' . $prefix;
            $keys[] = $prefix . '_birth_place_date';
            $keys[] = 'birthplace_date_' . $prefix;
        }
        $combined = $this->value($form, $keys);
        if (filled($combined)) {
            return $this->normalizeBirthPlaceDate($combined);
        }

        $place = $person['birth_place'] ?? $this->value($form, [
            $legacyPrefix === 'jenazah' ? 'deceased_birth_place' : $legacyPrefix . '_birth_place',
            $legacyPrefix === 'jenazah' ? 'tempat_kelahiran_jenazah' : 'tempat_lahir_' . $legacyPrefix,
        ]);
        $date = $person['birth_date'] ?? $this->date($this->value($form, [
            $legacyPrefix === 'jenazah' ? 'deceased_birth_date' : $legacyPrefix . '_birth_date',
            $legacyPrefix === 'jenazah' ? 'tanggal_lahir_jenazah' : 'tanggal_lahir_' . $legacyPrefix,
        ]));

        return $this->joinBirthPlaceDate($place, $date);
    }

    private function joinBirthPlaceDate(mixed $place, mixed $date): string
    {
        return implode(', ', array_values(array_filter([
            trim((string) $place),
            trim((string) $date),
        ], static fn (string $value): bool => $value !== '')));
    }

    private function splitBirthPlaceDate(mixed $value): array
    {
        $value = trim((string) $value);
        if (preg_match('/^(.*?)\s+((?:[[:alpha:]]{3}\s+)?[[:alpha:]]{3}\s+\d{1,2}\s+\d{4}\s+\d{2}:\d{2}:\d{2}(?:\s+GMT[+-]\d{4})?(?:\s*\([^)]*\))?)$/iu', $value, $matches)) {
            $date = $this->date($matches[2]);
            if ($date !== '' && preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
                return [rtrim(trim($matches[1]), " ,，\t\n\r\0\x0B"), $date];
            }
        }
        if (preg_match('/^(.*?)[,，]\s*(.+\b\d{4}(?:\s+\d{2}:\d{2}:\d{2})?(?:\s+GMT[+-]\d{4})?(?:\s*\([^)]*\))?)$/iu', $value, $matches)) {
            $date = $this->date($matches[2]);
            if ($date !== '' && preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
                return [trim($matches[1]), $date];
            }
        }
        $value = preg_replace('/\s*\([^)]*\)\s*$/u', '', $value) ?? $value;
        $value = preg_replace('/\s+GMT[+-]\d{4}\s*$/i', '', $value) ?? $value;
        if (preg_match('/^(.*?)[,，]\s*(\d{1,2}[-\/]\d{1,2}[-\/]\d{4})$/u', $value, $matches)) {
            try {
                $date = Carbon::createFromFormat('!d-m-Y', str_replace('/', '-', $matches[2]))->format('d-m-Y');
            } catch (\Throwable) {
                $date = $matches[2];
            }

            return [trim($matches[1]), $date];
        }

        if (preg_match('/^(.*?)[,，]\s*(\d{1,2}\s+[[:alpha:]\p{L}]+\s+\d{4})$/u', $value, $matches)) {
            return [trim($matches[1]), $this->date($matches[2])];
        }

        return ['', ''];
    }

    private function normalizeBirthPlaceDate(mixed $value): string
    {
        $value = trim((string) $value);
        [$place, $date] = $this->splitBirthPlaceDate($value);

        return $place !== '' && $date !== '' ? $this->joinBirthPlaceDate($place, $date) : $value;
    }

    private function addressWithRtRw(mixed $address, mixed $rt, mixed $rw): string
    {
        $address = preg_replace('/\s+/u', ' ', trim((string) $address)) ?? trim((string) $address);
        $rt = trim((string) $rt);
        $rw = trim((string) $rw);
        if ($rt === '' && $rw === '') {
            return $address;
        }

        $rtRw = implode(' / ', array_values(array_filter([
            $rt !== '' ? 'RT ' . $rt : '',
            $rw !== '' ? 'RW ' . $rw : '',
        ])));
        if ($address === '') {
            return $rtRw;
        }

        if (preg_match('/\bRT\s*[:.]?\s*' . preg_quote($rt, '/') . '/i', $address)
            && ($rw === '' || preg_match('/\bRW\s*[:.]?\s*' . preg_quote($rw, '/') . '/i', $address))) {
            return $address;
        }

        return $address . ', ' . $rtRw;
    }

    private function weekday(mixed $date): string
    {
        if (! filled($date)) {
            return '';
        }

        try {
            return Carbon::parse($date)->locale('id')->translatedFormat('l');
        } catch (\Throwable) {
            return '';
        }
    }

    private function value(array $form, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $form) && $form[$key] !== null && $form[$key] !== '') {
                return $form[$key];
            }
        }

        return '';
    }

    /** Tambahkan alias snake_case untuk payload camelCase dari form admin. */
    private function normalizeKeys(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        $normalized = [];
        foreach ($value as $key => $item) {
            $item = $this->normalizeKeys($item);
            $normalized[$key] = $item;
            if (is_string($key)) {
                $snake = strtolower((string) preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $key));
                if ($snake !== $key && ! array_key_exists($snake, $normalized)) {
                    $normalized[$snake] = $item;
                }
            }
        }

        return $normalized;
    }

    private function date(mixed $value): string
    {
        if (! $value) {
            return '';
        }

        try {
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->format('d-m-Y');
            }

            // Date.toString() browser payloads append both GMT offset and a
            // human timezone label; remove the label before parsing.
            $value = preg_replace('/\s*\([^)]*\)\s*$/u', '', trim((string) $value)) ?? trim((string) $value);

            // Some older form records stored the browser's complete local
            // date string. Parse its calendar components explicitly so PHP
            // does not leave it unformatted or shift it across timezones.
            if (preg_match('/^(?:[[:alpha:]]{3}\s+)?([[:alpha:]]{3})\s+(\d{1,2})\s+(\d{4})\s+\d{2}:\d{2}:\d{2}\s+GMT[+-]\d{4}$/i', $value, $matches)) {
                return Carbon::createFromFormat('!M d Y', "{$matches[1]} {$matches[2]} {$matches[3]}")->format('d-m-Y');
            }

            return Carbon::parse($value)->format('d-m-Y');
        } catch (\Throwable) {
            $value = preg_replace('/\s*\([^)]*\)\s*$/u', '', trim((string) $value)) ?? trim((string) $value);

            return (string) (preg_replace('/\s+GMT[+-]\d{4}\s*$/i', '', $value) ?? $value);
        }
    }

    private function asset(string $file): string
    {
        $path = public_path('images/letters/' . $file);

        if (! is_file($path)) {
            return '';
        }

        return 'data:' . (mime_content_type($path) ?: 'image/png') . ';base64,' . base64_encode(file_get_contents($path));
    }
}
