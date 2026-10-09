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
            'birth_place' => $this->value($form, ['tempat_kelahiran_jenazah', 'deceased_birth_place']),
            'birth_date' => $this->date($this->value($form, ['tanggal_lahir_jenazah', 'deceased_birth_date'])),
            'birth_place_date' => $this->value($form, ['ttl_jenazah', 'deceased_birth_place_date']),
            'age' => $this->value($form, ['umur_jenazah', 'deceased_age']),
            'religion' => $this->value($form, ['agama_jenazah', 'deceased_religion']),
            'occupation' => $this->value($form, ['pekerjaan_jenazah', 'deceased_occupation']),
            'address' => $this->value($form, ['alamat_jenazah', 'deceased_address']),
            'birth_order' => $this->value($form, ['anak_ke_jenazah', 'deceased_birth_order']),
            'death_date' => $this->value($form, ['meninggal_hari_tanggal', 'deceased_death_date']),
            'death_time' => $this->value($form, ['jam_meninggal', 'deceased_death_time']),
            'death_place' => $this->value($form, ['lokasi_meninggal', 'tempat_kematian', 'deceased_death_place']),
            'death_city' => $this->value($form, ['kota_tempat_meninggal', 'deceased_death_city']),
            'cause' => $this->value($form, ['sebab_kematian', 'deceased_death_cause']),
            'relation' => $this->value($form, ['yang_menerangkan', 'deceased_relation']),
        ];
        $death['mother'] = $this->person($form, 'ibu', ['mother']);
        $death['father'] = $this->person($form, 'ayah', ['father']);
        $death['reporter'] = $this->person($form, 'pelapor', ['reporter']);
        foreach ([
            'nik' => $request->applicant_nik,
            'name' => $request->applicant_name,
            'address' => $request->applicant_address,
        ] as $key => $fallback) {
            if (blank($death['reporter'][$key] ?? null)) {
                $death['reporter'][$key] = $fallback;
            }
        }
        $death['reporter']['report_date'] = $this->value($form, ['tanggal_lapor', 'report_date']);
        $death['witnesses'] = [
            $this->person($form, 'saksi_1', ['witness_1']),
            $this->person($form, 'saksi_2', ['witness_2']),
        ];
        $death['attorney'] = [
            'grantor' => $this->person($form, 'pemberi_kuasa', ['grantor']),
            'attorney' => $this->person($form, 'penerima_kuasa', ['attorney']),
        ];
        $death['statement'] = [
            'name' => $this->value($form, ['nama_pemberi_pernyataan', 'nama_pelapor']) ?: $request->applicant_name,
            'nik' => $this->value($form, ['nik_pemberi_pernyataan', 'nik_pelapor']) ?: $request->applicant_nik,
            'address' => $this->value($form, ['alamat_pemberi_pernyataan', 'alamat_pelapor']) ?: $request->applicant_address,
            'relationship' => $this->value($form, ['hubungan_pelapor']),
        ];

        $data['number'] = $data['nomor'] = $request->letter_number ?? '';
        $data['document_title'] = $request->letterType?->letter_name
            ?? $data['document_title'];
        $data['request'] = [
            'code' => $request->request_code,
            'source' => $request->source,
            'notes' => $request->notes,
        ];
        $data['death'] = $death;
        $data['signature']['date'] = $request->authorized_at?->format('d-m-Y') ?? '';
        $data['signature']['position'] = $request->authorizedSigner?->position
            ?? $request->letterType?->signer?->position
            ?? '';
        $data['signature']['name'] = $request->authorizedSigner?->name
            ?? $request->letterType?->signer?->name
            ?? '';

        return $data;
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
            'religion' => '', 'occupation' => '', 'address' => '',
            'birth_order' => '', 'report_date' => '',
        ];

        return [
            'family' => ['kk_number' => '', 'head_name' => ''],
            'deceased' => array_merge($person, [
                'death_date' => '', 'death_time' => '', 'death_place' => '',
                'death_city' => '', 'cause' => '', 'relation' => '',
            ]),
            'mother' => $person,
            'father' => $person,
            'reporter' => $person,
            'witnesses' => [$person, $person],
            'attorney' => ['grantor' => $person, 'attorney' => $person],
            'selamatan' => array_fill_keys(['3', '7', '40', '100', '365', '730', '1000'], ''),
            'statement' => ['name' => '', 'nik' => '', 'address' => '', 'relationship' => ''],
        ];
    }

    private function person(array $form, string $prefix, array $aliases = []): array
    {
        // Setiap kolom harus membaca key miliknya sendiri. Sebelumnya daftar
        // alias seluruh kolom digabung sekaligus, sehingga semua kolom
        // menemukan nilai pertama (nik_*) dan menampilkan NIK.
        $get = function (string $field, array $extra = []) use ($form, $prefix): mixed {
            return $this->value($form, array_merge([
                $field . '_' . $prefix,
            ], $extra));
        };

        return [
            'nik' => $get('nik', ['nik_' . $prefix]),
            'name' => $get('nama', ['name_' . $prefix]),
            'gender' => $get('jenis_kelamin', ['gender_' . $prefix]),
            'birth_place_date' => $get('ttl', ['birth_place_date_' . $prefix]),
            'age' => $get('umur', ['age_' . $prefix]),
            'occupation' => $get('pekerjaan', ['occupation_' . $prefix]),
            'address' => $get('alamat', ['address_' . $prefix]),
        ] + ($aliases ? ['role' => $aliases[0]] : []);
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

    private function date(mixed $value): string
    {
        if (! $value) {
            return '';
        }

        try {
            return Carbon::parse($value)->format('d-m-Y');
        } catch (\Throwable) {
            return (string) $value;
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
