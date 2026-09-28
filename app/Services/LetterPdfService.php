<?php

namespace App\Services;

use App\Models\LetterRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Support\Carbon;

/**
 * MERGE NOTE (gabungan File 1 + File 2):
 * - Dari File 1 dipertahankan: MARRIAGE_LETTERS, key 'marriage' di viewData(),
 *   marriageData(), marriagePerson(), longDate(), birthLong(), sampleMarriageForm(),
 *   dan entri sampleOverrides() untuk surat pernikahan perempuan
 *   (registration-form, n1, n2, ... numpang-nikah).
 * - Dari File 2 dipertahankan: parameter $template di viewData(), VIEW_FOLDERS,
 *   viewName(), MARRIED_LETTER_SLUGS + marriedLetterData()/personBlock(),
 *   SIGNATURE_DIRECT_SLUGS, KALURAHAN_KOP_TEMPLATES, DATE_PLACEHOLDER,
 *   surat akta kelahiran (birthLetterFromForm dst), letter-c/land/endorser/
 *   application, regionData($blank), signature(..., $directPrefix), kopData(..., $kalurahanKop).
 * - Revisi terbaru: 'unmarried-certificate' (Surat Keterangan Belum Kawin) di
 *   MARRIAGE_LETTERS/marriageData()/sampleOverrides(); marriedLetterData() memakai
 *   longDate() untuk tanggal; fallback applicant_name, registration & village
 *   letter_number di viewData().
 * - Kedua set surat pernikahan (lama: slug n1/n2/..; baru: slug *-letter di folder
 *   married-man) berjalan berdampingan tanpa bentrok slug.
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

    public const DATE_PLACEHOLDER = self::BLANK_PLACEHOLDER;

    /**
     * Slug letter_type yang kopnya HARUS selalu format "PEMERINTAH KALURAHAN
     * BIMOMARTANI" biasa, walau signer-nya Lurah langsung. TTD tetap ikut
     * jabatan signer asli, tapi kop tidak berubah.
     */
    private const KOP_ALWAYS_KALURAHAN_SLUGS = [
        'permit-followup-letter',
        'surat-tindak-lanjut-izin',
        'lease-offer-letter',
        'surat-penawaran-sewa',
    ];

    /** Slug yang TTD-nya cukup "a.n LURAH" (tanpa rantai Carik / u.b.). */
    private const SIGNATURE_DIRECT_SLUGS = [
        'birth-attestation-letter',
        'birth-certificate-referral-letter',
        'birth-certificate-power-of-attorney',
        'spousal-relationship-responsibility-statement',
    ];

    /** Template yang kopnya selalu "PEMERINTAH KALURAHAN BIMOMARTANI". */
    private const KALURAHAN_KOP_TEMPLATES = [
        'land-price-certificate-letter',
        'land-origin-certificate-letter',
    ];

    private const VIEW_FOLDERS = [
        'letter-c-data-statement-letter' => 'letter-c',
        'power-of-attorney-letter' => 'letter-c',
        'land-price-certificate-letter' => 'letter-c',
        'land-origin-certificate-letter' => 'letter-c',
        'general-certificate-letter' => 'married-man',
        'marriage-application-letter' => 'married-man',
        'marriage-lodging-certificate-letter' => 'married-man',
        'never-married-certificate-letter' => 'married-man',
        'not-remarried-statement-letter' => 'married-man',
        'death-certificate-for-marriage-letter' => 'married-man',
        'bride-groom-consent-letter' => 'married-man',
        'parental-consent-letter' => 'married-man',
        'marriage-introduction-letter' => 'married-man',
        'marriage-registration-data-sheet' => 'married-man',
    ];

    private const MARRIED_LETTER_SLUGS = [
        'general-certificate-letter',
        'marriage-application-letter',
        'marriage-lodging-certificate-letter',
        'never-married-certificate-letter',
        'not-remarried-statement-letter',
        'death-certificate-for-marriage-letter',
        'bride-groom-consent-letter',
        'parental-consent-letter',
        'marriage-introduction-letter',
        'marriage-registration-data-sheet',
    ];

    public const MARRIAGE_LETTERS = [
        'registration-form'   => 'Data Isian Pendaftaran Nikah',
        'n1'                  => 'Pengantar Nikah (N1)',
        'n2'                  => 'Permohonan Kehendak Nikah (N2)',
        'n4'                  => 'Persetujuan Calon Pengantin (N4)',
        'n5'                  => 'Surat Izin Orang Tua (N5)',
        'n6'                  => 'Surat Keterangan Kematian (N6)',
        'guardian-statement'  => 'Surat Keterangan Wali Nikah',
        'judge-guardian'      => 'Surat Keterangan Wali Hakim',
        'health-referral'     => 'Surat Keterangan (Pengantar Puskesmas)',
        'unmarried-statement' => 'Surat Pernyataan Belum Menikah Lagi',
        'unmarried-certificate' => 'Surat Keterangan Belum Kawin',
        'numpang-nikah'       => 'Surat Keterangan Numpang Nikah',
    ];

    public function viewData(LetterRequest $letterRequest, ?string $template = null): array
    {
        $letterRequest->loadMissing(['citizen', 'letterType.signer', 'authorizedSigner']);

        $form = $letterRequest->form_data ?? [];
        $citizen = $letterRequest->citizen;
        $signer = $letterRequest->authorizedSigner ?? $letterRequest->letterType?->signer;

        // Tidak fallback ke now(): kalau belum authorized_at, tanggal TTD tampil
        // placeholder titik-titik (lihat signature()).
        $letterDate = $letterRequest->authorized_at;

        $birthPlace = $form['birth_place'] ?? $citizen?->birth_place;
        $birthDate = $form['birth_date'] ?? $citizen?->birth_date;
        $address = $this->fullAddress($letterRequest->applicant_address ?? $citizen?->address);
        $templateSlug = $template ?? $letterRequest->letterType?->slug;
        $signerPosition = $signer?->position;
        if (
            in_array($templateSlug, self::MARRIED_LETTER_SLUGS, true)
            && in_array(
                strtolower(trim($signerPosition ?? '')),
                ['kaur tata laksana', 'kepala urusan tata laksana'],
                true
            )
        ) {
            $signerPosition = 'KAMITUWA';
        }

        $kalurahanKop = $this->usesKalurahanKop($templateSlug)
            || in_array($templateSlug, self::KOP_ALWAYS_KALURAHAN_SLUGS, true);

        // Kode formulir tetap (mis. "F-1.06"); diisi ke 2 key ('form_code' & 'kodeForm').
        $formCode = $form['kode_form'] ?? $letterRequest->letterType?->form_code ?? null;

        $data = [
            // 'number' (baru) dan 'nomor' (alias untuk blade lama).
            'number' => $number = $letterRequest->letter_number
                ?? (($letterRequest->letterType?->number_prefix ?? '') . '......'),
            'nomor' => $number,

            // surat pernikahan (set lama: n1, n2, dst)
            'marriage' => $this->marriageData($form, $number, $letterDate),

            'form_code' => $formCode,
            'kodeForm' => $formCode,

            'signer' => [
                'name' => $signer?->name,
                'position' => $signerPosition,
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

            // surat pernyataan beda nama/identitas
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

            // F-1.06: rincian anggota keluarga (sesuai KK)
            'family_members' => collect($form['family_members'] ?? [])->map(fn ($m) => [
                'name' => $m['name'] ?? null,
                'nik' => $m['nik'] ?? null,
                'shdk' => $m['shdk'] ?? null,
                'note' => $m['note'] ?? null,
            ])->all(),

            // F-1.06: tabel A (Pendidikan Terakhir & Pekerjaan)
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

            // F-1.06: tabel B (Agama & elemen "Lainnya")
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

            // "Kepada Yth" (surat balasan penelitian, tindak lanjut izin, penawaran sewa, dll)
            'recipient' => $form['recipient_name'] ?? null,
            'recipient_address' => $form['recipient_address'] ?? null,
            'ref_number' => $form['ref_letter_number'] ?? null,

            // surat penawaran sewa kontrak gedung
            'lease' => [
                'building_name' => $form['lease_building_name'] ?? null,
                'building_address' => $form['lease_building_address'] ?? null,
                'duration_years' => $form['lease_duration_years'] ?? null,
            ],

            // daftar "Tembusan Dikirim Kepada"
            'tembusan' => $form['tembusan'] ?? [],

            // surat kuasa waris & surat kuasa administrasi kependudukan (field digabung)
            'attorney' => [
                'name' => $form['attorney_name'] ?? null,
                'nik' => $form['attorney_nik'] ?? null,
                'birth' => $this->birth($form['attorney_birth_place'] ?? null, $form['attorney_birth_date'] ?? null),
                'gender' => $form['attorney_gender'] ?? null,
                'occupation' => $form['attorney_occupation'] ?? null,
                'address' => isset($form['attorney_address']) ? $this->fullAddress($form['attorney_address']) : null,
            ],
            // data almarhum/almarhumah pewaris
            'deceased' => [
                'name' => $form['deceased_name'] ?? null,
                'death_place' => $form['deceased_death_place'] ?? $form['death_place'] ?? null,
            ],
            // alasan/kondisi pemberi kuasa tidak bisa hadir sendiri
            'condition' => $form['condition'] ?? null,

            'endorser' => [
                'office' => $form['endorser_office'] ?? null,
                'name' => $form['endorser_name'] ?? null,
            ],

            // Sengaja tetap fallback ke tanggal hari ini walau $letterDate null
            // (surat kuasa antar-warga tidak melalui authorized_at) — lihat dateParts().
            'date_parts' => $this->dateParts($letterDate),

            // Carbon::parse(null) = now(), jadi tetap terisi tahun berjalan.
            'year' => Carbon::parse($letterDate)->format('Y'),

            // SPPD
            'travelOrder' => $this->travelOrderFromForm($form),

            // permohonan tinggal sementara
            'stayApplication' => $this->stayApplicationFromForm($form),

            // permohonan menjadi penduduk sementara (SKTS)
            'residentRequest' => $this->residentRequestFromForm($form),

            // surat akta kelahiran
            'birth' => $this->birthLetterFromForm($form, [
                'name' => $letterRequest->applicant_name,
                'nik' => $letterRequest->applicant_nik,
                'birth_place' => $birthPlace,
                'birth_date' => $birthDate,
                'occupation' => $form['occupation'] ?? $citizen?->occupation,
                'address' => $address,
                'rt' => $form['rt'] ?? $citizen?->rt,
                'rw' => $form['rw'] ?? $citizen?->rw,
                'phone' => $form['phone'] ?? $citizen?->phone,
                'kk_number' => $form['kk_number'] ?? $citizen?->kk_number,
            ]),

            'application' => [
                'type' => $form['application_type'] ?? null,
                'dukuh_name' => $form['dukuh_name'] ?? null,
            ],

            'hamlet_head_name' => $form['dukuh_name'] ?? null,

            'letter_c' => [
                'hamlet' => $form['letter_c_hamlet'] ?? null,
                'owner_name' => $form['letter_c_owner_name'] ?? null,
            ],

            'land' => [
                'certificate_number' => $form['land_certificate_number'] ?? null,
                'area' => $form['land_area'] ?? null,
                'area_in_words' => $form['land_area_in_words'] ?? null,
                'owner_name' => $form['land_owner_name'] ?? null,
                'hamlet' => $form['land_hamlet'] ?? null,
                'village' => $form['land_village'] ?? null,
                'district' => $form['land_district'] ?? null,
                'regency' => $form['land_regency'] ?? null,
                'price_min' => $this->rupiah($form['land_price_min'] ?? null),
                'price_max' => $this->rupiah($form['land_price_max'] ?? null),
                'measurement_letter_number' => $form['land_measurement_letter_number'] ?? null,
                'measurement_letter_date' => $this->longDate($form['land_measurement_letter_date'] ?? null),
            ],

            // data mentah, kalau template lain butuh field di luar daftar di atas
            'form' => $form,

            // nama kalurahan lengkap, dipakai di klausul "KHUSUS" surat kuasa
            'village' => self::VILLAGE_SUFFIX,

            // kotak digit kode wilayah untuk formulir F.1-25 / F.1-31
            'region' => $this->regionData(),
            'signature' => $this->signature(
                $signer?->name,
                $signerPosition,
                $letterDate,
                in_array($templateSlug, self::SIGNATURE_DIRECT_SLUGS, true),
            ),
            'logo' => $this->asset('logo-sleman.png'),
            'kop' => $this->kopData($signerPosition, $kalurahanKop),
        ];

        if (in_array($templateSlug, self::MARRIED_LETTER_SLUGS, true)) {
            $data = array_merge($data, $this->marriedLetterData($form));

            // Nama pemohon di TTD surat permohonan: fallback ke nama pemohon di LetterRequest.
            $data['applicant_name'] = $data['applicant_name'] ?? $letterRequest->applicant_name;

            // Petugas Kamituwa & nomor registrasi: fallback ke penanda tangan & nomor surat.
            $data['registration']['officer_name'] ??= $signer?->name;
            $data['registration']['number'] ??= $number;

            // Nomor surat desa di lembar data isian: fallback ke nomor surat.
            $data['village']['letter_number'] ??= $number;

            $data['applicant'] = array_merge($data['applicant'], [
                'full_name_alias' => $form['name_alias'] ?? $letterRequest->applicant_name,
                'bin_or_binti' => $form['bin_or_binti'] ?? null,
                'birth_place_date' => $data['applicant']['birth'],
                'nationality' => $form['nationality'] ?? 'WNI',
                'education' => $form['education'] ?? $citizen?->education,
                'last_education' => $form['education'] ?? $citizen?->education,
                'status' => $data['applicant']['marital_status'],
                'previous_spouse_name' => $form['previous_spouse_name'] ?? null,
                'note' => $form['note'] ?? null,
                'purpose' => $form['purpose'] ?? null,
                'additional_note' => $form['additional_note'] ?? null,
            ]);

            $data['spouse'] = array_merge($data['spouse'], [
                'full_name_alias' => $form['spouse_name'] ?? $data['spouse']['name'],
                'bin' => $form['spouse_bin'] ?? null,
                'binti' => $form['spouse_binti'] ?? null,
                'nik' => $form['spouse_nik'] ?? null,
                'nationality' => $form['spouse_nationality'] ?? 'WNI',
                'religion' => $form['spouse_religion'] ?? null,
                'birth_place_date' => $data['spouse']['birth'],
            ]);

            $data['deceased'] = array_merge($data['deceased'], [
                'full_name_alias' => $form['deceased_full_name_alias'] ?? $form['deceased_name'] ?? $data['deceased']['name'],
                'binti' => $form['deceased_binti'] ?? null,
                'bin' => $form['deceased_bin'] ?? null,
                'nik' => $form['deceased_nik'] ?? null,
                'birth_place_date' => $this->birth($form['deceased_birth_place'] ?? null, $form['deceased_birth_date'] ?? null),
                'nationality' => $form['deceased_nationality'] ?? 'WNI',
                'religion' => $form['deceased_religion'] ?? null,
                'occupation' => $form['deceased_occupation'] ?? null,
                'address' => isset($form['deceased_address']) ? $this->fullAddress($form['deceased_address']) : null,
                'death_date' => $this->longDate($form['deceased_death_date'] ?? null),
                'death_place' => $form['deceased_death_place'] ?? $data['deceased']['death_place'],
            ]);

            $data['signature'] = array_merge($data['signature'], [
                'position' => $data['signature']['position'] ?? $signerPosition,
                'name' => $signer?->name,
                'holder_name' => $letterRequest->applicant_name,
                'signer_name' => $signer?->name,
            ]);
        }

        return $data;
    }

    public function sampleViewData(string $template = 'surat-keterangan-usaha'): array
    {
        $data = array_replace_recursive($this->sampleBase(), $this->sampleOverrides($template));

        if (
            in_array($template, self::MARRIED_LETTER_SLUGS, true)
            && in_array(
                strtolower(trim($data['signer']['position'] ?? '')),
                ['kaur tata laksana', 'kepala urusan tata laksana'],
                true
            )
        ) {
            $data['signer']['position'] = 'KAMITUWA';
        }

        $data['number'] = $data['nomor'] = $this->sampleNumber($template);

        // sampleFormCode() dulu; kalau tidak ada, pakai yang di-set lewat sampleOverrides().
        $formCode = $this->sampleFormCode($template) ?? $data['form_code'] ?? $data['kodeForm'] ?? null;
        $data['form_code'] = $formCode;
        $data['kodeForm'] = $formCode;

        $data['region'] = $this->regionData($template === 'ktp-application-form');
        if (! in_array($template, self::MARRIED_LETTER_SLUGS, true)) {
            $data['village'] = self::VILLAGE_SUFFIX;
        } else {
            $data['village'] = $data['village'] ?? [
                'name' => 'BIMOMARTANI', 'subdistrict' => 'NGEMPLAK', 'regency' => 'SLEMAN',
                'letter_number' => null, 'letter_date' => null, 'head_name' => null,
            ];
        }

        $data['year'] = now()->format('Y');

        // TTD/kop bisa memakai contoh jabatan berbeda dari body surat
        // ('signature_position' / 'kop_position' di sampleOverrides()).
        $signaturePosition = $data['signature_position'] ?? $data['signer']['position'];
        $kopPosition = $data['kop_position'] ?? $signaturePosition;
        $kalurahanKop = $this->usesKalurahanKop($template)
            || in_array($template, self::KOP_ALWAYS_KALURAHAN_SLUGS, true);

        $data['signature'] = $this->signature(
            $data['signer']['name'],
            $signaturePosition,
            $data['date'] ?? null,
            in_array($template, self::SIGNATURE_DIRECT_SLUGS, true),
        );

        $data['logo'] = $this->asset('logo-sleman.png');
        $data['kop'] = $this->kopData($kopPosition, $kalurahanKop);
        $data['date_parts'] = $this->dateParts($data['date'] ?? null);
        unset($data['date'], $data['signature_position'], $data['kop_position']);

        return $data;
    }

    public function viewName(string $template): string
    {
        $folder = self::VIEW_FOLDERS[$template] ?? null;

        return 'letters.' . ($folder ? $folder . '.' : '') . $template;
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
     * Kode wilayah administrasi untuk kotak digit di formulir F.1-25 / F.1-31.
     * $blank = true menghasilkan kotak kosong (mis. ktp-application-form).
     */
    private function regionData(bool $blank = false): array
    {
        if ($blank) {
            return [
                'province_code'  => array_fill(0, 2, ''),
                'province_name'  => '',

                'regency_code'   => array_fill(0, 2, ''),
                'regency_name'   => '',

                'district_code'  => array_fill(0, 2, ''),
                'district_name'  => '',

                'village_code'   => array_fill(0, 4, ''),
                'village_name'   => '',
            ];
        }

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

    private function usesKalurahanKop(?string $template): bool
    {
        return $template !== null && in_array($template, self::KALURAHAN_KOP_TEMPLATES, true);
    }

    /**
     * Data kop surat. Berbeda tergantung apakah penandatangan adalah Lurah
     * langsung, atau pejabat lain yang menandatangani "a.n. Lurah".
     * $kalurahanKop = true memaksa kop "PEMERINTAH KALURAHAN BIMOMARTANI".
     */
    private function kopData(?string $position, bool $kalurahanKop = false): array
    {
        $isLurah = $this->signedByLurah($position) && ! $kalurahanKop;

        return [
            'line1'   => 'PEMERINTAH KABUPATEN SLEMAN',
            'line2'   => 'KAPANEWON NGEMPLAK',
            'line3'   => $isLurah ? 'LURAH BIMOMARTANI' : 'PEMERINTAH KALURAHAN BIMOMARTANI',
            'address' => 'Jl.Prambanan Cangkringan, Km.6.5, Bimomartani. Ngemplak, Sleman, DIY',
            'contact' => 'Kode Pos : 55584   Telepon : 08112654981',
            'email'   => null, // isi kalau kalurahan sudah punya email resmi
            'is_lurah' => $isLurah, // dipakai base.blade untuk lebar gambar aksara
            'aksara'  => $this->asset($isLurah ? 'aksara-lurah.png' : 'aksara-bimomartani.png'),
        ];
    }

    /**
     * Nomor contoh per-template untuk preview. Mendukung slug lama (Indonesia)
     * maupun slug baru (Inggris).
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
            'land-price-certificate-letter',
            'land-origin-certificate-letter' => '593/',
            'population-document-statement-letter' => null, // pakai form_code
            'heir-power-of-attorney-letter' => null, // surat kuasa antar-warga, tidak bernomor
            'research-response-letter' => null,
            'marriage-certificate-duplicate-letter' => null,
            'general-cover-letter' => null,
            'permit-followup-letter', 'surat-tindak-lanjut-izin' => '010/',
            'lease-offer-letter', 'surat-penawaran-sewa' => '.........................',

            // Tiga template ini punya nomor sendiri di array masing-masing.
            'duty-travel-order-letter',
            'temporary-stay-application-form',
            'temporary-resident-request' => null,

            'birth-certificate-referral-letter' => '472/',
            'birth-attestation-letter' => '472.11/',
            'birth-certificate-application-form',
            'birth-report-form',
            'birth-report-statement',
            'spousal-relationship-responsibility-statement',
            'out-of-domicile-birth-report',
            'late-birth-registration-approval-decree',
            'birth-registration-report',
            'birth-certificate-power-of-attorney' => null,
            default => '581/ 1',
        };
    }

    /**
     * Kode formulir tetap untuk preview surat model formulir resmi.
     * null = surat bernomor urut biasa (atau kode di-set lewat sampleOverrides()).
     */
    private function sampleFormCode(string $template): ?string
    {
        return match ($template) {
            'population-document-statement-letter' => 'F-1.06',
            'unregistered-marriage-responsibility-letter' => 'F.1.07',
            'birth-report-form' => 'F2 02',
            default => null,
        };
    }

    private function signature(?string $name, ?string $position, $date, bool $directPrefix = false): array
    {
        $parsed = $date ? Carbon::parse($date) : null;

        return [
            'city' => self::SIGNATURE_CITY,
            'date' => $parsed?->format('d/m/Y') ?? self::BLANK_PLACEHOLDER,
            'date_long' => $parsed?->locale('id')->translatedFormat('d F Y') ?? self::BLANK_PLACEHOLDER,
            'prefix' => $this->signaturePrefix($position, $directPrefix),
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

    private function signaturePrefix(?string $position, bool $direct = false): array
    {
        if ($this->signedByLurah($position)) {
            return [];
        }

        $p = mb_strtolower((string) $position);

        // Carik menandatangani langsung a.n. Lurah, tanpa rantai "u.b." lagi.
        if (str_contains($p, 'carik')) {
            return [self::SIGNATURE_LURAH];
        }

        // Slug tertentu: cukup "a.n LURAH" tanpa rantai Carik / u.b.
        if ($direct) {
            return [self::SIGNATURE_LURAH];
        }

        // Pejabat lain berada di bawah Carik: a.n Lurah -> Carik -> u.b. [jabatan].
        return [self::SIGNATURE_LURAH, 'Carik', 'u.b.'];
    }

    private function birth(?string $place, $date): ?string
    {
        $formatted = $date ? Carbon::parse($date)->format('d/m/Y') : null;

        return collect([$place, $formatted])->filter()->implode(', ') ?: null;
    }

    private function marriageData(array $form, ?string $number, $letterDate): array
    {
        $date = $letterDate ? Carbon::parse($letterDate) : null;

        $bride     = $this->marriagePerson($form, 'bride');
        $groom     = $this->marriagePerson($form, 'groom');
        $father    = $this->marriagePerson($form, 'bride_father');
        $mother    = $this->marriagePerson($form, 'bride_mother');
        $guardian  = $this->marriagePerson($form, 'guardian');
        $exHusband = $this->marriagePerson($form, 'ex_husband');

        $bride['bin']    ??= $father['name'];
        $bride['gender'] ??= 'Perempuan';
        $groom['gender'] ??= 'Laki-laki';

        $judgeReason = $form['judge_guardian_reason'] ?? null;
        $relation    = $form['guardian_relation'] ?? null;
        if (blank($guardian['name']) && blank($judgeReason)) {
            $guardian = $father;
            $relation ??= 'Ayah kandung';
        }

        return [
            'letter_number' => $form['letter_number'] ?? $number,
            'akad' => [
                'day'   => $form['akad_day']
                    ?? (isset($form['akad_date'])
                        ? Carbon::parse($form['akad_date'])->locale('id')->translatedFormat('l') : null),
                'date'  => $this->longDate($form['akad_date'] ?? null),
                'time'  => $form['akad_time'] ?? null,
                'place' => $form['akad_place'] ?? null,
            ],
            'bride'        => $bride,
            'groom'        => $groom,
            'bride_father' => $father,
            'bride_mother' => $mother,
            'guardian'     => $guardian + ['relation' => $relation, 'reason' => $form['guardian_reason'] ?? null],
            'ex_husband'   => $exHusband + [
                'died_at'    => $form['ex_husband_died_at'] ?? null,
                'died_place' => $form['ex_husband_died_place'] ?? null,
            ],
            'judge_guardian_reason' => $judgeReason,
            'health' => [
                'destination' => $form['health_destination'] ?? null,
                'need'        => $form['health_need'] ?? null,
                'note'        => $form['health_note'] ?? null,
                'conduct'     => $form['conduct'] ?? null,
                'valid_from'  => $date?->copy()->locale('id')->translatedFormat('d F'),
                'valid_until' => $date?->copy()->addMonths(3)->locale('id')->translatedFormat('d F Y'),
            ],
            'numpang' => [
                'letter_number' => $form['numpang_letter_number'] ?? null,
                'date'          => $this->longDate($form['numpang_date'] ?? null),
            ],
            'unmarried_certificate' => [
                'letter_number' => $form['unmarried_certificate_number'] ?? null,
                'date'          => $this->longDate($form['unmarried_certificate_date'] ?? null),
            ],
        ];
    }

    private function longDate($date): ?string
    {
        return $date ? Carbon::parse($date)->locale('id')->translatedFormat('d F Y') : null;
    }

    private function birthLong(?string $place, $date): ?string
    {
        return collect([$place, $this->longDate($date)])->filter()->implode(', ') ?: null;
    }

    private function marriagePerson(array $form, string $prefix): array
    {
        $f = fn (string $key) => $form["{$prefix}_{$key}"] ?? null;

        return [
            'name'        => $f('name'),
            'bin'         => $f('bin'),
            'nik'         => $f('nik'),
            'gender'      => $f('gender'),
            'birth'       => $this->birthLong($f('birth_place'), $f('birth_date')),
            'citizenship' => $f('citizenship'),
            'religion'    => $f('religion'),
            'occupation'  => $f('occupation'),
            'education'   => $f('education'),
            'address'     => $f('address'), // sengaja TIDAK pakai fullAddress()
            'status'      => $f('status'),
        ];
    }

    private function sampleMarriageForm(): array
    {
        return [];
    }

    /**
     * Pecah satu tanggal jadi nama hari, tanggal, nama bulan, dan tahun (bahasa
     * Indonesia). $date null -> jatuh ke tanggal hari ini.
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

    private function personBlock(array $form, string $prefix, ?string $binLabel = null, array $extra = []): array
    {
        $get = fn (string $field) => $form["{$prefix}_{$field}"] ?? null;

        $data = [
            'full_name_alias' => $get('name'),
            'nik' => $get('nik'),
            'birth_place_date' => $this->birth($get('birth_place'), $get('birth_date')),
            'nationality' => $get('nationality') ?? 'WNI',
            'religion' => $get('religion'),
            'occupation' => $get('occupation'),
            'address' => isset($form["{$prefix}_address"]) ? $this->fullAddress($form["{$prefix}_address"]) : null,
        ];

        if ($binLabel) {
            $data[$binLabel] = $get($binLabel);
        }

        return array_merge($data, $extra);
    }

    private function marriedLetterData(array $form): array
    {
        $village = [
            'name' => $form['village_name'] ?? 'BIMOMARTANI',
            'subdistrict' => $form['village_subdistrict'] ?? 'NGEMPLAK',
            'regency' => $form['village_regency'] ?? 'SLEMAN',
            'letter_number' => $form['village_letter_number'] ?? null,
            'letter_date' => $this->longDate($form['village_letter_date'] ?? null),
            'head_name' => $form['village_head_name'] ?? null,
        ];

        $ceremonyDay = $form['ceremony_day'] ?? (isset($form['ceremony_date'])
            ? Carbon::parse($form['ceremony_date'])->locale('id')->translatedFormat('l')
            : null);
        $ceremonyDate = $this->longDate($form['ceremony_date'] ?? null);

        $ceremony = [
            'day' => $ceremonyDay,
            'date' => $ceremonyDate,
            'time' => $form['ceremony_time'] ?? null,
            'place' => $form['ceremony_place'] ?? null,
            'date_time' => collect([$ceremonyDay, $ceremonyDate])->filter()->implode(', ') ?: null,
        ];

        $registration = [
            'number' => $form['registration_number'] ?? null,
            'date' => $this->longDate($form['registration_date'] ?? null),
            'position' => $form['registration_position'] ?? null,
            'officer_name' => $form['registration_officer_name'] ?? null,
        ];

        $groom = $this->personBlock($form, 'groom', 'bin', [
            'status' => $form['groom_status'] ?? null,
            'last_education' => $form['groom_last_education'] ?? null,
            'previous_spouse_name' => $form['groom_previous_spouse_name'] ?? null,
            'previous_spouse_parent_name' => $form['groom_previous_spouse_parent_name'] ?? null,
            'previous_spouse_bin' => $form['groom_previous_spouse_bin'] ?? null,
            'previous_spouse_nik' => $form['groom_previous_spouse_nik'] ?? null,
            'previous_spouse_birth_place_date' => $this->birth(
                $form['groom_previous_spouse_birth_place'] ?? null,
                $form['groom_previous_spouse_birth_date'] ?? null,
            ),
            'previous_spouse_nationality' => $form['groom_previous_spouse_nationality'] ?? null,
            'previous_spouse_religion' => $form['groom_previous_spouse_religion'] ?? null,
            'previous_spouse_occupation' => $form['groom_previous_spouse_occupation'] ?? null,
            'previous_spouse_address' => $form['groom_previous_spouse_address'] ?? null,
            'previous_spouse_death_date' => $this->longDate($form['groom_previous_spouse_death_date'] ?? null),
            'previous_spouse_death_place' => $form['groom_previous_spouse_death_place'] ?? null,
        ]);
        $groom['name'] = $groom['full_name_alias'];

        $bride = $this->personBlock($form, 'bride', 'binti', [
            'status' => $form['bride_status'] ?? null,
            'last_education' => $form['bride_last_education'] ?? null,
            'previous_spouse_name' => $form['bride_previous_spouse_name'] ?? null,
            'previous_spouse_bin' => $form['bride_previous_spouse_bin'] ?? null,
            'previous_spouse_nik' => $form['bride_previous_spouse_nik'] ?? null,
            'previous_spouse_death_date' => $this->longDate($form['bride_previous_spouse_death_date'] ?? null),
            'previous_spouse_death_place' => $form['bride_previous_spouse_death_place'] ?? null,
        ]);
        $bride['name'] = $bride['full_name_alias'];

        $groomFather = $this->personBlock($form, 'groom_father', 'bin');
        $groomFather['name'] = $groomFather['full_name_alias'];
        $groomMother = $this->personBlock($form, 'groom_mother', 'binti');
        $groomMother['name'] = $groomMother['full_name_alias'];
        $brideFather = $this->personBlock($form, 'bride_father', 'bin');
        $brideFather['name'] = $brideFather['full_name_alias'];
        $brideMother = $this->personBlock($form, 'bride_mother', 'binti');
        $brideMother['name'] = $brideMother['full_name_alias'];

        return [
            'village' => $village,
            'ceremony' => $ceremony,
            'registration' => $registration,
            'subdistrict' => $form['subdistrict'] ?? $village['subdistrict'],
            'groom_name' => $groom['name'],
            'bride_name' => $bride['name'],
            'applicant_name' => $form['applicant_name'] ?? null,
            'groom' => $groom,
            'bride' => $bride,
            'groomFather' => $groomFather,
            'groomMother' => $groomMother,
            'brideFather' => $brideFather,
            'brideMother' => $brideMother,

            'father' => $this->personBlock($form, 'father', 'bin'),
            'mother' => $this->personBlock($form, 'mother', 'binti'),
            'child' => $this->personBlock($form, 'child', 'bin_or_binti'),
            'child_spouse' => $this->personBlock($form, 'child_spouse', 'bin_or_binti'),
        ];
    }

    /**
     * Petakan $form ke struktur $travelOrder (duty-travel-order-letter.blade.php).
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
     * Petakan $form ke struktur $stayApplication (temporary-stay-application-form.blade.php).
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
     * Petakan $form ke struktur $residentRequest (temporary-resident-request.blade.php).
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

    private function birthLetterFromForm(array $form, array $fallback = []): array
    {
        $type = $form['birth_type'] ?? null;

        $childBirth = $form['child_birth_date'] ?? null;

        $reporter = $this->birthLetterPerson($form, 'reporter', $fallback) + [
            'phone' => $form['reporter_phone'] ?? $fallback['phone'] ?? null,
            'relationship' => $form['reporter_relationship'] ?? null,
            'application_date' => $this->birthLetterDate($form['application_date'] ?? null, true),
            'report_date' => $this->birthLetterDate($form['report_date'] ?? null, true),
        ];

        $witnesses = collect($form['birth_witnesses'] ?? [])->map(fn ($w) => [
            'nik' => $w['nik'] ?? null,
            'name' => $w['name'] ?? null,
            'age' => $w['age'] ?? null,
            'address' => $w['address'] ?? null,
        ])->all();

        $witnesses = array_pad($witnesses, 2, ['nik' => null, 'name' => null, 'age' => null, 'address' => null]);

        return [
            'type' => $type,
            'application_number' => $form['application_number'] ?? null,
            'family_card_number' => $form['kk_number'] ?? $fallback['kk_number'] ?? null,
            'head_of_family_name' => $form['head_of_family_name'] ?? null,

            'report_kind' => $form['report_kind'] ?? match ($type) {
                'new' => 'Lahir Baru',
                'late' => 'Lahir Lama',
                default => null,
            },
            'registrar_name' => $form['registrar_name'] ?? null,

            'documents' => array_values($form['required_documents'] ?? []),

            'child' => [
                'nik' => $form['child_nik'] ?? null,
                'name' => $form['child_name'] ?? null,
                'gender' => $form['child_gender'] ?? null,
                'delivery_place' => $form['child_delivery_place'] ?? null,
                'delivery_address' => $form['child_delivery_address'] ?? null,
                'birth_place' => $form['child_birth_place'] ?? null,
                'birth_day_name' => $childBirth
                    ? Carbon::parse($childBirth)->locale('id')->translatedFormat('l')
                    : null,
                'birth_date' => $this->birthLetterDate($childBirth, true),
                'birth_time' => $form['child_birth_time'] ?? null,
                'plurality' => $form['child_plurality'] ?? null,
                'birth_order' => $form['child_birth_order'] ?? null,
                'birth_attendant' => $form['child_birth_attendant'] ?? null,
                'weight' => $form['child_weight'] ?? null,
                'length' => $form['child_length'] ?? null,
                'gestational_age' => $form['child_gestational_age'] ?? null,
                'delivery_method' => $form['child_delivery_method'] ?? null,
                'delivery_cost' => $form['child_delivery_cost'] ?? null,
            ],

            'mother' => $this->birthLetterPerson($form, 'mother'),
            'father' => $this->birthLetterPerson($form, 'father'),

            'marriage' => [
                'certificate_number' => $form['marriage_cert_number'] ?? null,
                'date' => $this->birthLetterDate($form['marriage_date'] ?? null, true),
                'record_place' => $form['marriage_record_place'] ?? null,
                'record_date' => $this->birthLetterDate($form['marriage_record_date'] ?? null, true),
            ],

            'reporter' => $reporter,
            'witnesses' => $witnesses,

            'hamlet' => [
                'name' => $form['hamlet_name'] ?? null,
                'head_name' => $form['hamlet_head_name'] ?? null,
            ],

            'decree' => [
                'number' => $form['decree_number'] ?? null,
                'place' => $form['decree_place'] ?? 'Sleman',
                'agency_head_name' => $form['agency_head_name'] ?? null,
                'agency_head_nip' => $form['agency_head_nip'] ?? null,
            ],
        ];
    }

    private function birthLetterPerson(array $form, string $prefix, array $fb = []): array
    {
        $get = fn (string $key) => $form["{$prefix}_{$key}"] ?? $fb[$key] ?? null;

        $rawBirth = $get('birth_date');

        return [
            'nik' => $get('nik'),
            'name' => $get('name'),
            'birth_place' => $get('birth_place'),
            'birth_date' => $this->birthLetterDate($rawBirth),
            'age' => $get('age') ?? ($rawBirth ? Carbon::parse($rawBirth)->age : null),
            'occupation' => $get('occupation'),
            'address' => $get('address'),
            'rt_rw' => $this->birthLetterRtRw($get('rt'), $get('rw')),
            'nationality' => $get('nationality'),
            'ethnicity' => $get('ethnicity'),
        ];
    }

    private function birthLetterDate($date, bool $long = false): ?string
    {
        if (! $date) {
            return null;
        }

        $parsed = Carbon::parse($date);

        return $long
            ? $parsed->locale('id')->translatedFormat('d F Y')
            : $parsed->format('d/m/Y');
    }

    private function birthLetterRtRw($rt, $rw): string
    {
        return 'RT: ' . $rt . str_repeat("\u{00A0}", 6) . 'RW: ' . $rw;
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
                // Default Kaur (bukan Lurah) — kop "PEMERINTAH KALURAHAN BIMOMARTANI"
                // dan TTD rantai 3-tingkat (a.n LURAH / Carik / u.b. / Kaur ...).
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

            // attorney digabung: 'gender' untuk heir-power-of-attorney-letter,
            // 'occupation' untuk population-service-authorization-letter.
            'attorney' => [
                'name' => null, 'nik' => null, 'birth' => null,
                'gender' => null, 'occupation' => null, 'address' => null,
            ],
            'deceased' => [
                'name' => null, 'death_place' => null,
            ],
            'condition' => null,
            'endorser' => [
                'office' => null, 'name' => null,
            ],

            // default kosong; diisi penuh lewat sampleOverrides() saat $template cocok.
            'travelOrder' => null,
            'stayApplication' => null,
            'residentRequest' => null,

            // surat pernikahan set lama (n1, n2, dst) — diisi lewat sampleOverrides()
            'marriage' => null,

            'birth' => $this->birthLetterFromForm([]),

            'application' => [
                'type' => null,
                'dukuh_name' => null,
            ],
            'hamlet_head_name' => null,

            'letter_c' => [
                'hamlet' => null, 'owner_name' => null,
            ],

            'land' => [
                'certificate_number' => null, 'area' => null, 'area_in_words' => null,
                'owner_name' => null, 'hamlet' => null, 'village' => null,
                'district' => null, 'regency' => null, 'price_min' => null, 'price_max' => null,
                'measurement_letter_number' => null, 'measurement_letter_date' => null,
            ],

            'ceremony' => ['day' => null, 'date' => null, 'time' => null, 'place' => null, 'date_time' => null],
            'registration' => ['number' => null, 'date' => null, 'position' => null, 'officer_name' => null],
            'subdistrict' => null,
            'groom_name' => null,
            'bride_name' => null,
            'applicant_name' => null,
            'groom' => null,
            'bride' => null,
            'groomFather' => null,
            'groomMother' => null,
            'brideFather' => null,
            'brideMother' => null,
            'father' => null,
            'mother' => null,
            'child' => null,
            'child_spouse' => null,

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

            // Versi 2 Surat Keterangan Jalan: ditandatangani langsung oleh Lurah.
            'surat-keterangan-jalan-lurah', 'travel-permit-letter-lurah',
            'travel-permit-letter-vill' => [
                'signer' => ['position' => 'Lurah'],
            ],

            'fuel-recommendation-letter' => [],

            'surat-keterangan-keramaian', 'event-permit-letter' => [
                'signer' => ['position' => 'Jogoboyo'],
            ],

            'surat-keterangan-penghasilan' => [],

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

            // surat pernikahan perempuan (set lama)
            'registration-form', 'n1', 'n2', 'n4', 'n5', 'n6',
            'guardian-statement', 'judge-guardian', 'health-referral',
            'unmarried-statement', 'numpang-nikah' => [
                'signer' => ['position' => 'Kamituwa'],
                'marriage' => $this->marriageData($this->sampleMarriageForm(), null, null),
            ],
            // Surat Keterangan Belum Kawin: kop dan TTD Lurah, bukan Kamituwa
            'unmarried-certificate' => [
                'signer' => ['position' => 'Lurah'],
                'marriage' => $this->marriageData($this->sampleMarriageForm(), null, null),
            ],

            // Surat pernyataan sendiri oleh warga — standalone, tanpa kop/signer pejabat.
            'population-document-statement-letter' => [],
            'identity-discrepancy-statement-letter' => [],
            'unregistered-marriage-responsibility-letter' => [],

            // Surat kuasa antar-warga untuk sidang waris — standalone.
            'heir-power-of-attorney-letter' => [],

            // signer pakai default Kaur Tata Laksana dari sampleBase().
            'research-response-letter' => [],

            // Jabatan di body dikosongkan; TTD tetap pakai contoh 'Kamituwa'.
            'marriage-certificate-duplicate-letter' => [
                'signer' => ['position' => null],
                'signature_position' => 'Kamituwa',
            ],

            // Jabatan di body dikosongkan; TTD pakai default Kaur Tata Laksana.
            'general-cover-letter' => [
                'signer' => ['position' => null],
                'signature_position' => 'Kepala Urusan Tata Laksana',
            ],

            // Surat kuasa pelayanan administrasi kependudukan — cukup set kode formulir.
            'population-service-authorization-letter' => [
                'kodeForm' => 'F.1.07',
            ],

            // Surat tindak lanjut izin: TTD langsung Lurah, kop tetap Kalurahan.
            'permit-followup-letter', 'surat-tindak-lanjut-izin' => [
                'signer' => ['position' => 'Lurah'],
                'kop_position' => 'Kepala Urusan Tata Laksana',
            ],

            // Surat penawaran sewa: TTD langsung Lurah, kop tetap Kalurahan.
            'lease-offer-letter', 'surat-penawaran-sewa' => [
                'signer' => ['position' => 'Lurah'],
                'kop_position' => 'Kepala Urusan Tata Laksana',
            ],

            'birth-certificate-referral-letter',
            'birth-attestation-letter' => [
                'signer' => ['position' => 'Kamituwa'],
            ],

            'spousal-relationship-responsibility-statement' => [
                'signer' => ['position' => 'Ulu - Ulu'],
            ],
            'birth-certificate-power-of-attorney' => [
                'signer' => ['position' => 'Kamituwa'],
            ],
            'out-of-domicile-birth-report',
            'late-birth-registration-approval-decree',
            'birth-registration-report' => [],

            'ktp-application-form' => [
                'kodeForm' => 'F-107',
                'date' => null,
            ],

            'land-price-certificate-letter', 'land-origin-certificate-letter' => [
                'signer' => ['position' => 'Lurah'],
                'date' => null,
            ],

            'letter-c-data-statement-letter', 'power-of-attorney-letter' => [
                'date' => null,
            ],

            // SPPD — TEMPLATE KOSONG ('' bukan null, supaya {{ $x }} tanpa "?? ''" tidak error).
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

            // Permohonan Tinggal Sementara — TEMPLATE KOSONG.
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

            // Surat Permohonan Menjadi Penduduk Sementara (SKTS) — TEMPLATE KOSONG.
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