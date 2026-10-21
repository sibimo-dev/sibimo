<?php

namespace App\Services;

use App\Models\LetterRequest;
use App\Models\LetterType;
use App\Models\Staff;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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
 *
 * CATATAN PERBAIKAN:
 * - MARRIAGE_LETTERS sebelumnya terdeklarasi 2x (fatal error) -> sisa 1.
 * - VIEW_FOLDERS, MARRIED_LETTER_SLUGS, SIGNATURE_DIRECT_SLUGS, KALURAHAN_KOP_TEMPLATES
 *   hilang saat merge -> sudah diisi ulang (versi terbaru).
 * - LANDSCAPE_SLUGS hilang saat merge (error "Undefined constant ...::LANDSCAPE_SLUGS")
 *   -> sudah diisi ulang.
 * - Key 'marriage' ganda di viewData() -> sisa 1.
 * - sampleBase() kini memuat application, hamlet_head_name, letter_c, dan land
 *   (sebelumnya preview error "Undefined variable $land").
 * - sampleBase() kini memuat 'birth' (birthLetterFromForm([])) supaya preview surat
 *   akta kelahiran tidak error "Undefined variable $birth".
 * - viewName() kini memetakan slug MARRIAGE_LETTERS (n1, n2, judge-guardian, dst)
 *   ke folder letters/marriage-women/letters/ (sebelumnya error "View ... tidak ditemukan").
 * - Data contoh preview surat tanah (land-price-certificate-letter &
 *   land-origin-certificate-letter) di sampleOverrides() dikosongkan (null).
 *
 * GABUNGAN DENGAN VERSI TEMAN (KIA):
 * - Ditambahkan dari versi teman: kiaApplicationFromForm(), key 'kia' di
 *   viewData()/sampleBase(), entri sampleOverrides() 'kia-application-form', dan
 *   'surat-keterangan-jalan-lurah' di sampleNumber().
 * - Key 'marriage' ganda di viewData() versi teman TIDAK dibawa (sisa 1).
 * - Tanggal 'date' di kiaApplicationFromForm() kini memakai locale('id').
 *
 * PENAMBAHAN:
 * - Formulir Biodata Penduduk WNI Per Keluarga (F-1.01): LANDSCAPE_SLUGS +
 *   isLandscape(); pdf() & previewHtml() mendukung landscape; sampleNumber() -> null
 *   dan sampleFormCode() -> 'F-1.01' untuk 'family-biodata-form'.
 * - Formulir Pendaftaran Peristiwa Kependudukan (F-1.02): konstanta
 *   POPULATION_OCCURRENCE_*_KEYS, populationOccurrenceFromForm(), key
 *   'populationOccurrence' di viewData()/sampleBase()/sampleOverrides().
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
     * Template utama per kode tipe surat.
     *
     * Beberapa template punya varian (misalnya formulir nikah dan kelahiran),
     * sehingga satu kode dipetakan ke satu template utama yang dipakai oleh
     * workflow request admin. Varian lain tetap dapat dilihat melalui route
     * preview developer.
     */
    private const TEMPLATE_BY_CODE = [
        'SKBK' => 'unmarried-status-letter',
        'SKU' => 'business-permit-letter',
        'SKUM' => 'general-statement-letter',
        'SKUMG' => 'general-statement-letter-gov',
        'SKD' => 'domicile-certificate',
        'SKTM' => 'sktm-general',
        'SKTMS' => 'sktm-school',
        'SKP' => 'income-permit-letter',
        'SKK' => 'event-permit-letter',
        'SKJ' => 'travel-permit-letter',
        'SKJG' => 'travel-permit-letter-gov',
        'SKJV' => 'travel-permit-letter-vill',
        'SKCK' => 'skck-referral-letter',
        'SPKTP' => 'ktp-application-form',
        'SPKIA' => 'kia-application-form',
        'SPKIAF' => 'population-occurrence-registration-form',
        'SRBBM' => 'fuel-recommendation-letter',
        'SPPWNI' => 'relocation-cover-letter',
        'SGC' => 'divorce-lawsuit-letter',
        'SMLPI' => 'permit-followup-letter',
        'SPSKG' => 'lease-offer-letter',
        'PNP' => 'registration-form',
        'N2P' => 'n2',
        'PNL' => 'marriage-registration-data-sheet',
        'PAK' => 'birth-certificate-application-form',
        'FPK' => 'birth-report-form',
        'LK' => 'birth-report-statement',
        'SKAK' => 'birth-certificate-power-of-attorney',
        'PPKT' => 'late-birth-registration-approval-decree',
        'LKLD' => 'out-of-domicile-birth-report',
        'SPPD' => 'duty-travel-order-letter',
        'SPBNI' => 'identity-discrepancy-statement-letter',
        'SPTMDK' => 'population-document-statement-letter',
        'SPBMLP' => 'unmarried-statement',
        'SPTJMPSI' => 'spousal-relationship-responsibility-statement',
        'SKDN' => 'marriage-certificate-duplicate-letter',
        'SPU' => 'general-cover-letter',
        'N1P' => 'n1',
        'N4P' => 'n4',
        'N5P' => 'n5',
        'N6P' => 'n6',
        'SKWNP' => 'guardian-statement',
        'SKWHP' => 'judge-guardian',
        'SPTKP' => 'health-referral',
        'SKNNP' => 'numpang-nikah',
        'N1L' => 'marriage-introduction-letter',
        'N4L' => 'bride-groom-consent-letter',
        'SKDPAK' => 'population-service-authorization-letter',
        'SBP' => 'research-response-letter',
        'SKKL' => 'birth-attestation-letter',
        'SKBK2' => 'unmarried-certificate',
        'SPTJMDK' => 'birth-data-responsibility-statement',
    ];

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

    /** Slug yang TTD-nya cukup "a.n LURAH" tanpa rantai Carik / u.b. */
    private const SIGNATURE_DIRECT_SLUGS = [
        'birth-attestation-letter',
        'birth-certificate-referral-letter',
        'birth-certificate-power-of-attorney',
        'spousal-relationship-responsibility-statement',
    ];

    /** Slug template yang dicetak landscape (kertas folio). */
    private const LANDSCAPE_SLUGS = [
        'family-biodata-form',
    ];

    /** Template yang kopnya selalu memakai kop Pemerintah Kalurahan. */
    private const KALURAHAN_KOP_TEMPLATES = [
        'land-price-certificate-letter',
        'land-origin-certificate-letter',
    ];

    private const VIEW_FOLDERS = [
        'birth-attestation-letter' => 'birth',
        'birth-certificate-application-form' => 'birth',
        'birth-certificate-power-of-attorney' => 'birth',
        'birth-certificate-referral-letter' => 'birth',
        'birth-data-responsibility-statement' => 'birth',
        'birth-registration-report' => 'birth',
        'birth-report-form' => 'birth',
        'birth-report-statement' => 'birth',
        'late-birth-registration-approval-decree' => 'birth',
        'out-of-domicile-birth-report' => 'birth',
        'spousal-relationship-responsibility-statement' => 'birth',
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
        'registration-form'     => 'Data Isian Pendaftaran Nikah',
        'n1'                    => 'Pengantar Nikah (N1)',
        'n2'                    => 'Permohonan Kehendak Nikah (N2)',
        'n4'                    => 'Persetujuan Calon Pengantin (N4)',
        'n5'                    => 'Surat Izin Orang Tua (N5)',
        'n6'                    => 'Surat Keterangan Kematian (N6)',
        'guardian-statement'    => 'Surat Keterangan Wali Nikah',
        'judge-guardian'        => 'Surat Keterangan Wali Hakim',
        'health-referral'       => 'Surat Keterangan (Pengantar Puskesmas)',
        'unmarried-statement'   => 'Surat Pernyataan Belum Menikah Lagi',
        'unmarried-certificate' => 'Surat Keterangan Belum Kawin',
        'numpang-nikah'         => 'Surat Keterangan Numpang Nikah',
    ];

    /**
     * Key jenis permohonan formulir F-1.02 (population-occurrence-registration-form).
     * Website publik mengirimnya sebagai array di field `application_types`.
     */
    public const POPULATION_OCCURRENCE_REQUEST_KEYS = [
        // I. Kartu Keluarga
        'family_card_new_family',
        'family_card_head_change',
        'family_card_split',
        'family_card_move_in',
        'family_card_citizen_abroad_return',
        'family_card_vulnerable_group',
        'family_card_join_existing',
        'family_card_important_occurrence',
        'family_card_data_element_change',
        'family_card_lost',
        'family_card_damaged',
        // II. KTP-el
        'id_card_new',
        'id_card_move_in',
        'id_card_lost',
        'id_card_damaged',
        'id_card_itap_extension',
        'id_card_citizenship_change',
        'id_card_out_of_domicile',
        'id_card_transmigration',
        // III. Kartu Identitas Anak / KIA
        'child_card_new',
        'child_card_lost',
        'child_card_damaged',
        'child_card_itap_extension',
        'child_card_other',
        // IV. Perubahan data
        'data_change_family_card',
        'data_change_id_card',
        'data_change_child_card',
    ];

    /**
     * Key persyaratan yang dilampirkan formulir F-1.02.
     * Website publik mengirimnya sebagai array di field `attached_documents`.
     */
    public const POPULATION_OCCURRENCE_DOCUMENT_KEYS = [
        // kolom kiri
        'old_family_card',
        'marriage_certificate',
        'divorce_certificate',
        'move_out_certificate',
        'move_abroad_certificate',
        'damaged_id_card',
        'travel_document',
        'police_loss_report',
        // kolom kanan
        'occurrence_evidence',
        'unregistered_marriage_statement',
        'death_certificate',
        'loss_damage_cause_statement',
        'foreign_mission_move_certificate',
        'family_acceptance_statement',
        'child_custody_power_of_attorney',
        'residence_permit_card',
    ];

    public function rootTemplateForType(?LetterType $letterType): ?string
    {
        $code = strtoupper((string) ($letterType?->code ?? ''));

        if (isset(self::TEMPLATE_BY_CODE[$code])) {
            return self::TEMPLATE_BY_CODE[$code];
        }

        // Template tambahan disimpan langsung sebagai blade_view oleh katalog
        // seeder. Ambil slug terakhir agar tetap kompatibel dengan resolver
        // folder yang sudah dipakai preview developer.
        $bladeView = (string) ($letterType?->blade_view ?? '');

        return str_starts_with($bladeView, 'letters.')
            ? str($bladeView)->afterLast('.')->toString()
            : null;
    }

    public function hasRootTemplate(?LetterType $letterType): bool
    {
        return $this->rootTemplateForType($letterType) !== null;
    }

    /**
     * Tentukan template surat berdasarkan request & siapa yang menandatangani.
     * Surat 2 jenis kop & template:
     * 1. Surat keterangan umum (SKUM / SKUMG):
     *    - TTD Lurah => general-statement-letter (kop lurah)
     *    - TTD Selain Lurah => general-statement-letter-gov (kop pemerintahan)
     * 2. Surat keterangan jalan (SKJ / SKJG / SKJV):
     *    - TTD Lurah => travel-permit-letter (kop lurah)
     *    - TTD Selain Lurah => travel-permit-letter-gov (kop pemerintahan)
     */
    public function resolveTemplate(LetterRequest $letterRequest, ?\App\Models\Staff $overrideSigner = null): ?string
    {
        $signer = $overrideSigner ?? $letterRequest->authorizedSigner ?? $letterRequest->letterType?->signer;
        $code = strtoupper((string) ($letterRequest->letterType?->code ?? ''));
        $isLurah = $this->signedByLurah($signer?->position);

        if (in_array($code, ['SKUM', 'SKUMG'], true)) {
            return $isLurah ? 'general-statement-letter' : 'general-statement-letter-gov';
        }

        if (in_array($code, ['SKJ', 'SKJG', 'SKJV'], true)) {
            return $isLurah ? 'travel-permit-letter' : 'travel-permit-letter-gov';
        }

        return $this->rootTemplateForType($letterRequest->letterType);
    }

    public function viewData(LetterRequest $letterRequest, ?string $template = null): array
    {
        $letterRequest->loadMissing([
            'citizen',
            'letterType.signer',
            'authorizedSigner',
            'attachments.letterTypeDocument',
        ]);

        $code = strtoupper((string) ($letterRequest->letterType?->code ?? ''));
        $form = $letterRequest->form_data ?? [];
        $citizen = $letterRequest->citizen;
        $signer = $letterRequest->authorizedSigner ?? $letterRequest->letterType?->signer;

        // Tidak fallback ke now(): kalau belum authorized_at, tanggal TTD tampil
        // placeholder titik-titik (lihat signature()).
        $letterDate = $letterRequest->authorized_at;

        $birthPlace = $form['birth_place'] ?? $citizen?->birth_place;
        $birthDate = $form['birth_date'] ?? $citizen?->birth_date;
        $address = $this->fullAddress($letterRequest->applicant_address ?? $citizen?->address);
        $templateSlug = $template ?? $this->resolveTemplate($letterRequest) ?? $letterRequest->letterType?->slug;
        $form = $this->normalizeRootTemplateForm($letterRequest, $form);
        $birthPlace = $form['birth_place'] ?? $citizen?->birth_place;
        $birthDate = $form['birth_date'] ?? $citizen?->birth_date;
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

        // F-1.01 menuliskan jabatan "Kepala Desa", sedangkan data struktur
        // organisasi memakai jabatan "Lurah". Nama di bawah area TTD harus
        // tetap nama Lurah, meskipun yang mengotorisasi surat adalah pejabat
        // lain (mis. Kaur).
        if ($templateSlug === 'family-biodata-form') {
            $organizationLurahName = $this->organizationLurahName();

            if (filled($organizationLurahName)) {
                $form['village_head_name'] = $organizationLurahName;
            } elseif (blank($form['village_head_name'] ?? null) && $this->signedByLurah($signerPosition)) {
                $form['village_head_name'] = $signer?->name;
            }
        }

        $isLurah = $this->signedByLurah($signerPosition);
        $ktpMedia = $code === 'SPKTP' ? $this->ktpMedia($letterRequest) : [];

        // Pengkondisian Kop:
        // - Kalau yang ttd lurah: otomatis pakai kop lurah ($kalurahanKop = false)
        // - Kalau yang ttd selain lurah: otomatis pakai kop pemerintahan ($kalurahanKop = true)
        // - Template -gov atau template khusus pemerintahan selalu memakai kop pemerintahan.
        $kalurahanKop = ! $isLurah
            || $code === 'SPTKP'
            || in_array($templateSlug, ['general-statement-letter-gov', 'travel-permit-letter-gov'], true)
            || $this->usesKalurahanKop($templateSlug)
            || in_array($templateSlug, self::KOP_ALWAYS_KALURAHAN_SLUGS, true);

        // Kode formulir tetap (mis. "F-1.06"); diisi ke 2 key ('form_code' & 'kodeForm').
        $formCode = $form['kode_form'] ?? $letterRequest->letterType?->form_code ?? null;

        $eventDate = $form['event_date'] ?? $form['tanggal_kegiatan'] ?? null;
        $eventEndDate = $form['event_end_date'] ?? $form['tanggal_selesai_kegiatan'] ?? null;

        $tembusanList = (array) ($form['tembusan'] ?? $form['carbon_copy'] ?? $form['carbonCopies'] ?? []);
        if (empty($tembusanList)) {
            for ($i = 1; $i <= 10; $i++) {
                if (!empty($form['tembusan_' . $i])) {
                    $tembusanList[] = $form['tembusan_' . $i];
                }
            }
        }

        $data = [
            // Metadata PDF harus mengikuti nama resmi pada service type.
            'document_title' => $letterRequest->letterType?->letter_name,

            // 'number' (baru) dan 'nomor' (alias untuk blade lama).
            'number' => $number = $letterRequest->letter_number
                ?? $form['letter_number']
                ?? $form['nomor_surat']
                ?? $form['nomor']
                ?? $this->draftLetterNumber($letterRequest, $letterDate),
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
                'name' => $form['applicant_name'] ?? $letterRequest->applicant_name,
                'birth' => $this->birth($birthPlace, $birthDate)
                    ?: ($form['birth'] ?? $form['birth_place_date'] ?? $form['birthPlaceDate'] ?? null),
                // Tersedia sebagai nilai terpisah untuk template yang membaca birth_place / birth_date secara individual
                'birth_place' => $birthPlace,
                'birth_date' => $birthDate,
                'birth_date_raw' => $birthDate,
                'nik' => $letterRequest->applicant_nik,
                'kk_number' => $form['kk_number'] ?? $citizen?->kk_number,
                'gender' => $form['gender'] ?? $citizen?->gender,
                'marital_status' => $form['marital_status'] ?? $citizen?->marital_status,
                'religion' => $form['religion'] ?? $citizen?->religion,
                'occupation' => $form['occupation'] ?? $citizen?->occupation,
                'education' => $form['education'] ?? $citizen?->education,
                'nationality' => $form['nationality'] ?? ($citizen ? 'WNI' : null),
                'address' => $form['address'] ?? $form['applicant_address'] ?? $address,
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
            'purpose' => $form['purpose'] ?? $form['necessity'] ?? $form['keperluan'] ?? null,
            'destination' => $form['destination_agency'] ?? $form['destination'] ?? $form['pergi_ke'] ?? $form['tujuan'] ?? null,

            // surat keterangan keramaian + surat tindak lanjut permohonan izin
            'event' => [
                'day_name' => $eventDate
                    ? Carbon::parse($eventDate)->locale('id')->translatedFormat('l')
                    : null,
                'end_day_name' => $eventEndDate
                    ? Carbon::parse($eventEndDate)->locale('id')->translatedFormat('l')
                    : null,
                'date' => $eventDate,
                'date_text' => $eventDate ? $this->longDate($eventDate) : null,
                'end_date' => $eventEndDate,
                'end_date_text' => $eventEndDate ? $this->longDate($eventEndDate) : null,
                'time' => $form['event_time'] ?? $form['waktu_kegiatan'] ?? null,
                'end_time' => $form['event_end_time'] ?? $form['waktu_selesai_kegiatan'] ?? null,
                'place' => $form['event_place'] ?? $form['tempat_kegiatan'] ?? $form['tempat'] ?? null,
                'participants' => $form['event_participants'] ?? $form['peserta'] ?? null,
                'objective' => $form['event_objective'] ?? $form['nama_acara'] ?? $form['acara'] ?? null,
                'date_long' => $eventDate
                    ? Carbon::parse($eventDate)->locale('id')->translatedFormat('l, d F Y')
                    : null,
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
                'name' => $form['company_name'] ?? $form['companyName'] ?? null,
                'activity' => $form['business_activity'] ?? $form['businessActivity'] ?? null,
                'building_status' => $form['building_status'] ?? $form['buildingStatus'] ?? null,
                'building_use' => $form['building_use'] ?? $form['buildingUsage'] ?? $form['buildingUse'] ?? null,
                'person_in_charge' => $form['person_in_charge'] ?? $form['responsiblePerson'] ?? $form['personInCharge'] ?? null,
                'employee_count' => $form['employee_count'] ?? $form['employeeCount'] ?? null,
                'phone' => $form['phone'] ?? null,
                'address' => $form['domicile_address'] ?? $form['domicileAddress'] ?? null,
            ],

            // surat gugat cerai: pasangan yang digugat, alasan gugatan, dua saksi
            'spouse' => [
                'name'               => $form['spouse_name'] ?? null,
                // birth: coba gabungan birth_place+birth_date, fallback ke string gabungan
                'birth'              => $this->birth($form['spouse_birth_place'] ?? null, $form['spouse_birth_date'] ?? null)
                    ?? $form['spouse_birth'] ?? null,
                'occupation'         => $form['spouse_occupation'] ?? null,
                'address'            => isset($form['spouse_address']) ? $this->fullAddress($form['spouse_address']) : null,
                'marriage_cert_number' => $form['marriage_cert_number'] ?? null,
            ],
            'reasons'  => $form['divorce_reasons'] ?? [],
            'witnesses' => collect($form['witnesses'] ?? [])->map(fn ($w) => [
                'name'       => $w['name'] ?? null,
                // birth: coba gabungan birth_place+birth_date, fallback ke string gabungan 'birth'
                'birth'      => $this->birth($w['birth_place'] ?? null, $w['birth_date'] ?? null)
                    ?? $w['birth'] ?? null,
                'religion'   => $w['religion'] ?? null,
                'occupation' => $w['occupation'] ?? null,
                'address'    => isset($w['address']) ? $this->fullAddress($w['address']) : null,
            ])->all(),

            // surat pernyataan beda nama/identitas
            'other_identity' => [
                'name' => $form['other_name'] ?? null,
                'address' => $form['other_address'] ?? null,                
                'nik' => $form['other_nik'] ?? null,
            ],

            // surat pernyataan tanggung jawab mutlak perkawinan belum tercatat
            'husband' => [
                'name' => $form['husband_name'] ?? null,
                'nik' => $form['husband_nik'] ?? null,
                'birth' => $this->birth($form['husband_birth_place'] ?? null, $form['husband_birth_date'] ?? null),
                'occupation' => $form['husband_occupation'] ?? null,
                'address' => $form['husband_address'] ?? null,
            ],
            'wife' => [
                'name' => $form['wife_name'] ?? null,
                'nik' => $form['wife_nik'] ?? null,
                'birth' => $this->birth($form['wife_birth_place'] ?? null, $form['wife_birth_date'] ?? null),
                'occupation' => $form['wife_occupation'] ?? null,
                'address' => $form['wife_address'] ?? null,
            ],
            'marriage_date' => $this->longDate($form['marriage_date'] ?? null),            
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
                'name' => $form['nama_mahasiswa'] ?? $form['researcher_name'] ?? null,
                'nim' => $form['nim'] ?? $form['researcher_nim'] ?? null,
                'study_program' => $form['program_studi'] ?? $form['study_program'] ?? null,
                'faculty' => $form['fakultas'] ?? $form['faculty'] ?? null,
            ],

            // "Kepada Yth" (surat balasan penelitian, tindak lanjut izin, penawaran sewa, dll)
            'recipient' => $form['ditujukan_kepada'] ?? $form['recipient_name'] ?? $form['destination'] ?? $form['tenant_name'] ?? null,
            'recipient_address' => $form['lokasi_tujuan_surat'] ?? $form['recipient_address'] ?? $form['destination_address'] ?? $form['tenant_address'] ?? null,
            'ref_number' => $form['nomor_surat_asal'] ?? $form['ref_letter_number'] ?? $form['request_number'] ?? $form['nomor_surat_permohonan'] ?? null,

            // surat penawaran sewa kontrak gedung
            'representative' => [
                'name' => $form['representative_name'] ?? $form['representativeName'] ?? $form['nama_penawar'] ?? null,
                'position' => $form['representative_position'] ?? $form['representativePosition'] ?? $form['jabatan_penawar'] ?? null,
                'address' => $form['representative_address'] ?? $form['representativeAddress'] ?? $form['alamat_penawar'] ?? null,
            ],
            'lease' => [
                'building_name' => $form['nama_gedung'] ?? $form['lease_building_name'] ?? $form['property_name'] ?? null,
                'building_address' => $form['alamat_gedung'] ?? $form['lease_building_address'] ?? $form['property_address'] ?? null,
                'end_date' => $form['tanggal_berakhir_sewa_lama'] ?? $form['lease_end_date'] ?? $form['leaseEndDate'] ?? null,
                'annual_rent' => $form['harga_sewa_per_tahun'] ?? $form['lease_annual_rent'] ?? $form['leaseAnnualRent'] ?? null,
                'annual_rent_words' => $form['lease_annual_rent_words'] ?? $form['leaseAnnualRentWords'] ?? $form['annual_rent_words'] ?? null,
                'duration_years' => $form['lama_sewa_tahun'] ?? $form['lease_duration_years'] ?? $form['rent_period'] ?? null,
            ],

            // daftar "Tembusan Dikirim Kepada"
            'tembusan' => $tembusanList,
            'tembusan_1' => $form['tembusan_1'] ?? $tembusanList[0] ?? null,
            'tembusan_2' => $form['tembusan_2'] ?? $tembusanList[1] ?? null,
            'tembusan_3' => $form['tembusan_3'] ?? $tembusanList[2] ?? null,
            'tembusan_4' => $form['tembusan_4'] ?? $tembusanList[3] ?? null,
            'tembusan_5' => $form['tembusan_5'] ?? $tembusanList[4] ?? null,
            'tembusan_6' => $form['tembusan_6'] ?? $tembusanList[5] ?? null,
            'tembusan_7' => $form['tembusan_7'] ?? $tembusanList[6] ?? null,
            'tembusan_8' => $form['tembusan_8'] ?? $tembusanList[7] ?? null,
            'tembusan_9' => $form['tembusan_9'] ?? $tembusanList[8] ?? null,
            'tembusan_10' => $form['tembusan_10'] ?? $tembusanList[9] ?? null,

            // surat kuasa waris & surat kuasa administrasi kependudukan (field digabung)
            'attorney' => [
                'name' => $form['attorney_name'] ?? null,
                'nik' => $form['attorney_nik'] ?? null,
                'birth' => $this->birth($form['attorney_birth_place'] ?? null, $form['attorney_birth_date'] ?? null),
                'gender' => $form['attorney_gender'] ?? null,
                'occupation' => $form['attorney_occupation'] ?? null,
                'address' => $form['attorney_address'] ?? null,            
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

            // KIA (Kartu Identitas Anak)
            'kia' => $this->kiaApplicationFromForm($form, $letterRequest),

            // permohonan tinggal sementara
            'stayApplication' => $this->stayApplicationFromForm($form),

            // permohonan menjadi penduduk sementara (SKTS)
            'residentRequest' => $this->residentRequestFromForm($form),

            // formulir pendaftaran peristiwa kependudukan (F-1.02)
            'populationOccurrence' => $this->populationOccurrenceFromForm($form, [
                'name' => $letterRequest->applicant_name,
                'nik' => $letterRequest->applicant_nik,
                'kk_number' => $form['kk_number'] ?? $citizen?->kk_number,
            ], $letterDate),

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
                'type' => $this->normalizeApplicationType($form['application_type'] ?? null),
                'dukuh_name' => $form['dukuh_name'] ?? null,
            ],

            'ktp_media' => $ktpMedia,

            // ktp-application-form.blade.php menggunakan $signers['hamlet_head_name'], dsb.
            'signers' => [
                'hamlet_head_name'   => $form['nama_dukuh'] ?? $form['hamlet_head_name'] ?? $form['dukuh_name'] ?? null,
                'applicant_name'     => $form['nama_pemohon'] ?? $form['applicant_name'] ?? $letterRequest->applicant_name,
                'district_head_name' => $form['nama_camat'] ?? $form['district_head_name'] ?? null,
                'village_officer_name' => $form['nama_lurah'] ?? $form['village_officer_name'] ?? $signer?->name,
                'notary_name'        => $form['nama_notaris'] ?? $form['notary_name'] ?? null,
            ],

            'hamlet_head_name' => $form['dukuh_name'] ?? null,

            'letter_c' => [
                'hamlet' => $form['letter_c_hamlet'] ?? null,
                'owner_name' => $form['letter_c_owner_name'] ?? null,
            ],

            'land' => [
                'certificate_number' => $form['land_certificate_number'] ?? $form['certificate_number'] ?? $form['certificateNumber'] ?? null,
                'area' => $form['land_area'] ?? $form['area'] ?? null,
                'area_in_words' => $form['land_area_in_words'] ?? $form['area_in_words'] ?? $form['areaInWords'] ?? null,
                'owner_name' => $form['land_owner_name'] ?? $form['owner_name'] ?? $form['ownerName'] ?? null,
                'hamlet' => $form['land_hamlet'] ?? $form['hamlet'] ?? null,
                'village' => $form['land_village'] ?? $form['village'] ?? 'Bimomartani',
                'district' => $form['land_district'] ?? $form['district'] ?? 'Ngemplak',
                'regency' => $form['land_regency'] ?? $form['regency'] ?? $form['city'] ?? 'Sleman',
                'price_min' => $this->rupiah($form['land_price_min'] ?? $form['price_min'] ?? $form['priceMin'] ?? null) ?: ($form['land_price_min'] ?? $form['price_min'] ?? $form['priceMin'] ?? null),
                'price_max' => $this->rupiah($form['land_price_max'] ?? $form['price_max'] ?? $form['priceMax'] ?? null) ?: ($form['land_price_max'] ?? $form['price_max'] ?? $form['priceMax'] ?? null),
                'measurement_letter_number' => $form['land_measurement_letter_number'] ?? $form['measurement_letter_number'] ?? $form['measurementLetterNumber'] ?? null,
                'measurement_letter_date' => $this->longDate($form['land_measurement_letter_date'] ?? $form['measurement_letter_date'] ?? $form['measurementLetterDate'] ?? null) ?: ($form['land_measurement_letter_date'] ?? $form['measurement_letter_date'] ?? $form['measurementLetterDate'] ?? null),
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
                $this->shouldRenderDigitalSignature($letterRequest) ? $signer?->signature_image : null,
            ),
            'logo' => $this->asset('logo-sleman.png'),
            'kop' => $this->kopData($signerPosition, $kalurahanKop),
        ];

        // SPTJM kebenaran data kelahiran memakai beberapa blok orang yang
        // tidak dipakai oleh template umum. Bentuk data form publik/admin
        // sengaja dipetakan di sini agar preview admin dan PDF final membaca
        // field yang sama, baik key camelCase maupun alias backend lama.
        if ($code === 'SPTJMDK' || $templateSlug === 'birth-data-responsibility-statement') {
            $value = static function (array $keys, $fallback = null) use ($form) {
                foreach ($keys as $key) {
                    if (array_key_exists($key, $form) && filled($form[$key])) {
                        return $form[$key];
                    }
                }

                return $fallback;
            };

            $person = function (array $keys) use ($value): array {
                $birthPlace = $value($keys['birth_place'] ?? []);
                $birthDate = $value($keys['birth_date'] ?? []);

                return [
                    'name' => $value($keys['name'] ?? []),
                    'nik' => $value($keys['nik'] ?? []),
                    'birth' => $this->birth($birthPlace, $birthDate)
                        ?: $value($keys['birth'] ?? []),
                    'occupation' => $value($keys['occupation'] ?? []),
                    'address' => $this->fullAddress($value($keys['address'] ?? [])),
                ];
            };

            $data['applicant'] = array_merge($data['applicant'], $person([
                'name' => ['name', 'nama_pemohon'],
                'nik' => ['nik', 'applicant_nik'],
                'birth_place' => ['birth_place', 'tempat_lahir_pemohon'],
                'birth_date' => ['birth_date', 'tanggal_lahir_pemohon'],
                'birth' => ['birth', 'birth_place_date'],
                'occupation' => ['occupation', 'pekerjaan_pemohon'],
                'address' => ['address', 'alamat_pemohon', 'applicant_address'],
            ]));

            $data['child'] = $person([
                'name' => ['child_name', 'childName', 'nama_anak'],
                'nik' => ['child_nik', 'childNik'],
                'birth_place' => ['child_birth_place', 'childBirthPlace', 'tempat_lahir_anak'],
                'birth_date' => ['child_birth_date', 'childBirthDate', 'tanggal_lahir_anak'],
                'birth' => ['child_birth'],
                'occupation' => ['child_occupation', 'childOccupation', 'pekerjaan_anak'],
                'address' => ['child_address', 'childAddress', 'alamat_anak'],
            ]);

            $data['mother'] = $person([
                'name' => ['mother_name', 'motherName', 'nama_ibu'],
                'nik' => ['mother_nik', 'motherNik', 'nik_ibu'],
                'birth_place' => ['mother_birth_place', 'motherBirthPlace', 'tempat_lahir_ibu'],
                'birth_date' => ['mother_birth_date', 'motherBirthDate', 'tanggal_lahir_ibu'],
                'birth' => ['mother_birth'],
                'occupation' => ['mother_occupation', 'motherOccupation', 'pekerjaan_ibu'],
                'address' => ['mother_address', 'motherAddress', 'alamat_ibu'],
            ]);

            $data['father'] = $person([
                'name' => ['father_name', 'fatherName', 'nama_ayah'],
                'nik' => ['father_nik', 'fatherNik', 'nik_ayah'],
                'birth_place' => ['father_birth_place', 'fatherBirthPlace', 'tempat_lahir_ayah'],
                'birth_date' => ['father_birth_date', 'fatherBirthDate', 'tanggal_lahir_ayah'],
                'birth' => ['father_birth'],
                'occupation' => ['father_occupation', 'fatherOccupation', 'pekerjaan_ayah'],
                'address' => ['father_address', 'fatherAddress', 'alamat_ayah'],
            ]);

            $data['birth_attendant'] = $person([
                'name' => ['birth_attendant_name', 'birthAttendantName'],
                'nik' => ['birth_attendant_nik', 'birthAttendantNik'],
                'birth_place' => ['birth_attendant_birth_place', 'birthAttendantBirthPlace'],
                'birth_date' => ['birth_attendant_birth_date', 'birthAttendantBirthDate'],
                'birth' => ['birth_attendant_birth'],
                'occupation' => ['birth_attendant_occupation', 'birthAttendantOccupation'],
                'address' => ['birth_attendant_address', 'birthAttendantAddress'],
            ]);

            $data['witnesses'] = [
                [
                    'name' => $value(['witness1_name', 'witness1Name', 'nama_saksi_1']),
                    'nik' => $value(['witness1_nik', 'witness1Nik', 'nik_saksi_1']),
                ],
                [
                    'name' => $value(['witness2_name', 'witness2Name', 'nama_saksi_2']),
                    'nik' => $value(['witness2_nik', 'witness2Nik', 'nik_saksi_2']),
                ],
            ];
        }

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

        // Banyak template tambahan sudah memiliki struktur data contoh di
        // sampleViewData(), tetapi belum memiliki normalizer khusus. Isi hanya
        // bagian yang masih kosong agar preview request tetap terisi tanpa
        // menimpa data asli dari request.
        if (! array_key_exists($code, self::TEMPLATE_BY_CODE)) {
            $data = $this->fillEmptyData($this->sampleViewData($templateSlug), $data);
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
        // Surat pernikahan perempuan (n1, n2, judge-guardian, dst) ada di
        // resources/views/letters/marriage-women/letters/, jadi otomatis dipetakan ke sana.
        $folder = self::VIEW_FOLDERS[$template]
            ?? (array_key_exists($template, self::MARRIAGE_LETTERS) ? 'marriage-women.letters' : null);

        return 'letters.' . ($folder ? $folder . '.' : '') . $template;
    }

    private function fillEmptyData(array $defaults, array $data): array
    {
        foreach ($defaults as $key => $default) {
            if (! array_key_exists($key, $data) || $data[$key] === null || $data[$key] === '' || $data[$key] === []) {
                $data[$key] = $default;
                continue;
            }

            if (is_array($default) && is_array($data[$key])) {
                $data[$key] = $this->fillEmptyData($default, $data[$key]);
            }
        }

        return $data;
    }

    /** true jika view ini termasuk slug yang dicetak landscape. */
    private function isLandscape(string $view): bool
    {
        foreach (self::LANDSCAPE_SLUGS as $slug) {
            if ($view === $this->viewName($slug)) {
                return true;
            }
        }

        return false;
    }

    /** PDF asli hasil DomPDF (ini yang dicetak/di-download). */
    public function pdf(string $view, array $data): DomPdf
    {
        $view = $this->wrapMarriageWomenView($view, $data);
        $html = $this->renderDocumentHtml($view, $data);

        return Pdf::loadHtml($html)
            ->setPaper(self::PAPER, $this->isLandscape($view) ? 'landscape' : 'portrait');
    }

    /**
     * Menyamakan metadata title PDF dengan nama service type tanpa mengubah
     * judul resmi yang tercetak di dalam badan template.
     */
    private function renderDocumentHtml(string $view, array $data): string
    {
        $html = view($view, $data)->render();
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

    /**
     * Template surat pernikahan perempuan adalah partial yang memakai
     * @push('styles'). Saat dirender langsung, stack CSS tidak punya layout
     * untuk menampilkannya sehingga kop dan tabel jatuh ke style default HTML.
     * Samakan jalur render request admin dengan preview developer yang memakai
     * wrapper `letters.marriage-women.single`.
     */
    private function wrapMarriageWomenView(string $view, array &$data): string
    {
        $prefix = 'letters.marriage-women.letters.';

        if (! str_starts_with($view, $prefix)) {
            return $view;
        }

        $data['letter'] = substr($view, strlen($prefix));

        return 'letters.marriage-women.single';
    }

    /**
     * Menjembatani key field dinamis dari seeder dengan key yang dipakai Blade.
     * Key asli tetap dipertahankan di form agar request lama tetap kompatibel.
     */
    private function normalizeRootTemplateForm(LetterRequest $letterRequest, array $form): array
    {
        $code = strtoupper((string) ($letterRequest->letterType?->code ?? ''));
        // Form publik memakai key camelCase dan awalan "applicant". Tambahkan alias
        // snake_case dan bentuk tanpa awalan, tanpa menghapus key asli.
        $identityKeys = ['applicant_name', 'applicant_nik', 'applicant_address', 'applicant_phone'];

        foreach ($form as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            $snake = Str::snake($key);
            $candidates = [$snake];

            if (str_starts_with($snake, 'applicant_') && ! in_array($snake, $identityKeys, true)) {
                $candidates[] = substr($snake, strlen('applicant_'));
            }

            foreach ($candidates as $candidate) {
                if ($candidate !== $key && (! array_key_exists($candidate, $form) || blank($form[$candidate]))) {
                    $form[$candidate] = $value;
                }
            }
        }

        // Form publik menyebutnya "destination"; PDF membaca "destination_agency".
        if (blank($form['destination_agency'] ?? null) && filled($form['destination'] ?? null)) {
            $form['destination_agency'] = $form['destination'];
        }

        // Beberapa form publik mengirim pergi_ke/purposeLocation untuk destination.
        if (blank($form['destination_agency'] ?? null) && filled($form['purpose_location'] ?? null)) {
            $form['destination_agency'] = $form['purpose_location'];
        }
        if (blank($form['destination_agency'] ?? null) && filled($form['pergi_ke'] ?? null)) {
            $form['destination_agency'] = $form['pergi_ke'];
        }

        // Form publik kadang mengirim birthPlaceDate sebagai string gabungan "Tempat, Tgl Lahir".
        // Simpan sebagai birth_place_date sehingga viewData() bisa mengisi $applicant['birth']
        // langsung dari string ini bila birth_place / birth_date terpisah tidak ada.
        $combined = $form['birth_place_date'] ?? $form['birthPlaceDate'] ?? $form['birth'] ?? null;
        if (filled($combined)) {
            $form['birth'] ??= $combined;
            if (str_contains($combined, ',')) {
                [$p, $d] = array_map('trim', explode(',', $combined, 2));
                $form['birth_place'] ??= $p;
                $form['birth_date'] ??= $d;
            } elseif (blank($form['birth_place'] ?? null)) {
                $form['birth_place'] = $combined;
            }
        }

        // responsiblePerson (camelCase Vue) → responsible_person (sudah via normalizeKeys tapi pastikan)
        if (blank($form['responsible_person'] ?? null) && filled($form['responsiblePerson'] ?? null)) {
            $form['responsible_person'] = $form['responsiblePerson'];
        }

        // Register Legalisasi: form publik mengirim "people" (camelCase),
        // Blade membaca "rows" (snake_case).
        if ($code === 'RL' && is_array($form['people'] ?? null) && blank($form['rows'] ?? null)) {
            $form['rows'] = collect($form['people'])->map(function ($person) {
                $row = [];
                foreach ((array) $person as $key => $value) {
                    $row[Str::snake((string) $key)] = $value;
                }

                return $row;
            })->values()->all();
        }

        if ($code === 'SPDP') {
            $filled = fn ($row) => filled($row['before'] ?? null)
                || filled($row['after'] ?? null)
                || filled($row['basis'] ?? null);
        
            $familyNames = collect($form['family_members'] ?? $form['familyMembers'] ?? [])
                ->map(fn ($m) => mb_strtolower(trim((string) (((array) $m)['name'] ?? ''))))
                ->values()
                ->all();
            $familyCount = count($familyNames);
        
            // Nomor baris (mulai 0) anggota keluarga untuk satu baris perubahan.
            $slotOf = function (array $row) use ($familyNames): ?int {
                // 1) Indeks eksplisit dari form (kalau form publik mengirimnya, mulai 0).
                $explicit = $row['member_index'] ?? $row['memberIndex'] ?? null;
                if (is_numeric($explicit)) {
                    return isset($familyNames[(int) $explicit]) ? (int) $explicit : null;
                }
        
                $person = mb_strtolower(trim((string) ($row['name'] ?? '')));
                if ($person === '') {
                    return null;
                }
        
                // 2) Nama persis sama.
                foreach ($familyNames as $i => $name) {
                    if ($name === $person) {
                        return $i;
                    }
                }
        
                // 3) Nama saling mengandung ("Puji Yolanda S.H." ~ "Puji Yolanda").
                foreach ($familyNames as $i => $name) {
                    if ($name !== '' && (str_contains($person, $name) || str_contains($name, $person))) {
                        return $i;
                    }
                }
        
                return null;
            };
        
            $mergeRows = function (array $firstList, string $firstPrefix, array $secondList, string $secondPrefix) use ($filled, $slotOf, $familyCount) {
                $slots = [];  // baris yang cocok dengan anggota keluarga: [nomor baris => data]
                $extra = [];  // baris yang tidak cocok: ditaruh setelah baris anggota keluarga
        
                foreach ([[$firstPrefix, $firstList], [$secondPrefix, $secondList]] as [$prefix, $list]) {
                    foreach ($list as $index => $row) {
                        $row = (array) $row;
        
                        if (! $filled($row)) {
                            continue; // baris kosong dari form tidak dicetak
                        }
        
                        $slot = $slotOf($row);
        
                        if ($slot !== null) {
                            $entry = $slots[$slot] ?? [];
                        } else {
                            $name = mb_strtolower(trim((string) ($row['name'] ?? '')));
                            $extraKey = $name !== '' ? 'n:' . $name : $prefix . ':' . $index;
                            $entry = $extra[$extraKey] ?? [];
                        }
        
                        $entry[$prefix . '_before'] = $row['before'] ?? null;
                        $entry[$prefix . '_after'] = $row['after'] ?? null;
                        $entry[$prefix . '_basis'] = $row['basis'] ?? null;
        
                        // Keterangan hanya diisi kalau memang dikirim (mis. tanggal nikah).
                        if (filled($row['note'] ?? null)) {
                            $entry['note'] = $row['note'];
                        }
        
                        if ($slot !== null) {
                            $slots[$slot] = $entry;
                        } else {
                            $extra[$extraKey] = $entry;
                        }
                    }
                }
        
                // Baris ke-N sejajar dengan anggota keluarga ke-N; celah diisi baris kosong.
                $result = [];
                $lastSlot = $slots ? max(array_keys($slots)) : -1;
        
                for ($i = 0; $i <= $lastSlot; $i++) {
                    $result[] = $slots[$i] ?? [];
                }
        
                while (count($result) < $familyCount) {
                    $result[] = [];
                }
        
                foreach ($extra as $entry) {
                    $result[] = $entry;
                }
        
                return $result;
            };
        
            if (blank($form['education_job_changes'] ?? null)) {
                $form['education_job_changes'] = $mergeRows(
                    (array) ($form['education_changes'] ?? $form['educationChanges'] ?? []),
                    'education',
                    (array) ($form['job_changes'] ?? $form['jobChanges'] ?? []),
                    'job',
                );
            }
        
            if (blank($form['religion_other_changes'] ?? null)) {
                $otherList = (array) ($form['other_changes'] ?? $form['otherChanges'] ?? []);
        
                $form['religion_other_changes'] = $mergeRows(
                    (array) ($form['religion_changes'] ?? $form['religionChanges'] ?? []),
                    'religion',
                    $otherList,
                    'other',
                );
        
                $element = collect($otherList)->pluck('element')->filter()->first();
                if (filled($element) && blank($form['other_element_label'] ?? null)) {
                    $form['other_element_label'] = $element;
                }
            }
        }

        // Form publik memakai "necessity" untuk keperluan; surat membaca "purpose".
        if (blank($form['purpose'] ?? null) && filled($form['necessity'] ?? null)) {
            $form['purpose'] = $form['necessity'];
        }

        // Form admin lama memakai camelCase (childBirthPlace, akadTime, dst.),
        // sedangkan normalizer dan Blade memakai snake_case. Tambahkan alias
        // snake_case secara rekursif tanpa menghapus key asli agar request lama,
        // request baru, dan data array tetap kompatibel.
        $normalizeKeys = static function (mixed $value) use (&$normalizeKeys): mixed {
            if (! is_array($value)) {
                return $value;
            }

            $normalized = [];
            foreach ($value as $key => $item) {
                $item = $normalizeKeys($item);
                $normalized[$key] = $item;
                if (is_string($key)) {
                    $snake = strtolower((string) preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $key));
                    if ($snake !== $key && ! array_key_exists($snake, $normalized)) {
                        $normalized[$snake] = $item;
                    }
                }
            }

            return $normalized;
        };

        $form = $normalizeKeys($form);

        // Request manual lama menyimpan sebagian field dengan camelCase
        // (mis. birthDate/fatherName), sedangkan template PDF memakai
        // snake_case. Normalisasi dilakukan sebelum normalizer per-template
        // agar data lama dan data baru dirender dengan kontrak yang sama.
        $copy = static function (array &$target, string $canonical, array $sources): void {
            if (array_key_exists($canonical, $target) && $target[$canonical] !== null && $target[$canonical] !== '') {
                return;
            }

            foreach ($sources as $source) {
                if (array_key_exists($source, $target) && $target[$source] !== null && $target[$source] !== '') {
                    $target[$canonical] = $target[$source];
                    return;
                }
            }
        };

        $legacyAliases = [
            'applicant_name' => ['applicantName', 'name'],
            'applicant_nik' => ['applicantNik', 'nik'],
            'applicant_address' => ['applicantAddress', 'address'],
            'birth_place' => ['birthPlace', 'applicantBirthPlace'],
            'birth_date' => ['birthDate', 'applicantBirthDate'],
            'gender' => ['jenisKelamin', 'applicantGender'],
            'marital_status' => ['maritalStatus', 'applicantMaritalStatus'],
            'religion' => ['agama', 'applicantReligion'],
            'occupation' => ['pekerjaan', 'applicantOccupation'],
            'address' => ['applicantAddress'],
            'nationality' => ['applicantNationality'],
            'education' => ['education'],
            'purpose' => ['purpose'],
            'destination' => ['destination'],
            'father_name' => ['fatherName'],
            'father_nik' => ['fatherNik'],
            'father_bin' => ['fatherBin'],
            'father_birth_place' => ['fatherBirthPlace'],
            'father_birth_date' => ['fatherBirthDate'],
            'father_nationality' => ['fatherNationality'],
            'father_religion' => ['fatherReligion'],
            'father_occupation' => ['fatherOccupation'],
            'father_address' => ['fatherAddress'],
            'mother_name' => ['motherName'],
            'mother_nik' => ['motherNik'],
            'mother_binti' => ['motherBin', 'motherBinti'],
            'mother_birth_place' => ['motherBirthPlace'],
            'mother_birth_date' => ['motherBirthDate'],
            'mother_nationality' => ['motherNationality'],
            'mother_religion' => ['motherReligion'],
            'mother_occupation' => ['motherOccupation'],
            'mother_address' => ['motherAddress'],
            'previous_spouse_name' => ['previousSpouseName'],
            'previous_spouse_bin' => ['previousSpouseBin'],
            'previous_spouse_nik' => ['previousSpouseNik'],
            'previous_spouse_birth_place' => ['previousSpouseBirthPlace'],
            'previous_spouse_birth_date' => ['previousSpouseBirthDate'],
            'previous_spouse_death_date' => ['previousSpouseDeathDate'],
            'previous_spouse_death_place' => ['previousSpouseDeathPlace'],
            'groom_name' => ['groomName'],
            'groom_bin' => ['groomBin'],
            'groom_nik' => ['groomNik'],
            'groom_birth_place' => ['groomBirthPlace'],
            'groom_birth_date' => ['groomBirthDate'],
            'groom_citizenship' => ['groomNationality'],
            'groom_religion' => ['groomReligion'],
            'groom_occupation' => ['groomOccupation'],
            'groom_education' => ['groomEducation'],
            'groom_address' => ['groomAddress'],
            'groom_status' => ['groomStatus'],
            'bride_name' => ['brideName'],
            'bride_binti' => ['brideBinti'],
            'bride_nik' => ['brideNik'],
            'bride_birth_place' => ['brideBirthPlace'],
            'bride_birth_date' => ['brideBirthDate'],
            'bride_citizenship' => ['brideNationality'],
            'bride_religion' => ['brideReligion'],
            'bride_occupation' => ['brideOccupation'],
            'bride_education' => ['brideEducation'],
            'bride_address' => ['brideAddress'],
            'bride_status' => ['brideStatus'],
            'ceremony_day' => ['akadDay'],
            'ceremony_date' => ['akadDate'],
            'ceremony_time' => ['akadTime'],
            'ceremony_place' => ['akadPlace'],
            'deceased_name' => ['deceasedName'],
            'deceased_nik' => ['deceasedNik'],
            'deceased_birth_place' => ['deceasedBirthPlace'],
            'deceased_birth_date' => ['deceasedBirthDate'],
            'deceased_nationality' => ['deceasedNationality'],
            'deceased_religion' => ['deceasedReligion'],
            'deceased_occupation' => ['deceasedOccupation'],
            'deceased_address' => ['deceasedAddress'],
            'deceased_death_date' => ['deathDate'],
            'deceased_death_place' => ['deathPlace'],
            'deceased_binti' => ['deceasedBinti'],
            'applicant_bin' => ['applicantBin'],
            'spouse_name' => ['spouseName'],
            'spouse_nik' => ['spouseNik'],
            'spouse_birth_place' => ['spouseBirthPlace'],
            'spouse_birth_date' => ['spouseBirthDate'],
            'spouse_nationality' => ['spouseNationality'],
            'spouse_religion' => ['spouseReligion'],
            'spouse_occupation' => ['spouseOccupation'],
            'spouse_address' => ['spouseAddress'],
            'spouse_bin' => ['spouseBin'],
            'spouse_binti' => ['spouseBinti'],
            'behavior' => ['behavior'],
            'valid_until' => ['validUntil'],
            'additional_note' => ['additionalNote'],
            'registration_number' => ['registrationNumber'],
            'registration_date' => ['registrationDate'],
            'children' => ['children'],
            'family_members' => ['familyMembers'],
            'rows' => ['people'],
        ];

        foreach ($legacyAliases as $canonical => $sources) {
            $copy($form, $canonical, $sources);
        }

        $form = $this->normalizeComplexTemplateForm($code, $form);

        if ($code === 'N1P' || $code === 'SPTKP' || $code === 'SPBMLP' || $code === 'SKNNP') {
            $form['bride_name'] ??= $letterRequest->applicant_name;
        }

        if ($code === 'N1L') {
            $form['groom_name'] ??= $letterRequest->applicant_name;
        }
        if ($code === 'SKDPAK') {
            $form['condition'] ??= $form['grantor_condition'] ?? null;
        }

        $aliases = match ($code) {
            'SKBK' => [
                'birth_place' => 'tempat_lahir',
                'birth_date' => 'tanggal_lahir',
                'gender' => 'jenis_kelamin',
                'religion' => 'agama',
                'occupation' => 'pekerjaan',
            ],
            'SKU' => [
                'birth_place' => 'tempat_lahir',
                'birth_date' => 'tanggal_lahir',
                'gender' => 'jenis_kelamin',
                'marital_status' => 'status_perkawinan',
                'occupation' => 'pekerjaan',
                'business_type' => 'jenis_usaha',
                'business_address' => 'alamat_usaha',
                'purpose' => 'keperluan',
                'destination_agency' => 'instansi_tujuan',
            ],
            'SKUM' => [
                'birth_place' => 'tempat_lahir',
                'birth_date' => 'tanggal_lahir',
                'gender' => 'jenis_kelamin',
                'marital_status' => 'status_perkawinan',
                'religion' => 'agama',
                'occupation' => 'pekerjaan',
                'purpose' => 'menerangkan_bahwa',
            ],
            'SKD' => [
                'company_name' => 'nama_perusahaan',
                'business_activity' => 'jenis_usaha_kegiatan',
                'building_status' => 'status_bangunan',
                'building_use' => 'kegunaan_bangunan',
                'person_in_charge' => 'nama_penanggung_jawab',
                'employee_count' => 'jumlah_karyawan',
                'phone' => 'nomor_telepon_usaha',
                'domicile_address' => 'alamat_domisili_usaha',
                'name' => 'nama_pemilik',
                'nik' => 'nik_pemilik',
                'birth_place' => 'tempat_lahir_pemilik',
                'birth_date' => 'tanggal_lahir_pemilik',
                'gender' => 'jenis_kelamin_pemilik',
                'marital_status' => 'status_perkawinan',
                'religion' => 'agama_pemilik',
                'address' => 'alamat_pemilik',
            ],
            'SKTM' => [
                'birth_place' => 'tempat_lahir',
                'birth_date' => 'tanggal_lahir',
                'gender' => 'jenis_kelamin',
                'marital_status' => 'status_perkawinan',
                'religion' => 'agama',
                'occupation' => 'pekerjaan',
                'income' => 'penghasilan',
                'category' => 'kategori',
                'kkm_number' => 'nomor_kkm_krm',
                'purpose' => 'keperluan',
                'destination_agency' => 'instansi_tujuan',
            ],
            'SKP' => [
                'birth_place' => 'tempat_lahir',
                'birth_date' => 'tanggal_lahir',
                'marital_status' => 'status_perkawinan',
                'gender' => 'jenis_kelamin',
                'religion' => 'agama',
                'occupation' => 'pekerjaan',
                'income' => 'penghasilan',
                'category' => 'kategori',
                'kkm_number' => 'nomor_kkm_krm',
                'purpose' => 'dipergunakan_untuk',
            ],
            'SKK' => [
                'gender' => 'jenis_kelamin',
                'marital_status' => 'status_perkawinan',
                'religion' => 'agama',
                'occupation' => 'pekerjaan',
                'destination' => 'pergi_ke',
                'purpose' => 'keperluan',
                'event_date' => 'pada_hari_tanggal',
                'event_time' => 'jam',
                'event_place' => 'tempat',
                'event_participants' => 'peserta',
                'event_objective' => 'tujuan',
                'responsible_person' => 'penanggung_jawab',
            ],
            'SKJ' => [
                'birth_place' => 'tempat_lahir',
                'birth_date' => 'tanggal_lahir',
                'gender' => 'jenis_kelamin',
                'marital_status' => 'status_perkawinan',
                'religion' => 'agama',
                'occupation' => 'pekerjaan',
                'destination' => 'pergi_ke',
                'purpose' => 'maksud_dan_tujuan',
            ],
            'SKCK' => [
                'birth_place' => 'tempat_lahir',
                'birth_date' => 'tanggal_lahir',
                'gender' => 'jenis_kelamin',
                'marital_status' => 'status_perkawinan',
                'religion' => 'agama',
                'occupation' => 'pekerjaan',
                'destination' => 'pergi_ke',
                'purpose' => 'keperluan',
            ],
            'SKDPAK' => [
                'attorney_name' => 'nama_penerima_kuasa',
                'attorney_nik' => 'nik_penerima_kuasa',
                'attorney_birth_place' => 'tempat_lahir_penerima_kuasa',
                'attorney_birth_date' => 'tanggal_lahir_penerima_kuasa',
                'attorney_occupation' => 'pekerjaan_penerima_kuasa',
                'attorney_address' => 'alamat_penerima_kuasa',
                'condition' => 'alasan_kuasa',
            ],
            'SBP' => [
                'recipient_name' => 'ditujukan_kepada',
                'ref_letter_number' => 'nomor_surat_asal',
                'researcher_name' => 'nama_mahasiswa',
                'researcher_nim' => 'nim',
                'study_program' => 'program_studi',
                'faculty' => 'fakultas',
            ],
            default => [],
        };

        foreach ($aliases as $canonicalKey => $sourceKey) {
            if (! array_key_exists($canonicalKey, $form) && array_key_exists($sourceKey, $form)) {
                $form[$canonicalKey] = $form[$sourceKey];
            }
        }

        return $form;
    }

    /**
     * Field seeder menggunakan label Indonesia, sedangkan beberapa template
     * lama memakai kontrak data berbahasa Inggris dan struktur bertingkat.
     * Normalisasi ini menambahkan alias canonical tanpa menghapus key asli,
     * sehingga request lama dan request baru tetap kompatibel.
     */
    private function normalizeComplexTemplateForm(string $code, array $form): array
    {
        $set = static function (array &$target, string $canonical, array $sources): void {
            if (array_key_exists($canonical, $target) && $target[$canonical] !== null && $target[$canonical] !== '') {
                return;
            }

            foreach ($sources as $source) {
                if (array_key_exists($source, $target) && $target[$source] !== null && $target[$source] !== '') {
                    $target[$canonical] = $target[$source];
                    return;
                }
            }
        };

        $marriageCodes = [
            'PNP', 'N2P', 'PNL', 'N1P', 'N4P', 'N5P', 'N6P',
            'SKWNP', 'SKWHP', 'SPTKP', 'SPBMLP', 'SKNNP', 'N1L', 'N4L',
            // Kode katalog baru untuk surat nikah individual. Template-nya
            // tetap memakai struktur marriedLetterData yang sama.
            'SPCCP', 'SKKMP', 'SKUMN', 'SPNIK', 'SPNIP', 'SKTNB',
            'FPDN', 'SKBKN', 'SPBML', 'SPOT', 'SPTJP', 'SKBK2',
        ];

        if (in_array($code, $marriageCodes, true)) {
            $people = [
                'bride' => [
                    'name' => ['nama_mempelai_wanita', 'nama_catin_putri', 'nama_calon_istri'],
                    'bin' => ['binti_mempelai_wanita', 'binti_catin_putri', 'binti_calon_istri'],
                    'nik' => ['nik_mempelai_wanita', 'nik_catin_putri', 'nik_calon_istri'],
                    'birth_place' => ['tempat_lahir_mempelai_wanita', 'tempat_lahir_catin_putri', 'tempat_lahir_calon_istri', 'ttl_mempelai_wanita', 'ttl_catin_putri', 'ttl_calon_istri'],
                    'birth_date' => ['tanggal_lahir_mempelai_wanita', 'tanggal_lahir_catin_putri', 'tanggal_lahir_calon_istri'],
                    'citizenship' => ['kewarganegaraan_mempelai_wanita', 'kewarganegaraan_catin_putri', 'kewarganegaraan_calon_istri'],
                    'religion' => ['agama_mempelai_wanita', 'agama_catin_putri', 'agama_calon_istri'],
                    'occupation' => ['pekerjaan_mempelai_wanita', 'pekerjaan_catin_putri', 'pekerjaan_calon_istri'],
                    'education' => ['pendidikan_mempelai_wanita', 'pendidikan_catin_putri', 'pendidikan_calon_istri'],
                    'address' => ['alamat_mempelai_wanita', 'alamat_catin_putri', 'alamat_calon_istri'],
                    'status' => ['status_mempelai_wanita', 'status_catin_putri', 'status_calon_istri'],
                ],
                'groom' => [
                    'name' => ['nama_mempelai_pria', 'nama_catin_putra', 'nama_catin_pria', 'nama_calon_suami'],
                    'bin' => ['bin_mempelai_pria', 'bin_catin_putra', 'bin_catin_pria', 'bin_calon_suami'],
                    'nik' => ['nik_mempelai_pria', 'nik_catin_putra', 'nik_catin_pria', 'nik_calon_suami'],
                    'birth_place' => ['tempat_lahir_mempelai_pria', 'tempat_lahir_catin_putra', 'tempat_lahir_catin_Putra', 'tempat_lahir_catin_pria', 'tempat_lahir_calon_suami', 'ttl_mempelai_pria', 'ttl_catin_putra', 'ttl_catin_pria', 'ttl_calon_suami'],
                    'birth_date' => ['tanggal_lahir_mempelai_pria', 'tanggal_lahir_catin_putra', 'tanggal_lahir_catin_Putra', 'tanggal_lahir_catin_pria', 'tanggal_lahir_calon_suami'],
                    'citizenship' => ['kewarganegaraan_mempelai_pria', 'kewarganegaraan_catin_putra', 'kewarganegaraan_catin_Putra', 'kewarganegaraan_catin_pria', 'kewarganegaraan_calon_suami'],
                    'religion' => ['agama_mempelai_pria', 'agama_catin_putra', 'agama_catin_pria', 'agama_calon_suami'],
                    'occupation' => ['pekerjaan_mempelai_pria', 'pekerjaan_catin_putra', 'pekerjaan_catin_pria', 'pekerjaan_calon_suami'],
                    'education' => ['pendidikan_mempelai_pria', 'pendidikan_catin_putra', 'pendidikan_catin_pria', 'pendidikan_calon_suami'],
                    'address' => ['alamat_mempelai_pria', 'alamat_catin_putra', 'alamat_catin_pria', 'alamat_calon_suami'],
                    'status' => ['status_mempelai_pria', 'status_catin_putra', 'status_catin_pria', 'status_calon_suami'],
                ],
                'bride_father' => [
                    'name' => ['nama_ayah_putri', 'nama_ayah'],
                    'bin' => ['bin_ayah_putri', 'bin_ayah'],
                    'nik' => ['nik_ayah_putri', 'nik_ayah'],
                    'birth_place' => ['ttl_ayah_putri', 'ttl_ayah'],
                    'citizenship' => ['kewarganegaraan_ayah_putri', 'kewarganegaraan_ayah'],
                    'religion' => ['agama_ayah_putri', 'agama_ayah'],
                    'occupation' => ['pekerjaan_ayah_putri', 'pekerjaan_ayah'],
                    'address' => ['alamat_ayah_putri', 'alamat_ayah'],
                ],
                'groom_father' => [
                    'name' => ['nama_ayah_putra', 'nama_ayah_catin_putra', 'nama_ayah'],
                    'bin' => ['bin_ayah_putra', 'bin_ayah_catin_putra', 'bin_ayah'],
                    'nik' => ['nik_ayah_putra', 'nik_ayah_catin_putra', 'nik_ayah'],
                    'birth_place' => ['ttl_ayah_putra', 'ttl_ayah_catin_putra', 'ttl_ayah'],
                    'citizenship' => ['kewarganegaraan_ayah_putra', 'kewarganegaraan_ayah_catin_putra', 'kewarganegaraan_ayah'],
                    'religion' => ['agama_ayah_putra', 'agama_ayah_catin_putra', 'agama_ayah'],
                    'occupation' => ['pekerjaan_ayah_putra', 'pekerjaan_ayah_catin_putra', 'pekerjaan_ayah'],
                    'address' => ['alamat_ayah_putra', 'alamat_ayah_catin_putra', 'alamat_ayah'],
                ],
                'bride_mother' => [
                    'name' => ['nama_ibu_putri', 'nama_ibu'],
                    'bin' => ['binti_ibu_putri', 'binti_ibu'],
                    'nik' => ['nik_ibu_putri', 'nik_ibu'],
                    'birth_place' => ['ttl_ibu_putri', 'ttl_ibu'],
                    'citizenship' => ['kewarganegaraan_ibu_putri', 'kewarganegaraan_ibu'],
                    'religion' => ['agama_ibu_putri', 'agama_ibu'],
                    'occupation' => ['pekerjaan_ibu_putri', 'pekerjaan_ibu'],
                    'address' => ['alamat_ibu_putri', 'alamat_ibu'],
                ],
                'groom_mother' => [
                    'name' => ['nama_ibu_putra', 'nama_ibu_catin_putra', 'nama_ibu'],
                    'bin' => ['binti_ibu_putra', 'binti_ibu_catin_putra', 'binti_ibu'],
                    'nik' => ['nik_ibu_putra', 'nik_ibu_catin_putra', 'nik_ibu'],
                    'birth_place' => ['ttl_ibu_putra', 'ttl_ibu_catin_putra', 'ttl_ibu'],
                    'citizenship' => ['kewarganegaraan_ibu_putra', 'kewarganegaraan_ibu_catin_putra', 'kewarganegaraan_ibu'],
                    'religion' => ['agama_ibu_putra', 'agama_ibu_catin_putra', 'agama_ibu'],
                    'occupation' => ['pekerjaan_ibu_putra', 'pekerjaan_ibu_catin_putra', 'pekerjaan_ibu'],
                    'address' => ['alamat_ibu_putra', 'alamat_ibu_catin_putra', 'alamat_ibu'],
                ],
                'guardian' => [
                    'name' => ['nama_wali'],
                    'bin' => ['bin_wali'],
                    'nik' => ['nik_wali'],
                    'birth_place' => ['ttl_wali'],
                    'citizenship' => ['kewarganegaraan_wali'],
                    'religion' => ['agama_wali'],
                    'occupation' => ['pekerjaan_wali'],
                    'address' => ['alamat_wali'],
                ],
            ];

            foreach ($people as $person => $fields) {
                foreach ($fields as $field => $sources) {
                    $set($form, "{$person}_{$field}", $sources);
                }
            }

            $set($form, 'akad_day', ['hari_akad']);
            $set($form, 'akad_date', ['tanggal_akad']);
            $set($form, 'akad_time', ['jam_akad']);
            $set($form, 'akad_place', ['tempat_akad']);
            $set($form, 'guardian_relation', ['hubungan_wali', 'hubungan_wali_nasab']);
            $set($form, 'guardian_reason', ['sebab_wali_bukan_ayah', 'sebab_wali_hakim']);
            $set($form, 'purpose', ['keperluan']);

            // Field pada katalog baru sudah memakai nama Inggris/canonical,
            // tetapi sebagian template lama membaca nama yang lebih spesifik.
            // Tambahkan alias tanpa menghapus key asli dari form_data.
            $set($form, 'applicant_bin', ['bin_or_binti', 'applicantBin']);
            $set($form, 'applicant_nationality', ['nationality']);
            $set($form, 'applicant_behavior', ['behavior']);
            $set($form, 'applicant_valid_until', ['valid_until']);
            $set($form, 'applicant_additional_note', ['additional_note']);

            if (in_array($code, ['SPTJP'], true)) {
                $form['marriage_witnesses'] ??= array_values(array_filter([
                    [
                        'name' => $form['witness1_name'] ?? null,
                        'nik' => $form['witness1_nik'] ?? null,
                    ],
                    [
                        'name' => $form['witness2_name'] ?? null,
                        'nik' => $form['witness2_nik'] ?? null,
                    ],
                ], static fn (array $row): bool => filled($row['name'] ?? null) || filled($row['nik'] ?? null)));

                if (isset($form['children']) && is_array($form['children'])) {
                    $form['children'] = array_map(static fn (array $child): array => [
                        'name' => $child['name'] ?? null,
                        'birth_cert_number' => $child['birth_cert_number'] ?? $child['birthCertificateNumber'] ?? null,
                        'shdk' => $child['shdk'] ?? null,
                    ], $form['children']);
                }
            }

            // N1 memakai data diri umum untuk satu calon pengantin.
            if ($code === 'N1P') {
                $set($form, 'bride_gender', ['jenis_kelamin']);
                $set($form, 'bride_birth_place', ['tempat_lahir']);
                $set($form, 'bride_birth_date', ['tanggal_lahir']);
                $set($form, 'bride_citizenship', ['kewarganegaraan']);
                $set($form, 'bride_religion', ['agama']);
                $set($form, 'bride_occupation', ['pekerjaan']);
                $set($form, 'bride_education', ['pendidikan_terakhir']);
                $set($form, 'bride_status', ['status_pernikahan']);
                $set($form, 'ex_husband_name', ['nama_pasangan_terdahulu']);
                $set($form, 'marital_status', ['status_pernikahan']);
                $form['gender'] = 'Perempuan';
                $form['bride_gender'] = 'Perempuan';
                $form['marital_status'] = $this->normalizeN1Status(
                    $form['bride_status'] ?? $form['marital_status'] ?? null,
                    'N1P',
                );
                $form['bride_status'] = $form['marital_status'];
            }

            if ($code === 'N1L') {
                $set($form, 'groom_gender', ['jenis_kelamin']);
                $set($form, 'groom_birth_place', ['tempat_lahir']);
                $set($form, 'groom_birth_date', ['tanggal_lahir']);
                $set($form, 'groom_citizenship', ['kewarganegaraan']);
                $set($form, 'groom_religion', ['agama']);
                $set($form, 'groom_occupation', ['pekerjaan']);
                $set($form, 'groom_education', ['pendidikan_terakhir']);
                $set($form, 'groom_status', ['status_perkawinan']);
                $set($form, 'previous_spouse_name', ['nama_pasangan_terdahulu']);

                // Template laki-laki memakai blok applicant/father/mother,
                // sedangkan field katalog N1L memakai nama field Indonesia.
                $set($form, 'birth_place', ['tempat_lahir']);
                $set($form, 'birth_date', ['tanggal_lahir']);
                $set($form, 'gender', ['jenis_kelamin']);
                $set($form, 'nationality', ['kewarganegaraan']);
                $set($form, 'education', ['pendidikan_terakhir']);
                $set($form, 'marital_status', ['status_perkawinan']);
                $set($form, 'father_name', ['nama_ayah']);
                $set($form, 'father_nik', ['nik_ayah']);
                $set($form, 'father_birth_place', ['ttl_ayah']);
                $set($form, 'father_nationality', ['kewarganegaraan_ayah']);
                $set($form, 'father_religion', ['agama_ayah']);
                $set($form, 'father_occupation', ['pekerjaan_ayah']);
                $set($form, 'father_address', ['alamat_ayah']);
                $set($form, 'mother_name', ['nama_ibu']);
                $set($form, 'mother_nik', ['nik_ibu']);
                $set($form, 'mother_birth_place', ['ttl_ibu']);
                $set($form, 'mother_nationality', ['kewarganegaraan_ibu']);
                $set($form, 'mother_religion', ['agama_ibu']);
                $set($form, 'mother_occupation', ['pekerjaan_ibu']);
                $set($form, 'mother_address', ['alamat_ibu']);

                // N1L adalah varian untuk calon pengantin laki-laki. Kode
                // surat menjadi sumber kebenaran agar data seed lama yang
                // kebetulan memilih opsi gender perempuan tidak membuat
                // baris status tampil pada bagian yang salah.
                $form['gender'] = 'Laki-laki';
                $form['groom_gender'] = 'Laki-laki';
                $form['marital_status'] = $this->normalizeN1Status($form['marital_status'] ?? null, 'N1L');
                $form['groom_status'] = $form['marital_status'];
            }

            // SPNIP adalah kode baru untuk Pengantar Nikah laki-laki. Form
            // admin lama masih memakai prefix father/mother, sedangkan blok
            // Blade married-man membaca data calon suami dan orang tuanya.
            if ($code === 'SPNIP') {
                $set($form, 'groom_gender', ['gender', 'jenis_kelamin']);
                $set($form, 'groom_birth_place', ['birth_place', 'tempat_lahir']);
                $set($form, 'groom_birth_date', ['birth_date', 'tanggal_lahir']);
                $set($form, 'groom_citizenship', ['nationality', 'kewarganegaraan']);
                $set($form, 'groom_religion', ['religion', 'agama']);
                $set($form, 'groom_occupation', ['occupation', 'pekerjaan']);
                $set($form, 'groom_education', ['education', 'pendidikan_terakhir']);
                $set($form, 'groom_status', ['marital_status', 'status_perkawinan']);
                $set($form, 'groom_address', ['address', 'alamat']);
                $set($form, 'previous_spouse_name', ['nama_pasangan_terdahulu']);
                $set($form, 'father_name', ['nama_ayah']);
                $set($form, 'father_nik', ['nik_ayah']);
                $set($form, 'father_birth_place', ['tempat_lahir_ayah', 'ttl_ayah']);
                $set($form, 'father_birth_date', ['tanggal_lahir_ayah']);
                $set($form, 'father_nationality', ['kewarganegaraan_ayah']);
                $set($form, 'father_religion', ['agama_ayah']);
                $set($form, 'father_occupation', ['pekerjaan_ayah']);
                $set($form, 'father_address', ['alamat_ayah']);
                $set($form, 'mother_name', ['nama_ibu']);
                $set($form, 'mother_nik', ['nik_ibu']);
                $set($form, 'mother_birth_place', ['tempat_lahir_ibu', 'ttl_ibu']);
                $set($form, 'mother_birth_date', ['tanggal_lahir_ibu']);
                $set($form, 'mother_nationality', ['kewarganegaraan_ibu']);
                $set($form, 'mother_religion', ['agama_ibu']);
                $set($form, 'mother_occupation', ['pekerjaan_ibu']);
                $set($form, 'mother_address', ['alamat_ibu']);
            }

            // N6 adalah varian surat kematian dalam folder template nikah.
            if ($code === 'N6P') {
                $set($form, 'ex_husband_name', ['nama_almarhum']);
                $set($form, 'ex_husband_bin', ['bin_binti_almarhum']);
                $set($form, 'ex_husband_nik', ['nik_almarhum']);
                $set($form, 'ex_husband_birth_place', ['ttl_almarhum']);
                $set($form, 'ex_husband_citizenship', ['kewarganegaraan_almarhum']);
                $set($form, 'ex_husband_religion', ['agama_almarhum']);
                $set($form, 'ex_husband_occupation', ['pekerjaan_almarhum']);
                $set($form, 'ex_husband_address', ['alamat_almarhum']);
                $set($form, 'ex_husband_died_at', ['tanggal_meninggal']);
                $set($form, 'ex_husband_died_place', ['tempat_meninggal']);
                $set($form, 'bride_name', ['nama_pasangan']);
                $set($form, 'bride_bin', ['bin_binti_pasangan']);
                $set($form, 'bride_nik', ['nik_pasangan']);
                $set($form, 'bride_birth_place', ['ttl_pasangan']);
                $set($form, 'bride_citizenship', ['kewarganegaraan_pasangan']);
                $set($form, 'bride_religion', ['agama_pasangan']);
                $set($form, 'bride_occupation', ['pekerjaan_pasangan']);
                $set($form, 'bride_address', ['alamat_pasangan']);
            }

            // Surat kesehatan dan pernyataan belum menikah memakai satu
            // pemohon, tetapi Blade lama membaca blok bride.
            if (in_array($code, ['SPTKP', 'SPBMLP', 'SKBK2'], true)) {
                $set($form, 'bride_name', ['name', 'applicant_name', 'nama']);
                $set($form, 'bride_nik', ['nik', 'applicant_nik']);
                $set($form, 'bride_address', ['address', 'applicant_address', 'alamat']);
                $set($form, 'bride_gender', ['gender', 'jenis_kelamin']);
                $set($form, 'bride_birth_place', ['birth_place', 'tempat_lahir']);
                $set($form, 'bride_birth_date', ['birth_date', 'tanggal_lahir']);
                $set($form, 'bride_citizenship', ['nationality', 'citizenship', 'kewarganegaraan']);
                $set($form, 'bride_religion', ['religion', 'agama']);
                $set($form, 'bride_education', ['education', 'pendidikan', 'pendidikan_terakhir']);
                $set($form, 'bride_occupation', ['occupation', 'pekerjaan']);
                $set($form, 'bride_status', ['marital_status', 'status_perkawinan']);
                $set($form, 'health_destination', ['destination', 'tujuan_ke']);
                $set($form, 'health_need', ['need', 'keperluan']);
                $set($form, 'health_note', ['note', 'keterangan_lain']);
                $set($form, 'health_conduct', ['conduct', 'kelakuan']);
                $set($form, 'health_valid_from', ['valid_from', 'berlaku_mulai']);
                $set($form, 'health_valid_until', ['valid_until', 'berlaku_sampai']);
            }

            if ($code === 'SKBK2') {
                $form['unmarried_certificate_number'] ??= $form['number'] ?? $form['letter_number'] ?? null;
                $form['unmarried_certificate_date'] ??= $form['letter_date'] ?? null;
            }

            if ($code === 'SKNNP') {
                $set($form, 'bride_name', ['nama_warga']);
                $set($form, 'bride_bin', ['bin_binti_warga']);
                $set($form, 'bride_birth_place', ['tempat_lahir_warga']);
                $set($form, 'bride_birth_date', ['tanggal_lahir_warga']);
                $set($form, 'bride_citizenship', ['kewarganegaraan_warga']);
                $set($form, 'bride_religion', ['agama_warga']);
                $set($form, 'bride_occupation', ['pekerjaan_warga']);
                $set($form, 'bride_education', ['pendidikan_warga']);
                $set($form, 'groom_name', ['nama_calon_pasangan']);
                $set($form, 'groom_bin', ['bin_binti_calon_pasangan']);
                $set($form, 'groom_nik', ['nik_calon_pasangan']);
                $set($form, 'groom_birth_place', ['ttl_calon_pasangan']);
                $set($form, 'groom_citizenship', ['kewarganegaraan_calon_pasangan']);
                $set($form, 'groom_religion', ['agama_calon_pasangan']);
                $set($form, 'groom_occupation', ['pekerjaan_calon_pasangan']);
                $set($form, 'groom_address', ['alamat_calon_pasangan']);
            }
        }

        $birthCodes = ['SPAKL', 'LPKL', 'PAK', 'FPK', 'LK', 'SKAK', 'PPKT', 'LKLD', 'SKKL'];

        if (in_array($code, $birthCodes, true)) {
            $birthAliases = [
                'child_nik' => ['nik_anak'],
                'child_name' => ['nama_anak'],
                'child_gender' => ['jenis_kelamin_anak'],
                'child_delivery_place' => ['tempat_dilahirkan'],
                'child_delivery_address' => ['alamat_rs'],
                'child_birth_place' => ['tempat_kelahiran_kota', 'tempat_lahir_anak'],
                'child_birth_date' => ['tanggal_lahir_anak', 'hari_tanggal_lahir'],
                'child_birth_time' => ['jam_kelahiran'],
                'child_plurality' => ['jenis_kelahiran'],
                'child_birth_order' => ['kelahiran_ke', 'anak_ke'],
                'child_birth_attendant' => ['penolong_kelahiran'],
                'child_weight' => ['berat_bayi'],
                'child_length' => ['panjang_bayi'],
                'child_delivery_method' => ['cara_kelahiran'],
                'child_delivery_cost' => ['biaya_kelahiran'],
                'mother_nik' => ['nik_ibu'],
                'mother_name' => ['nama_ibu'],
                'mother_birth_place' => ['ttl_ibu'],
                'mother_birth_date' => ['tanggal_lahir_ibu'],
                'mother_occupation' => ['pekerjaan_ibu'],
                'mother_address' => ['alamat_ibu'],
                'mother_rt' => ['rt_ibu'],
                'mother_rw' => ['rw_ibu'],
                'mother_nationality' => ['kewarganegaraan_ibu'],
                'father_nik' => ['nik_ayah'],
                'father_name' => ['nama_ayah'],
                'father_birth_place' => ['ttl_ayah'],
                'father_birth_date' => ['tanggal_lahir_ayah'],
                'father_occupation' => ['pekerjaan_ayah'],
                'father_address' => ['alamat_ayah'],
                'father_rt' => ['rt_ayah'],
                'father_rw' => ['rw_ayah'],
                'father_nationality' => ['kewarganegaraan_ayah'],
                'reporter_nik' => ['nik_pelapor'],
                'reporter_name' => ['nama_pelapor'],
                'reporter_birth_place' => ['ttl_pelapor'],
                'reporter_age' => ['umur_pelapor'],
                'reporter_occupation' => ['pekerjaan_pelapor'],
                'reporter_address' => ['alamat_pelapor'],
                'report_date' => ['tanggal_lapor'],
                'marriage_cert_number' => ['nomor_akta_perkawinan_ortu'],
                'marriage_date' => ['tanggal_pernikahan_ortu'],
            ];

            foreach ($birthAliases as $canonical => $sources) {
                $set($form, $canonical, $sources);
            }

            $set($form, 'kk_number', ['nomor_kk', 'family_card_number']);
            $set($form, 'marriage_cert_number', ['nomor_akta_perkawinan_ortu', 'marriage_certificate_number']);
            $set($form, 'head_of_family_name', ['nama_kepala_keluarga', 'head_of_family_name']);
            $set($form, 'reporter_phone', ['kontak_pelapor', 'reporter_phone']);
            $set($form, 'application_date', ['tanggal_lapor', 'report_date']);

            if (! array_key_exists('birth_witnesses', $form)) {
                $form['birth_witnesses'] = [
                    [
                        'nik' => $form['nik_saksi_1'] ?? $form['witness1_nik'] ?? $form['witness_1_nik'] ?? null,
                        'name' => $form['nama_saksi_1'] ?? $form['witness1_name'] ?? $form['witness_1_name'] ?? null,
                        'age' => $form['umur_saksi_1'] ?? $form['witness1_age'] ?? $form['witness_1_age'] ?? null,
                        'address' => $form['alamat_saksi_1'] ?? $form['witness1_address'] ?? $form['witness_1_address'] ?? null,
                    ],
                    [
                        'nik' => $form['nik_saksi_2'] ?? $form['witness2_nik'] ?? $form['witness_2_nik'] ?? null,
                        'name' => $form['nama_saksi_2'] ?? $form['witness2_name'] ?? $form['witness_2_name'] ?? null,
                        'age' => $form['umur_saksi_2'] ?? $form['witness2_age'] ?? $form['witness_2_age'] ?? null,
                        'address' => $form['alamat_saksi_2'] ?? $form['witness2_address'] ?? $form['witness_2_address'] ?? null,
                    ],
                ];
            }

            $form['birth_type'] ??= match ($code) {
                'PPKT' => 'late',
                'LKLD' => 'out_of_domicile',
                default => 'new',
            };
        }

        if ($code === 'SPTJMPSI') {
            $set($form, 'husband_name', ['nama_suami', 'nama_pihak_pertama']);
            $set($form, 'husband_nik', ['nik_suami', 'nik_pihak_pertama']);
            $set($form, 'husband_birth_place', ['tempat_lahir_suami']);
            $set($form, 'husband_birth_date', ['tanggal_lahir_suami']);
            $set($form, 'husband_occupation', ['pekerjaan_suami']);
            $set($form, 'husband_address', ['alamat_suami']);
            $set($form, 'wife_name', ['nama_istri', 'nama_pihak_kedua']);
            $set($form, 'wife_nik', ['nik_istri', 'nik_pihak_kedua']);
            $set($form, 'wife_birth_place', ['tempat_lahir_istri']);
            $set($form, 'wife_birth_date', ['tanggal_lahir_istri']);
            $set($form, 'wife_occupation', ['pekerjaan_istri']);
            $set($form, 'wife_address', ['alamat_istri']);
            $set($form, 'marriage_date', ['tanggal_perkawinan', 'tanggal_pernikahan']);
        

            if (empty($form['marriage_witnesses'])) {
                $form['marriage_witnesses'] = [
                    [
                        'name' => $form['nama_saksi_1'] ?? null,
                        'nik' => $form['nik_saksi_1'] ?? null,
                    ],
                    [
                        'name' => $form['nama_saksi_2'] ?? null,
                        'nik' => $form['nik_saksi_2'] ?? null,
                    ],
                ];
            }
            
        
            if (! array_key_exists('children', $form)) {
                $form['children'] = collect(range(1, 7))
                    ->map(fn ($i) => [
                        'name' => $form["nama_anak_{$i}"] ?? null,
                        'birth_cert_number' => $form["nomor_akta_anak_{$i}"] ?? $form["akta_kelahiran_anak_{$i}"] ?? null,
                        'shdk' => $form["shdk_anak_{$i}"] ?? null,
                    ])
                    ->filter(fn ($c) => filled($c['name']))
                    ->values()
                    ->all();
            }
        }

        // SPTJP: form publik mengirim saksi sebagai field datar (witness1Name, ...)
        // dan nomor akta anak sebagai birthCertificateNumber.
        if ($code === 'SPTJP') {
            if (blank($form['marriage_witnesses'] ?? null)) {
                $form['marriage_witnesses'] = [
                    [
                        'name' => $form['witness1_name'] ?? $form['witness1Name'] ?? null,
                        'nik' => $form['witness1_nik'] ?? $form['witness1Nik'] ?? null,
                    ],
                    [
                        'name' => $form['witness2_name'] ?? $form['witness2Name'] ?? null,
                        'nik' => $form['witness2_nik'] ?? $form['witness2Nik'] ?? null,
                    ],
                ];
            }

            if (is_array($form['children'] ?? null)) {
                $form['children'] = collect($form['children'])->map(function ($child) {
                    $child = (array) $child;

                    if (blank($child['birth_cert_number'] ?? null)) {
                        $child['birth_cert_number'] = $child['birthCertificateNumber']
                            ?? $child['birth_certificate_number']
                            ?? null;
                    }

                    return $child;
                })->all();
            }
        }

        if ($code === 'SMLPI') {
            $set($form, 'recipient_name', ['destination', 'ditujukan_kepada']);
            $set($form, 'recipient_address', ['destination_address', 'lokasi_tujuan_surat']);
            $set($form, 'ref_letter_number', ['request_number', 'nomor_surat_permohonan', 'nomor_surat_asal']);
            $set($form, 'event_date', ['follow_up_date', 'tanggal_tindak_lanjut', 'tanggal_kegiatan']);
            $set($form, 'event_time', ['follow_up_time', 'waktu_kegiatan']);
            $set($form, 'event_place', ['follow_up_place', 'tempat_kegiatan']);
            $set($form, 'event_objective', ['permit_type', 'request_subject', 'nama_acara']);
            $set($form, 'tembusan', ['carbon_copy']);
        }

        if ($code === 'SPSKG') {
            $set($form, 'recipient_name', ['tenant_name', 'tenant_representative', 'ditujukan_kepada']);
            $set($form, 'recipient_address', ['tenant_address', 'lokasi_tujuan_surat']);
            $set($form, 'representative_name', ['representativeName', 'nama_penawar']);
            $set($form, 'representative_position', ['representativePosition', 'jabatan_penawar']);
            $set($form, 'representative_address', ['representativeAddress', 'alamat_penawar']);
            $set($form, 'lease_building_name', ['property_name', 'nama_gedung']);
            $set($form, 'lease_building_address', ['property_address', 'alamat_gedung']);
            $set($form, 'lease_end_date', ['leaseEndDate', 'tanggal_berakhir_sewa_lama']);
            $set($form, 'lease_annual_rent', ['leaseAnnualRent', 'harga_sewa_per_tahun']);
            $set($form, 'lease_annual_rent_words', ['leaseAnnualRentWords', 'annual_rent_words']);
            $set($form, 'lease_duration_years', ['rent_period', 'lama_sewa_tahun']);
        }

        if ($code === 'SBP') {
            $set($form, 'recipient_name', ['destination', 'request_institution', 'ditujukan_kepada']);
            $set($form, 'ref_letter_number', ['request_number', 'nomor_surat_permohonan']);
            $set($form, 'researcher_name', ['name', 'nama_mahasiswa']);
            $set($form, 'researcher_nim', ['researcher_number', 'nim']);
            $set($form, 'study_program', ['researcher_study_program', 'program_studi']);
            $set($form, 'faculty', ['request_institution', 'fakultas']);
        }

        if ($code === 'SPKTP') {
            $set($form, 'application_type', ['jenis_permohonan_ktp']);
            $set($form, 'kk_number', ['nomor_kk']);
        }

        if ($code === 'SPKIA') {
            $set($form, 'nik', ['nik_anak']);
            $set($form, 'name', ['nama_anak']);
            $set($form, 'birth_place', ['tempat_lahir_anak']);
            $set($form, 'birth_date', ['tanggal_lahir_anak']);
            $set($form, 'gender', ['jenis_kelamin_anak']);
            $set($form, 'blood_type', ['golongan_darah_anak']);
            $set($form, 'kk_number', ['nomor_kk']);
            $set($form, 'household_head', ['nama_kepala_keluarga']);
            $set($form, 'birth_cert_number', ['nomor_akta_kelahiran']);
            $set($form, 'religion', ['agama_anak']);
            $set($form, 'citizenship', ['kewarganegaraan']);
            $set($form, 'address', ['alamat_anak']);
            $set($form, 'rt', ['rt']);
            $set($form, 'rw', ['rw']);
            $set($form, 'village', ['kelurahan']);
            $set($form, 'district', ['kecamatan']);
            $set($form, 'application_date', ['tgl_pengambilan']);
        }

        if ($code === 'SPKIAF') {
            // F-1.02 memakai data pemohon dan pilihan checkbox, bukan data
            // biodata KIA. Jangan memberi default pilihan di sini: pilihan
            // harus mengikuti checkbox yang dikirim oleh frontend.
            $set($form, 'applicant_name', ['nama_pemohon', 'name']);
            $set($form, 'applicant_nik', ['nik_pemohon', 'nik']);
            $set($form, 'kk_number', ['nomor_kk']);
            $set($form, 'application_date', ['tanggal_pengajuan', 'submission_date']);
        }

        if ($code === 'SRBBM') {
            $set($form, 'consumer', ['konsumen_jenis_bbm']);
            $set($form, 'business_type', ['jenis_usaha_kegiatan']);
            $set($form, 'business_address', ['alamat_usaha']);
            $set($form, 'total', ['jumlah']);
            $set($form, 'volume_allocation', ['alokasi_volume']);
            $set($form, 'pickup_location', ['tempat_pengambilan']);
            $set($form, 'distributor_number', ['nomor_lembaga_penyalur']);
            $set($form, 'distributor_location', ['lokasi']);
            $form['fuel_items'] ??= [[
                'retailer' => $form['pengecer_solar'] ?? null,
                'type' => $form['bbm_jenis_tertentu'] ?? null,
                'description' => $form['konsumsi_bbm'] ?? null,
            ]];
        }

        if ($code === 'SPPD') {
            foreach ([
                'issuing_official' => ['pejabat_berwenang'],
                'employee_name' => ['nama_pegawai'],
                'employee_rank' => ['pangkat_golongan'],
                'employee_position' => ['jabatan'],
                'travel_purpose' => ['maksud_perjalanan'],
                'transport' => ['alat_angkutan'],
                'departure_place' => ['tempat_berangkat'],
                'destination_place' => ['tempat_tujuan'],
                'departure_date' => ['tanggal_berangkat'],
                'return_due_date' => ['tanggal_kembali'],
                'companions' => ['pengikut'],
                'budget_agency' => ['anggaran_instansi'],
                'budget_item' => ['anggaran_mata_anggaran'],
                'travel_notes' => ['keterangan_lain'],
            ] as $canonical => $sources) {
                $set($form, $canonical, $sources);
            }
        }

        if (in_array($code, ['SPPWNI', 'SPPWF', 'SKDT'], true)) {
            // Peta alasan pindah: label Indonesia (dari Vue) → kode numerik 1-7
            $relocationReasonMap = [
                'pekerjaan'  => '1', 'Pekerjaan'  => '1',
                'pendidikan' => '2', 'Pendidikan' => '2',
                'keamanan'   => '3', 'Keamanan'   => '3',
                'kesehatan'  => '4', 'Kesehatan'  => '4',
                'perumahan'  => '5', 'Perumahan'  => '5',
                'keluarga'   => '6', 'Keluarga'   => '6',
                'lainnya'    => '7', 'Lainnya'    => '7',
            ];
            // Peta jenis kepindahan: label Indonesia → kode 1-4
            $relocationTypeMap = [
                'kep. keluarga'                              => '1',
                'kepala keluarga'                            => '1',
                'kep. keluarga dan seluruh angg. keluarga'   => '2',
                'kep. keluarga dan seluruh anggota keluarga' => '2',
                'kep. keluarga dan sbg angg. keluarga'       => '3',
                'kep. keluarga dan sebagian angg. keluarga'  => '3',
                'anggota keluarga'                           => '4',
            ];
            // Peta status KK: label Indonesia → kode 1-3
            $kkStatusMap = [
                'numpang kk'     => '1', 'numpang' => '1',
                'buat kk baru'   => '2', 'baru'    => '2',
                'tetap dalam kk' => '3', 'tetap'   => '3',
            ];

            // Normalizer: cari kode dari label; jika sudah angka, biarkan
            $toCode = static function (?string $value, array $map): ?string {
                if ($value === null || $value === '') {
                    return null;
                }
                // Sudah kode numerik?
                if (preg_match('/^\d+$/', trim($value))) {
                    return trim($value);
                }
                // Ambil bagian sebelum titik jika format "1. Label"
                if (preg_match('/^(\d+)\./', trim($value), $m)) {
                    return $m[1];
                }
                $lower = mb_strtolower(trim($value));
                foreach ($map as $label => $code) {
                    if (mb_strtolower($label) === $lower) {
                        return $code;
                    }
                }
                // Substring match sebagai fallback
                foreach ($map as $label => $code) {
                    if (str_contains($lower, mb_strtolower($label)) || str_contains(mb_strtolower($label), $lower)) {
                        return $code;
                    }
                }
                return null;
            };

            // Fallback camelCase → snake (sudah dikonversi normalizeKeys, tapi tambahkan fallback eksplisit)
            $form['origin'] ??= [
                'kk_number'     => $form['origin_kk_number'] ?? $form['originKkNumber'] ?? $form['kk_number'] ?? $form['nomor_kk'] ?? null,
                'head_of_family'=> $form['origin_head_of_family'] ?? $form['head_of_family'] ?? $form['nama_kepala_keluarga'] ?? null,
                'address'       => $form['origin_address'] ?? $form['alamat_asal'] ?? null,
                'rt'            => $form['origin_rt'] ?? $form['rt_asal'] ?? null,
                'rw'            => $form['origin_rw'] ?? $form['rw_asal'] ?? null,
                'hamlet'        => $form['origin_hamlet'] ?? $form['dusun_asal'] ?? null,
                'village'       => $form['origin_village'] ?? $form['desa_asal'] ?? null,
                'district'      => $form['origin_district'] ?? $form['kecamatan_asal'] ?? null,
                'regency'       => $form['origin_regency'] ?? $form['kabupaten_asal'] ?? null,
                'province'      => $form['origin_province'] ?? $form['provinsi_asal'] ?? null,
                'postal_code'   => $form['origin_postal_code'] ?? $form['kode_pos_asal'] ?? null,
                'phone'         => $form['origin_phone'] ?? $form['telepon_asal'] ?? null,
            ];
            if (blank($form['origin']['kk_number'] ?? null)) {
                $form['origin']['kk_number'] = $form['origin_kk_number'] ?? $form['originKkNumber'] ?? null;
            }
            $form['destination'] ??= [
                'kk_number'     => $form['destination_kk_number'] ?? null,
                'head_of_family'=> $form['destination_head_of_family'] ?? null,
                'address'       => $form['destination_address'] ?? $form['alamat_tujuan'] ?? null,
                'rt'            => $form['destination_rt'] ?? $form['rt_tujuan'] ?? null,
                'rw'            => $form['destination_rw'] ?? $form['rw_tujuan'] ?? null,
                'hamlet'        => $form['destination_hamlet'] ?? $form['dusun_tujuan'] ?? null,
                'village'       => $form['destination_village'] ?? $form['desa_tujuan'] ?? null,
                'district'      => $form['destination_district'] ?? $form['kecamatan_tujuan'] ?? null,
                'regency'       => $form['destination_regency'] ?? $form['kabupaten_tujuan'] ?? null,
                'province'      => $form['destination_province'] ?? $form['provinsi_tujuan'] ?? null,
                'postal_code'   => $form['destination_postal_code'] ?? $form['kode_pos_tujuan'] ?? null,
                'phone'         => $form['destination_phone'] ?? $form['telepon_tujuan'] ?? null,
            ];

            // F.1-31 menampilkan dusun/dukuh/kampung pada header formulir,
            // bukan di blok alamat tujuan. Field frontend baru memakai key
            // `hamlet`; fallback ini tetap mendukung payload lama.
            $form['hamlet'] ??= $form['destination']['hamlet']
                ?? $form['destination_hamlet']
                ?? $form['dusun_tujuan']
                ?? null;

            // Ambil alasan pindah dari camelCase Vue atau fallback Indonesia
            $set($form, 'relocation_reason', ['alasan_pindah', 'relocationReason']);
            $set($form, 'relocation_reason_other', ['alasan_pindah_lainnya', 'relocationReasonOther', 'relocation_reason_other']);
            $set($form, 'relocation_type', ['jenis_kepindahan', 'relocationType']);
            $set($form, 'origin_kk_status', ['status_kk_tidak_pindah', 'originKkStatus']);
            $set($form, 'destination_kk_status', ['status_kk_pindah', 'destinationKkStatus']);
            $set($form, 'arrival_date', ['tanggal_kedatangan']);

            // Konversi label Indonesia ke kode numerik (dibutuhkan template checkmark grid)
            $form['relocation_reason_code'] = $toCode(
                $form['relocation_reason_code'] ?? $form['relocation_reason'] ?? null,
                $relocationReasonMap,
            );
            $form['relocation_type_code'] = $toCode(
                $form['relocation_type_code'] ?? $form['relocation_type'] ?? null,
                $relocationTypeMap,
            );
            $form['origin_kk_status_code'] = $toCode(
                $form['origin_kk_status_code'] ?? $form['origin_kk_status'] ?? null,
                $kkStatusMap,
            );
            $form['destination_kk_status_code'] = $toCode(
                $form['destination_kk_status_code'] ?? $form['destination_kk_status'] ?? null,
                $kkStatusMap,
            );

            // Label teks jenis kepindahan (dipakai blade sebagai teks deskriptif)
            if (blank($form['relocation_type_label'] ?? null) && filled($form['relocation_type'] ?? null)) {
                $form['relocation_type_label'] = $form['relocation_type'];
            }

            if (isset($form['family_members']) && is_array($form['family_members'])) {
                $form['family_members'] = array_map(static fn (array $member): array => [
                    'nik'         => $member['nik'] ?? null,
                    'name'        => $member['name'] ?? null,
                    'valid_until' => $member['valid_until'] ?? $member['validUntil'] ?? null,
                    'shdk'        => $member['shdk'] ?? null,
                ], $form['family_members']);
            }
        }

        if ($code === 'SPTTS' && ! isset($form['family_members'])) {
            $form['family_members'] = array_values(array_filter(array_map(
                static fn (int $index): array => [
                    'name' => $form["family_member{$index}_name"] ?? null,
                    'nik' => $form["family_member{$index}_nik"] ?? null,
                ],
                range(1, 5),
            ), static fn (array $member): bool => filled($member['name'] ?? null) || filled($member['nik'] ?? null)));
        }

        if ($code === 'SPKIAF') {
            // Frontend publik/admin lama mengirim pilihan dalam empat field
            // kelompok. Pertahankan dukungan itu, tetapi jangan pernah
            // mengisi pilihan contoh/default karena akan membuat checkbox PDF
            // terlihat tercentang padahal tidak dipilih user.
            if (empty($form['application_types'])) {
                $form['application_types'] = array_values(array_filter(array_merge(
                    (array) ($form['kk_types'] ?? $form['kkTypes'] ?? []),
                    (array) ($form['ktp_el_types'] ?? $form['ktpElTypes'] ?? []),
                    (array) ($form['kia_types'] ?? $form['kiaTypes'] ?? []),
                    (array) ($form['data_change_types'] ?? $form['dataChangeTypes'] ?? []),
                )));
            }

            if (empty($form['attached_documents'])) {
                $form['attached_documents'] = array_values(array_filter(
                    (array) ($form['requirements'] ?? []),
                ));
            }
        }

        if (in_array($code, ['SGC'], true)) {
            $set($form, 'applicant_name', ['nama_penggugat']);
            $set($form, 'birth_place', ['tempat_lahir_penggugat']);
            $set($form, 'birth_date', ['tanggal_lahir_penggugat']);
            $set($form, 'gender', ['jenis_kelamin_penggugat']);
            $set($form, 'religion', ['agama_penggugat']);
            $set($form, 'occupation', ['pekerjaan_penggugat']);
            $set($form, 'address', ['alamat_penggugat']);
            $set($form, 'applicant_birth_place', ['tempat_lahir_penggugat']);
            $set($form, 'applicant_birth_date', ['tanggal_lahir_penggugat']);
            $set($form, 'applicant_gender', ['jenis_kelamin_penggugat']);
            $set($form, 'applicant_religion', ['agama_penggugat']);
            $set($form, 'applicant_occupation', ['pekerjaan_penggugat']);
            $set($form, 'applicant_address', ['alamat_penggugat']);
            $set($form, 'spouse_name', ['nama_tergugat']);
            $set($form, 'spouse_birth_place', ['tempat_lahir_tergugat']);
            $set($form, 'spouse_birth_date', ['tanggal_lahir_tergugat']);
            $set($form, 'spouse_occupation', ['pekerjaan_tergugat']);
            $set($form, 'spouse_address', ['alamat_tergugat']);
            $set($form, 'marriage_cert_number', ['nomor_surat_nikah']);

            // divorce_reasons: bisa berupa string tunggal, array string, atau array of objects {text: '...'}
            $rawReasons = $form['reasons'] ?? $form['divorce_reasons'] ?? $form['alasan_gugat_cerai'] ?? null;
            if (is_string($rawReasons) && $rawReasons !== '') {
                $form['divorce_reasons'] = [$rawReasons];
            } elseif (is_array($rawReasons)) {
                $form['divorce_reasons'] = array_values(array_filter(
                    array_map(static fn ($item) => is_array($item) ? ($item['text'] ?? $item['reason'] ?? null) : (string) $item, $rawReasons),
                    static fn ($s) => $s !== null && $s !== '',
                ));
            } else {
                $form['divorce_reasons'] = array_values(array_filter([$form['alasan_gugat_cerai'] ?? null]));
            }

            $form['witnesses'] ??= [
                [
                    'name'       => $form['witness1_name'] ?? $form['nama_saksi_1'] ?? null,
                    'birth_place'=> $form['witness1_birth_place'] ?? $form['tempat_lahir_saksi_1'] ?? null,
                    'birth_date' => $form['witness1_birth_date'] ?? $form['tanggal_lahir_saksi_1'] ?? null,
                    // fallback untuk form Vue yang mengirim birth sebagai string gabungan
                    'birth'      => $form['witness1_birth'] ?? null,
                    'religion'   => $form['witness1_religion'] ?? $form['agama_saksi_1'] ?? null,
                    'occupation' => $form['witness1_occupation'] ?? $form['pekerjaan_saksi_1'] ?? null,
                    'address'    => $form['witness1_address'] ?? $form['alamat_saksi_1'] ?? null,
                ],
                [
                    'name'       => $form['witness2_name'] ?? $form['nama_saksi_2'] ?? null,
                    'birth_place'=> $form['witness2_birth_place'] ?? $form['tempat_lahir_saksi_2'] ?? null,
                    'birth_date' => $form['witness2_birth_date'] ?? $form['tanggal_lahir_saksi_2'] ?? null,
                    // fallback untuk form Vue yang mengirim birth sebagai string gabungan
                    'birth'      => $form['witness2_birth'] ?? null,
                    'religion'   => $form['witness2_religion'] ?? $form['agama_saksi_2'] ?? null,
                    'occupation' => $form['witness2_occupation'] ?? $form['pekerjaan_saksi_2'] ?? null,
                    'address'    => $form['witness2_address'] ?? $form['alamat_saksi_2'] ?? null,
                ],
            ];

            // Pastikan applicant birth juga terisi (Vue mengirim applicantBirth sebagai string gabungan)
            if (blank($form['birth_place'] ?? null) && blank($form['birth_date'] ?? null) && filled($form['applicant_birth'] ?? null)) {
                $form['birth'] = $form['applicant_birth'];
            }
            // Pastikan spouse_birth string gabungan tersedia bila tidak ada komponen terpisah
            if (blank($form['spouse_birth_place'] ?? null) && blank($form['spouse_birth_date'] ?? null) && filled($form['spouse_birth'] ?? null)) {
                // spouse_birth sudah ada (camelCase spouseBirth → spouse_birth via normalizeKeys)
            }
        }


        if ($code === 'SPBNI') {
            $set($form, 'other_name', ['perbedaan_nama']);
            $set($form, 'other_address', ['perbedaan_alamat']);
            $set($form, 'other_nik', ['perbedaan_nik']);
        }

        if ($code === 'SPDP') {
            // Form admin menyimpan tabel sebagai empat array terpisah;
            // template F-1.06 memakai nama key canonical dari PDF.
            if (isset($form['family_members']) && is_array($form['family_members'])) {
                $form['family_members'] = array_map(static fn (mixed $row): array => [
                    'name' => is_array($row) ? ($row['name'] ?? null) : null,
                    'nik' => is_array($row) ? ($row['nik'] ?? null) : null,
                    'shdk' => is_array($row) ? ($row['shdk'] ?? null) : null,
                    'note' => is_array($row) ? ($row['note'] ?? null) : null,
                ], $form['family_members']);
            }

            $form['education_job_changes'] = array_values(array_filter([
                ...array_map(static fn (mixed $row): array => [
                    'name' => is_array($row) ? ($row['name'] ?? null) : null,
                    'education_before' => is_array($row) ? ($row['before'] ?? null) : null,
                    'education_after' => is_array($row) ? ($row['after'] ?? null) : null,
                    'education_basis' => is_array($row) ? ($row['basis'] ?? null) : null,
                    'job_before' => null,
                    'job_after' => null,
                    'job_basis' => null,
                    'note' => null,
                ], (array) ($form['education_changes'] ?? [])),
                ...array_map(static fn (mixed $row): array => [
                    'name' => is_array($row) ? ($row['name'] ?? null) : null,
                    'education_before' => null,
                    'education_after' => null,
                    'education_basis' => null,
                    'job_before' => is_array($row) ? ($row['before'] ?? null) : null,
                    'job_after' => is_array($row) ? ($row['after'] ?? null) : null,
                    'job_basis' => is_array($row) ? ($row['basis'] ?? null) : null,
                    'note' => null,
                ], (array) ($form['job_changes'] ?? [])),
            ], static fn (array $row): bool => filled($row['name'] ?? null) || filled($row['education_after'] ?? null) || filled($row['job_after'] ?? null)));

            $form['religion_other_changes'] = array_values(array_filter([
                ...array_map(static fn (mixed $row): array => [
                    'religion_before' => is_array($row) ? ($row['before'] ?? null) : null,
                    'religion_after' => is_array($row) ? ($row['after'] ?? null) : null,
                    'religion_basis' => is_array($row) ? ($row['basis'] ?? null) : null,
                    'other_before' => null,
                    'other_after' => null,
                    'other_basis' => null,
                    'note' => null,
                ], (array) ($form['religion_changes'] ?? [])),
                ...array_map(static fn (mixed $row): array => [
                    'religion_before' => null,
                    'religion_after' => null,
                    'religion_basis' => null,
                    'other_before' => is_array($row) ? ($row['before'] ?? null) : null,
                    'other_after' => is_array($row) ? ($row['after'] ?? null) : null,
                    'other_basis' => is_array($row) ? ($row['basis'] ?? null) : null,
                    'note' => is_array($row) ? ($row['note'] ?? null) : null,
                ], (array) ($form['other_changes'] ?? [])),
            ], static fn (array $row): bool => filled($row['religion_after'] ?? null) || filled($row['other_after'] ?? null)));
        }

        if ($code === 'RL' && isset($form['rows']) && is_array($form['rows'])) {
            $form['rows'] = array_map(static fn (array $row): array => [
                'date' => $row['date'] ?? null,
                'kk_number' => $row['kk_number'] ?? $row['kkNumber'] ?? null,
                'address' => $row['address'] ?? null,
                'rt' => $row['rt'] ?? null,
                'rw' => $row['rw'] ?? null,
                'nik' => $row['nik'] ?? null,
                'name' => $row['name'] ?? null,
                'gender' => $row['gender'] ?? null,
                'birth_place' => $row['birth_place'] ?? $row['birthPlace'] ?? null,
                'birth_date' => $row['birth_date'] ?? $row['birthDate'] ?? null,
                'religion' => $row['religion'] ?? null,
                'marital_status' => $row['marital_status'] ?? $row['maritalStatus'] ?? null,
                'family_status' => $row['family_status'] ?? $row['familyStatus'] ?? null,
                'education' => $row['education'] ?? null,
                'occupation' => $row['occupation'] ?? null,
            ], $form['rows']);
        }

        if (in_array($code, ['SKDN', 'SPU', 'SPTMDK'], true)) {
            $set($form, 'birth_place', ['tempat_lahir']);
            $set($form, 'birth_date', ['tanggal_lahir']);
            $set($form, 'gender', ['jenis_kelamin']);
            $set($form, 'marital_status', ['status_perkawinan']);
            $set($form, 'religion', ['agama']);
            $set($form, 'occupation', ['pekerjaan']);
            $set($form, 'destination_agency', ['pergi_ke', 'tujuan']);
            $set($form, 'purpose', ['keperluan']);
            $set($form, 'mother_name', ['nama_ibu']);
            $set($form, 'father_name', ['nama_ayah']);
        }

        if (array_key_exists('tembusan', $form) && ! is_array($form['tembusan'])) {
            $form['tembusan'] = preg_split('/\r\n|\r|\n|,/', (string) $form['tembusan'], -1, PREG_SPLIT_NO_EMPTY);
        }

        // Data seeder lama pernah menyimpan kata acak pada field bertipe text
        // yang secara makna adalah tanggal. Jangan biarkan satu nilai invalid
        // membuat seluruh preview PDF gagal; nilai tanggal invalid dikosongkan
        // dan akan terisi kembali ketika LetterRequestSeeder dijalankan ulang.
        $dateKeys = [
            'event_date', 'akad_date', 'numpang_date', 'unmarried_certificate_date',
            'birth_date', 'student_birth_date', 'spouse_birth_date',
            'husband_birth_date', 'wife_birth_date', 'child_birth_date',
            'mother_birth_date', 'father_birth_date', 'reporter_birth_date',
            'report_date', 'marriage_date', 'bride_birth_date', 'groom_birth_date',
            'ex_husband_died_at', 'application_date', 'departure_date',
            'return_due_date', 'land_measurement_letter_date',
        ];

        foreach ($dateKeys as $dateKey) {
            if (! array_key_exists($dateKey, $form) || blank($form[$dateKey])) {
                continue;
            }

            $rawDate = (string) $form[$dateKey];
            try {
                $form[$dateKey] = Carbon::parse($rawDate)->format('Y-m-d');
            } catch (\Throwable) {
                $months = [
                    'januari' => 'january', 'februari' => 'february', 'maret' => 'march',
                    'april' => 'april', 'mei' => 'may', 'juni' => 'june',
                    'juli' => 'july', 'agustus' => 'august', 'september' => 'september',
                    'oktober' => 'october', 'november' => 'november', 'desember' => 'december',
                ];
                $translated = str_ireplace(array_keys($months), array_values($months), $rawDate);
                try {
                    $form[$dateKey] = Carbon::parse($translated)->format('Y-m-d');
                } catch (\Throwable) {
                    // Jika tetap gagal, jangan null-kan bila nilai asli berisi teks tanggal
                    $form[$dateKey] = $rawDate !== '' ? $rawDate : null;
                }
            }
        }

        if ($code === 'FBWNI') {
            $form['head_name'] ??= $form['name'] ?? null;
            $form['postal_code'] ??= $form['postalCode'] ?? null;
            $form['member_count'] ??= $form['memberCount'] ?? null;
            $form['hamlet_head_name'] ??= $form['hamletHead'] ?? $form['dukuh_name'] ?? null;
            $form['village_head_name'] ??= $form['villageHead'] ?? $form['lurah_name'] ?? null;
            $form['district_officer_name'] ??= $form['districtOfficer'] ?? null;
            $form['kk_number'] ??= $form['familyCardNumber'] ?? null;

            if (isset($form['members']) && is_array($form['members'])) {
                $form['members'] = array_map(function ($member) {
                    if (! is_array($member)) {
                        return $member;
                    }
                    $normalized = [];
                    foreach ($member as $k => $v) {
                        $normalized[Str::snake((string) $k)] = $v;
                    }
                    $normalized['birth_cert'] ??= $normalized['birth_cert_status'] ?? null;
                    $normalized['marriage_cert'] ??= $normalized['marriage_cert_status'] ?? null;
                    $normalized['divorce_cert'] ??= $normalized['divorce_cert_status'] ?? null;
                    $normalized['family_status'] ??= $normalized['shdk'] ?? null;
                    $normalized['disability_mental'] ??= $normalized['disorder'] ?? null;
                    $normalized['disability_type'] ??= $normalized['disability'] ?? null;
                    return $normalized;
                }, $form['members']);
            }
        }

        return $form;
    }

    /**
     * Ubah status umum dari form menjadi istilah yang sesuai dengan Model N1.
     * N1P menggunakan Perawan/Janda, sedangkan N1L menggunakan Jejaka/Duda.
     */
    private function normalizeN1Status(?string $status, string $code): ?string
    {
        $status = trim((string) $status);

        if ($status === '') {
            return null;
        }

        $statusMap = $code === 'N1P'
            ? [
                'belum kawin' => 'Perawan',
                'perjaka' => 'Perawan',
                'jejaka' => 'Perawan',
                'perawan' => 'Perawan',
                'duda' => 'Janda',
                'janda' => 'Janda',
                'cerai hidup' => 'Janda',
                'cerai mati' => 'Janda',
            ]
            : [
                'belum kawin' => 'Jejaka',
                'perjaka' => 'Jejaka',
                'jejaka' => 'Jejaka',
                'duda' => 'Duda',
                'janda' => 'Duda',
                'cerai hidup' => 'Duda',
                'cerai mati' => 'Duda',
            ];

        return $statusMap[strtolower($status)] ?? $status;
    }

    /**
     * Normalisasi jenis permohonan KTP dari label Indonesia atau kode numerik
     * ke internal key yang dibaca oleh ktp-application-form.blade.php.
     */
    private function normalizeApplicationType(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $map = [
            // Label Indonesia
            'baru'        => 'new',
            'new'         => 'new',
            'perpanjangan' => 'extension',
            'extension'   => 'extension',
            'penggantian'  => 'replacement',
            'replacement'  => 'replacement',
            // Kode numerik
            '1' => 'new',
            '2' => 'extension',
            '3' => 'replacement',
        ];

        return $map[strtolower(trim($value))] ?? $value;
    }

    public function previewHtml(string $view, array $data): string
    {
        $view = $this->wrapMarriageWomenView($view, $data);
        $html = $this->renderDocumentHtml($view, $data);

        $page = $this->isLandscape($view)
            ? 'width:33.02cm;min-height:21.59cm;padding:0.9cm'
            : 'width:21.59cm;min-height:33.02cm;padding:2cm';

        $screen = '<style>'
            . 'html{background:#d9d9d9}'
            . "body{box-sizing:border-box;{$page};margin:20px auto;background:#fff;box-shadow:0 0 8px rgba(0,0,0,.3)}"
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
            'travel-permit-letter-vill' => '471.21/ 10',
            'fuel-recommendation-letter' => '471/ 12',
            'land-price-certificate-letter',
            'land-origin-certificate-letter' => '593/ 05',
            'population-document-statement-letter' => null, // pakai form_code
            'population-occurrence-registration-form' => null, // pakai form_code (F-1.02)
            'heir-power-of-attorney-letter' => null, // surat kuasa antar-warga, tidak bernomor
            'research-response-letter' => null,
            'marriage-certificate-duplicate-letter' => null,
            'general-cover-letter' => null,
            'permit-followup-letter', 'surat-tindak-lanjut-izin' => '010/ 02',
            'lease-offer-letter', 'surat-penawaran-sewa' => '028/ 01/2026',
            'relocation-cover-letter' => '471.21/ 01',
            'relocation-certificate-form' => '471.21/ 02',
            'general-statement-letter' => '470/49',
            'general-statement-letter-gov' => '470/51',

            // Formulir F-1.01 tidak bernomor; pakai form_code.
            'family-biodata-form' => null,

            // Tiga template ini punya nomor sendiri di array masing-masing.
            'duty-travel-order-letter',
            'temporary-stay-application-form',
            'temporary-resident-request' => null,

            'birth-certificate-referral-letter' => '472/ 08',
            'birth-attestation-letter' => '472.11/ 04',
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
            'population-occurrence-registration-form' => 'F-1.02',
            'unregistered-marriage-responsibility-letter' => 'F.1.07',
            'birth-report-form' => 'F2 02',
            'family-biodata-form' => 'F-1.01',
            default => null,
        };
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

    private function draftLetterNumber(LetterRequest $letterRequest, $letterDate): string
    {
        $now = $letterDate ? Carbon::parse($letterDate) : now();
        $code = rtrim($letterRequest->letterType?->number_prefix ?: ($letterRequest->letterType?->code ?? ''), '/');

        if ($code === '') {
            $code = '470';
        }

        $seq = null;
        if ($letterRequest->letter_type_id) {
            try {
                $lastSeq = \App\Models\LetterNumberSequence::where('letter_type_id', $letterRequest->letter_type_id)
                    ->where('year', $now->year)
                    ->where('month', $now->month)
                    ->value('last_sequence');
                $seq = str_pad((int) ($lastSeq ?? 0) + 1, 3, '0', STR_PAD_LEFT);
            } catch (\Throwable) {
                $seq = '001';
            }
        }

        $seq = $seq ?: '001';
        $romanMonth = $this->toRomanMonth($now->month);

        return "{$code}/{$seq}/{$romanMonth}/{$now->year}";
    }

    private function signature(?string $name, ?string $position, $date, bool $directPrefix = false, ?string $signatureImagePath = null): array
    {
        $parsed = $date ? Carbon::parse($date) : null;

        return [
            'city' => self::SIGNATURE_CITY,
            'date' => $parsed?->locale('id')->translatedFormat('d F Y')?? self::BLANK_PLACEHOLDER,
            'date_long' => $parsed?->locale('id')->translatedFormat('d F Y') ?? self::BLANK_PLACEHOLDER,
            'prefix' => $this->signaturePrefix($position, $directPrefix),
            'position' => $this->signedByLurah($position) ? self::SIGNATURE_LURAH_TITLE : $position,
            'name' => $name,
            'image' => $this->signatureImageBase64($signatureImagePath),
        ];
    }

    /**
     * Ambil gambar yang diunggah untuk formulir SPKTP berdasarkan nama tipe
     * dokumennya, bukan berdasarkan ID database yang bisa berbeda antar data.
     */
    private function ktpMedia(LetterRequest $letterRequest): array
    {
        $media = [
            'photo' => null,
            'signature_specimen' => null,
        ];

        foreach ($letterRequest->attachments ?? [] as $attachment) {
            $documentName = strtolower((string) ($attachment->letterTypeDocument?->document_name ?? ''));
            $image = $this->imageBase64($attachment->file_path);

            if (!$image) {
                continue;
            }

            if (
                !$media['photo']
                && preg_match('/pas\s*foto|pass\s*photo|pasphoto|foto|photo/i', $documentName)
            ) {
                $media['photo'] = $image;
                continue;
            }

            if (
                !$media['signature_specimen']
                && preg_match('/spesimen|tanda\s*tangan|cap\s*jempol/i', $documentName)
            ) {
                $media['signature_specimen'] = $image;
            }
        }

        return $media;
    }

    /**
     * Konversi path / URL gambar tanda tangan ke data-URI base64 agar bisa
     * dirender DomPDF tanpa request HTTP tambahan.
     * Mendukung:
     *  - URL relatif: /storage/signatures/xxx.png  → public_path()
     *  - URL absolut: https://...  → dibaca via file_get_contents (jika lokal)
     *  - Path absolut langsung
     * Return null jika file tidak ditemukan / tidak bisa dibaca.
     */
    private function signatureImageBase64(?string $pathOrUrl): ?string
    {
        return $this->imageBase64($pathOrUrl);
    }

    private function imageBase64(?string $pathOrUrl): ?string
    {
        if (blank($pathOrUrl)) {
            return null;
        }

        // Kalau sudah berupa data-URI, langsung kembalikan.
        if (str_starts_with($pathOrUrl, 'data:')) {
            return $pathOrUrl;
        }

        // Bersihkan query string / fragment.
        $clean = strtok($pathOrUrl, '?#');

        // Ubah URL relatif ke path absolut di public/.
        if (str_starts_with($clean, '/')) {
            $absPath = public_path(ltrim($clean, '/'));
        } elseif (preg_match('#^https?://#', $clean)) {
            // URL absolut: ambil hanya path-nya (strip host:port) ke public/.
            // Tidak pakai url('/') karena tidak reliable di CLI / queue context.
            $parsed  = parse_url($clean);
            $absPath = public_path(ltrim($parsed['path'] ?? '', '/'));
        } else {
            $absPath = $clean;
        }

        if (! is_file($absPath) || ! is_readable($absPath)) {
            return null;
        }

        $mime = mime_content_type($absPath) ?: 'image/png';

        if (! str_starts_with($mime, 'image/')) {
            return null;
        }

        return "data:{$mime};base64," . base64_encode(file_get_contents($absPath));
    }

    /**
     * Gambar tanda tangan digital hanya boleh dirender bila surat sudah
     * berstatus diotorisasi/selesai (authorized/completed) DAN tipe tanda tangan
     * yang dipilih adalah digital. Sebelum diotorisasi (draft/preview) atau bila
     * tipe TTD manual, area tanda tangan dibiarkan kosong/placeholder.
     */
    public function shouldRenderDigitalSignature(LetterRequest $letterRequest): bool
    {
        $isAuthorized = filled($letterRequest->authorized_at)
            || in_array($letterRequest->status, ['authorized', 'completed'], true);

        return $isAuthorized && $letterRequest->signature_type === 'digital';
    }

    /** true jika Lurah menandatangani sendiri (bukan "a.n LURAH" oleh Carik/Kaur/dll). */
    public function signedByLurah(?string $position): bool
    {
        $p = mb_strtolower((string) $position);

        if (str_contains($p, 'urusan') || str_starts_with($p, 'kaur')) {
            return false;
        }

        return str_starts_with($p, 'lurah') || str_contains($p, 'kepala desa');
    }

    /**
     * Ambil nama Lurah dari data struktur organisasi yang dikelola admin.
     * Staff struktur disimpan sebagai is_signer=false, sedangkan akun pejabat
     * penandatangan dapat tersimpan sebagai baris signer terpisah.
     */
    private function organizationLurahName(): ?string
    {
        return Staff::query()
            ->where('is_signer', false)
            ->get(['name', 'position'])
            ->first(function (Staff $staff): bool {
                $position = mb_strtolower(trim((string) $staff->position));

                return str_starts_with($position, 'lurah') || str_contains($position, 'kepala desa');
            })
            ?->name;
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
        $formatted = $this->longDate($date);
    
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
        if (blank($date)) {
            return null;
        }

        try {
            return Carbon::parse($date)->locale('id')->translatedFormat('d F Y');
        } catch (\Throwable) {
            return (string) $date;
        }
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
            'bin'         => $f('bin') ?? $f('binti'),
            'nik'         => $f('nik'),
            'gender'      => $f('gender'),
            'birth'       => $this->birthLong($f('birth_place'), $f('birth_date')) ?: ($f('birth') ?? null),
            'citizenship' => $f('citizenship') ?? $f('nationality') ?? 'WNI',
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
            'last_education' => $form['groom_last_education'] ?? $form['groom_education'] ?? null,
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
            'last_education' => $form['bride_last_education'] ?? $form['bride_education'] ?? null,
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
     * Petakan $form ke struktur $kia (kia-application-form.blade.php).
     */
    private function kiaApplicationFromForm(array $form, ?LetterRequest $letterRequest = null): array
    {
        $birthPlace = $form['tempat_lahir_anak'] ?? $form['birth_place'] ?? $form['birthPlace'] ?? null;
        $birthDate = $form['tanggal_lahir_anak'] ?? $form['birth_date'] ?? $form['birthDate'] ?? null;

        return [
            'nik'               => $form['nik_anak'] ?? $form['nik'] ?? null,
            'name'              => $form['nama_anak'] ?? $form['name'] ?? null,
            'birth'             => $this->birth($birthPlace, $birthDate),
            'gender'            => $form['jenis_kelamin_anak'] ?? $form['gender'] ?? null,
            'blood_type'        => $form['golongan_darah_anak'] ?? $form['blood_type'] ?? $form['bloodType'] ?? null,
            'kk_number'         => $form['nomor_kk'] ?? $form['kk_number'] ?? $form['kkNumber'] ?? null,
            'household_head'    => $form['nama_kepala_keluarga'] ?? $form['household_head'] ?? $form['householdHead'] ?? null,
            'birth_cert_number' => $form['nomor_akta_kelahiran'] ?? $form['birth_cert_number'] ?? $form['birthCertNumber'] ?? null,
            'religion'          => $form['agama_anak'] ?? $form['religion'] ?? null,
            'citizenship'       => $form['kewarganegaraan'] ?? $form['citizenship'] ?? 'WNI',
            'address'           => $form['alamat_anak'] ?? $form['address'] ?? null,
            'rt'                => $form['rt'] ?? null,
            'rw'                => $form['rw'] ?? null,
            'village'           => $form['kelurahan'] ?? $form['village'] ?? null,
            'district'          => $form['kecamatan'] ?? $form['district'] ?? null,
            'date'              => !empty($form['submission_date'])
                ? $this->longDate($form['submission_date'])
                : (!empty($form['tgl_pengambilan'])
                    ? $this->longDate($form['tgl_pengambilan'])
                    : now()->locale('id')->translatedFormat('d F Y')),
            'applicant_name'    => $form['nama_pemohon'] ?? $form['applicant_name'] ?? $form['applicantName'] ?? $form['applicantManual'] ?? $letterRequest?->applicant_name ?? null,
            'applicant_nik'     => $form['nik_pemohon'] ?? $form['applicant_nik'] ?? $form['applicantNik'] ?? $letterRequest?->applicant_nik ?? null,
            'officer_name'      => $form['nama_petugas'] ?? $form['officer_name'] ?? $form['officerName'] ?? null,
        ];
    }

    /**
     * Petakan $form ke struktur $travelOrder (duty-travel-order-letter.blade.php).
     */
    private function travelOrderFromForm(array $form): array
    {
        return [
            'issuingOfficial'     => $form['issuing_official'] ?? null,
            'employeeName'        => $form['employee_name'] ?? $form['name'] ?? $form['applicant_name'] ?? null,
            'employeeRank'        => $form['employee_rank'] ?? null,
            'employeePosition'    => $form['employee_position'] ?? null,
            'travelLevel'         => $form['travel_level'] ?? 'Biasa',
            'purpose'             => $form['travel_purpose'] ?? $form['purpose'] ?? null,
            'transport'           => $form['transport'] ?? null,
            'departurePlace'      => $form['departure_place'] ?? null,
            'destinationPlace'    => $form['destination_place'] ?? null,
            'duration'            => $form['duration'] ?? null,
            'departureDate'       => $form['departure_date'] ?? null,
            'returnDueDate'       => $form['return_due_date'] ?? null,
            'companions'          => $form['companions'] ?? null,
            'budgetAgency'        => $form['budget_agency'] ?? null,
            'budgetItem'          => $form['budget_item'] ?? null,
            'notes'               => $form['travel_notes'] ?? $form['notes'] ?? null,
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

            'applicantName'    => $form['applicant_name'] ?? $form['name'] ?? null,
            'applicantNik'     => $form['applicant_nik'] ?? $form['nik'] ?? null,
            'familyCardNumber' => $form['family_card_number'] ?? $form['familyCardNumber'] ?? $form['kk_number'] ?? null,

            'originAddress'    => $form['origin_address'] ?? $form['address'] ?? null,
            'originVillage'    => $form['origin_village'] ?? null,
            'originDistrict'   => $form['origin_district'] ?? null,
            'originRegency'    => $form['origin_regency'] ?? null,
            'originProvince'   => $form['origin_province'] ?? null,

            'reason' => $form['reason'] ?? null,

            'destinationAddressLine1' => $form['destination_address_1'] ?? $form['destination_address_line_1'] ?? $form['destinationAddressLine1'] ?? null,
            'destinationAddressLine2' => $form['destination_address_2'] ?? $form['destination_address_line_2'] ?? $form['destinationAddressLine2'] ?? null,

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

            'applicantSignerName' => $form['applicant_name'] ?? $form['name'] ?? null,
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
            'applicantName' => $form['applicant_name'] ?? $form['name'] ?? null,
            'applicantNik'  => $form['applicant_nik'] ?? $form['nik'] ?? null,
            'birthInfo'     => $this->birth($form['birth_place'] ?? null, $form['birth_date'] ?? null)
                ?? $form['birth_place'] ?? $form['birth'] ?? null,
            'gender'     => $form['gender'] ?? null,
            'occupation' => $form['occupation'] ?? null,
            'education'  => $form['education'] ?? null,

            'originAddress'  => $form['origin_address'] ?? $form['address'] ?? null,
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
            'hostFamilyCardNumber' => $form['host_kk_number'] ?? $form['host_family_card_number'] ?? $form['hostFamilyCardNumber'] ?? null,
            'familyMemberCount'    => $form['family_member_count'] ?? null,

            'familyMembers' => $form['family_members'] ?? [],

            'letterDate' => $form['letter_date'] ?? null,

            'applicantSignerName' => $form['applicant_name'] ?? $form['name'] ?? null,
            'applicantNumber'     => $form['applicant_number'] ?? null,
            'applicantDate'       => $form['applicant_date'] ?? null,

            'hamletHeadNumber' => $form['hamlet_head_number'] ?? null,
            'hamletHeadDate'   => $form['hamlet_head_date'] ?? null,
        ];
    }

    /**
     * Petakan $form ke struktur $populationOccurrence
     * (population-occurrence-registration-form.blade.php, formulir F-1.02).
     *
     * Input dari website publik:
     * - applicant_name, applicant_nik, kk_number : opsional (fallback ke $fallback)
     * - application_types  : array key dari POPULATION_OCCURRENCE_REQUEST_KEYS
     * - attached_documents : array key dari POPULATION_OCCURRENCE_DOCUMENT_KEYS
     * - application_date   : opsional (fallback ke $letterDate, lalu titik-titik)
     */
    private function populationOccurrenceFromForm(array $form, array $fallback = [], $letterDate = null): array
    {
        $rawDocs         = (array) ($form['attached_documents'] ?? $form['requirements'] ?? []);
        $kkTypes         = (array) ($form['kk_types'] ?? $form['kkTypes'] ?? []);
        $ktpTypes        = (array) ($form['ktp_el_types'] ?? $form['ktpElTypes'] ?? []);
        $kiaTypes        = (array) ($form['kia_types'] ?? $form['kiaTypes'] ?? []);
        $dataChangeTypes = (array) ($form['data_change_types'] ?? $form['dataChangeTypes'] ?? []);
        $appTypes        = (array) ($form['application_types'] ?? []);

        $checkItem = static function (array $haystack, array $needles): bool {
            foreach ($haystack as $item) {
                $itemLower = mb_strtolower(trim((string) $item));
                foreach ($needles as $needle) {
                    $needleLower = mb_strtolower(trim((string) $needle));
                    if ($itemLower === $needleLower || str_contains($itemLower, $needleLower)) {
                        return true;
                    }
                }
            }
            return false;
        };

        $selectedMap = [
            // I. KK
            'family_card_new_family'            => in_array('family_card_new_family', $appTypes, true) || $checkItem($kkTypes, ['membentuk keluarga baru']),
            'family_card_head_change'           => in_array('family_card_head_change', $appTypes, true) || $checkItem($kkTypes, ['penggantian kepala keluarga']),
            'family_card_split'                 => in_array('family_card_split', $appTypes, true) || $checkItem($kkTypes, ['pisah kk']),
            'family_card_move_in'               => in_array('family_card_move_in', $appTypes, true) || $checkItem($kkTypes, ['pindah datang']),
            'family_card_citizen_abroad_return' => in_array('family_card_citizen_abroad_return', $appTypes, true) || $checkItem($kkTypes, ['wni ln karena pindah']),
            'family_card_vulnerable_group'      => in_array('family_card_vulnerable_group', $appTypes, true) || $checkItem($kkTypes, ['rentan adminduk']),
            'family_card_join_existing'         => in_array('family_card_join_existing', $appTypes, true) || $checkItem($kkTypes, ['menumpang dalam kk']),
            'family_card_important_occurrence'  => in_array('family_card_important_occurrence', $appTypes, true) || $checkItem($kkTypes, ['peristiwa penting']),
            'family_card_data_element_change'   => in_array('family_card_data_element_change', $appTypes, true) || $checkItem($kkTypes, ['perubahan elemen data']),
            'family_card_lost'                  => in_array('family_card_lost', $appTypes, true) || $checkItem($kkTypes, ['hilang']),
            'family_card_damaged'               => in_array('family_card_damaged', $appTypes, true) || $checkItem($kkTypes, ['rusak']),

            // II. KTP-el
            'id_card_new'                       => in_array('id_card_new', $appTypes, true) || $checkItem($ktpTypes, ['baru']),
            'id_card_move_in'                   => in_array('id_card_move_in', $appTypes, true) || $checkItem($ktpTypes, ['pindah datang']),
            'id_card_lost'                      => in_array('id_card_lost', $appTypes, true) || $checkItem($ktpTypes, ['hilang']),
            'id_card_damaged'                   => in_array('id_card_damaged', $appTypes, true) || $checkItem($ktpTypes, ['rusak']),
            'id_card_itap_extension'            => in_array('id_card_itap_extension', $appTypes, true) || $checkItem($ktpTypes, ['perpanjangan itap']),
            'id_card_citizenship_change'        => in_array('id_card_citizenship_change', $appTypes, true) || $checkItem($ktpTypes, ['status kewarganegaraan']),
            'id_card_out_of_domicile'           => in_array('id_card_out_of_domicile', $appTypes, true) || $checkItem($ktpTypes, ['luar domisili']),
            'id_card_transmigration'            => in_array('id_card_transmigration', $appTypes, true) || $checkItem($ktpTypes, ['transmigrasi']),

            // III. KIA
            'child_card_new'                    => in_array('child_card_new', $appTypes, true) || $checkItem($kiaTypes, ['baru']),
            'child_card_lost'                   => in_array('child_card_lost', $appTypes, true) || $checkItem($kiaTypes, ['hilang']),
            'child_card_damaged'                => in_array('child_card_damaged', $appTypes, true) || $checkItem($kiaTypes, ['rusak']),
            'child_card_itap_extension'         => in_array('child_card_itap_extension', $appTypes, true) || $checkItem($kiaTypes, ['perpanjangan itap']),
            'child_card_other'                  => in_array('child_card_other', $appTypes, true) || $checkItem($kiaTypes, ['lainnya']),

            // IV. Perubahan Data
            'data_change_family_card'           => in_array('data_change_family_card', $appTypes, true) || $checkItem($dataChangeTypes, ['kk']),
            'data_change_id_card'               => in_array('data_change_id_card', $appTypes, true) || $checkItem($dataChangeTypes, ['ktp']),
            'data_change_child_card'            => in_array('data_change_child_card', $appTypes, true) || $checkItem($dataChangeTypes, ['kia']),
        ];

        // Map label Indonesia dokumen → internal key
        $documentKeyMap = [
            'old_family_card'                  => ['kk lama', 'kk lama/rusak', 'kk rusak'],
            'marriage_certificate'             => ['buku nikah', 'kutipan akta perkawinan', 'buku nikah/kutipan akta perkawinan'],
            'divorce_certificate'              => ['kutipan akta perceraian'],
            'move_out_certificate'             => ['surat keterangan pindah'],
            'move_abroad_certificate'          => ['surat keterangan pindah luar negeri'],
            'damaged_id_card'                  => ['ktp-el rusak'],
            'travel_document'                  => ['dokumen perjalanan'],
            'police_loss_report'               => ['surat keterangan hilang dari kepolisian'],
            'occurrence_evidence'              => ['surat keterangan/bukti perubahan peristiwa kependudukan dan peristiwa penting', 'surat keterangan/bukti perubahan'],
            'unregistered_marriage_statement'  => ['sptjm perkawinan/perceraian belum tercatat', 'sptjm'],
            'death_certificate'                => ['akta kematian'],
            'loss_damage_cause_statement'      => ['surat pernyataan penyebab terjadinya hilang atau rusak', 'surat pernyataan penyebab'],
            'foreign_mission_move_certificate' => ['surat keterangan pindah dari perwakilan ri'],
            'family_acceptance_statement'      => ['surat pernyataan bersedia menerima sebagai anggota keluarga'],
            'child_custody_power_of_attorney'  => ['surat kuasa pengasuhan anak dari orang tua/wali', 'surat kuasa pengasuhan anak'],
            'residence_permit_card'            => ['kartu izin tinggal tetap'],
        ];

        $attachedDocs = [];
        foreach (self::POPULATION_OCCURRENCE_DOCUMENT_KEYS as $docKey) {
            $aliases = $documentKeyMap[$docKey] ?? [$docKey];
            $attachedDocs[$docKey] = in_array($docKey, $rawDocs, true) || $checkItem($rawDocs, $aliases);
        }

        return [
            'applicant' => [
                'name'               => $form['applicant_name'] ?? $form['name'] ?? $fallback['name'] ?? null,
                'nik'                => $form['applicant_nik'] ?? $form['nik'] ?? $fallback['nik'] ?? null,
                'family_card_number' => $form['family_card_number'] ?? $form['familyCardNumber'] ?? $form['kk_number'] ?? $fallback['kk_number'] ?? null,
            ],
            'selected'           => $selectedMap,
            'attached_documents' => $attachedDocs,
            'application_date'   => $this->longDate($form['application_date'] ?? $letterDate)
                ?? self::BLANK_PLACEHOLDER,
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
                'birth' => 'Sleman, 17 Agustus 1990',
                'birth_place' => 'Sleman',
                'birth_date' => '17 Agustus 1990',
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
            'representative' => [
                'name' => null, 'position' => null, 'address' => null,
            ],
            'recipient' => null,
            'recipient_address' => null,
            'ref_number' => null,
            'tembusan' => [],
            'tembusan_1' => null,
            'tembusan_2' => null,
            'tembusan_3' => null,
            'tembusan_4' => null,
            'tembusan_5' => null,
            'tembusan_6' => null,
            'tembusan_7' => null,
            'tembusan_8' => null,
            'tembusan_9' => null,
            'tembusan_10' => null,
            'lease' => [
                'building_name' => null, 'building_address' => null,
                'end_date' => null, 'annual_rent' => null, 'annual_rent_words' => null,
                'duration_years' => null,
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
            'populationOccurrence' => null,
            'kia' => null,

            // surat akta kelahiran (birth-*): struktur lengkap dengan nilai kosong,
            // supaya preview tidak error "Undefined variable $birth".
            'birth' => $this->birthLetterFromForm([]),

            'marriage' => null,

            // letter-c / land-* (surat keterangan tanah). Sebelumnya tidak ada di
            // sampleBase() sehingga preview error "Undefined variable $land".
            'application' => [
                'type' => null,
                'dukuh_name' => null,
            ],
            'hamlet_head_name' => null,
            'letter_c' => [
                'hamlet' => null,
                'owner_name' => null,
            ],
            'land' => [
                'certificate_number' => null,
                'area' => null,
                'area_in_words' => null,
                'owner_name' => null,
                'hamlet' => null,
                'village' => null,
                'district' => null,
                'regency' => null,
                'price_min' => null,
                'price_max' => null,
                'measurement_letter_number' => null,
                'measurement_letter_date' => null,
            ],

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

            // surat pernikahan perempuan
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

            // Surat pernyataan sendiri oleh warga (bukan diterbitkan/ditandatangani
            // pejabat kalurahan) -- tidak perlu override 'signer'. Blade-nya
            // standalone (tidak extends letters.layouts.base) jadi otomatis
            // tanpa kop, tidak butuh flag apa pun di sini.
            'population-document-statement-letter' => [],
            'identity-discrepancy-statement-letter' => [],
            'unregistered-marriage-responsibility-letter' => [],

            // Formulir F-1.02 diisi & ditandatangani pemohon sendiri (standalone, tanpa kop).
            // Preview = formulir kosong: tanpa tanda V / lingkaran terisi, tanggal titik-titik.
            'population-occurrence-registration-form' => [
                'date' => null,
                'populationOccurrence' => $this->populationOccurrenceFromForm([]),
            ],

            // Formulir Biodata Penduduk WNI (Per Keluarga) F-1.01 — standalone, landscape,
            // TEMPLATE KOSONG (8 baris anggota keluarga kosong bernomor).
            'family-biodata-form' => [
                'kodeForm' => 'F-1.01',
                'form' => ['members' => []],
            ],

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

            // Surat Keterangan Harga Tanah & Asal-Usul Tanah: preview TEMPLATE KOSONG
            // (semua data null; key sudah tersedia di sampleBase()).
            'land-price-certificate-letter', 'land-origin-certificate-letter' => [
                'signer' => ['position' => 'Lurah'],
                'date' => null,
                'hamlet_head_name' => null,
                'land' => [
                    'certificate_number' => null,
                    'area' => null,
                    'area_in_words' => null,
                    'owner_name' => null,
                    'hamlet' => null,
                    'village' => null,
                    'district' => null,
                    'regency' => null,
                    'price_min' => null,
                    'price_max' => null,
                    'measurement_letter_number' => null,
                    'measurement_letter_date' => null,
                ],
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

            // KIA — TEMPLATE KOSONG.
            'kia-application-form' => [
                'kia' => $this->kiaApplicationFromForm([]),
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
