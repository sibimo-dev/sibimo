<?php

namespace App\Support;

/**
 * Katalog template surat mandiri yang belum termasuk tipe surat utama.
 *
 * File layout, partial, wrapper, dan helper tidak dimasukkan ke katalog ini.
 * Katalog ini dipakai oleh beberapa seeder agar tipe, field, dan dokumen
 * tambahan selalu memakai sumber metadata yang sama.
 */
final class AdditionalLetterTemplateCatalog
{
    /** @return array<int, array<string, mixed>> */
    public static function all(): array
    {
        $baseDocuments = [
            ['name' => 'Fotocopy Kartu Keluarga (KK)'],
            ['name' => 'Fotocopy Kartu Tanda Penduduk (KTP)'],
        ];

        return [
            self::type('SPAKL', 'Surat Pengantar Akta Kelahiran', 'Pengantar', 'digital', 'letters.birth.birth-certificate-referral-letter', 'kamituwa', self::identityFields(), $baseDocuments),
            self::type('LPKL', 'Laporan Pendaftaran Peristiwa Kelahiran', 'Permohonan', 'digital', 'letters.birth.birth-registration-report', 'kamituwa', self::birthFields(), $baseDocuments),

            self::type('DKF', 'Formulir Pelaporan Kematian', 'Permohonan', 'digital', 'letters.death.death-report-form', 'kaur tata laksana', self::deathFields(), $baseDocuments),
            self::type('DKA', 'Permohonan Akta Kematian', 'Permohonan', 'digital', 'letters.death.death-certificate-application', 'kaur tata laksana', self::deathFields(), $baseDocuments),
            self::type('DKR', 'Laporan Kematian', 'Keterangan', 'digital', 'letters.death.death-report', 'kaur tata laksana', self::deathFields(), $baseDocuments),
            self::type('DKU', 'Surat Keterangan Kematian Umum', 'Keterangan', 'manual', 'letters.death.death-general-statement', 'kaur tata laksana', self::deathFields(), $baseDocuments),
            self::type('DKS', 'SPTJM Kebenaran Data Kematian', 'Pernyataan', 'manual', 'letters.death.death-data-statement', 'kaur tata laksana', self::deathFields(), $baseDocuments),
            self::type('DKP', 'Surat Kuasa Permohonan Akta Kematian', 'Permohonan', 'manual', 'letters.death.death-power-of-attorney', 'kaur tata laksana', self::deathFields(), $baseDocuments),
            self::type('DKC', 'Pelaporan Pencatatan Kematian', 'Permohonan', 'digital', 'letters.death.death-registration-report', 'kaur tata laksana', self::deathFields(), $baseDocuments),
            self::type('DKH', 'Perhitungan Selamatan Kematian', 'Pernyataan', 'manual', 'letters.death.death-commemoration-calculation', 'kaur tata laksana', self::deathFields(), $baseDocuments),

            self::type('FBWNI', 'Formulir Biodata Penduduk WNI', 'Permohonan', 'manual', 'letters.family-biodata-form', 'kaur tata laksana', self::familyFields(), $baseDocuments),
            self::type('SKUMG', 'Surat Keterangan Umum Pemerintah', 'Keterangan', 'manual', 'letters.general-statement-letter-gov', 'lurah', self::identityPurposeFields(), $baseDocuments),
            self::type('SKWKA', 'Surat Kuasa Antar-Warga untuk Sidang Waris', 'Permohonan', 'manual', 'letters.heir-power-of-attorney-letter', 'kaur tata laksana', self::attorneyFields(), $baseDocuments),
            self::type('SPKIAF', 'Formulir Pendaftaran Peristiwa Kependudukan', 'Permohonan', 'digital', 'letters.population-occurrence-registration-form', 'kaur tata laksana', self::populationOccurrenceFields(), $baseDocuments),
            self::type('RL', 'Register Legalisasi', 'Keterangan', 'manual', 'letters.legalization-register', 'kaur tata laksana', self::registerFields(), $baseDocuments),

            self::type('SKTA', 'Surat Keterangan Asal-Usul Tanah', 'Keterangan', 'manual', 'letters.letter-c.land-origin-certificate-letter', 'lurah', self::landFields(), $baseDocuments),
            self::type('SKTH', 'Surat Keterangan Harga Tanah', 'Keterangan', 'manual', 'letters.letter-c.land-price-certificate-letter', 'lurah', self::landFields(), $baseDocuments),
            self::type('SPKLC', 'Surat Pernyataan Data Letter C', 'Pernyataan', 'manual', 'letters.letter-c.letter-c-data-statement-letter', 'kaur tata laksana', self::letterCFields(), $baseDocuments),
            self::type('SKC', 'Surat Kuasa Pelayanan Letter C', 'Permohonan', 'manual', 'letters.letter-c.power-of-attorney-letter', 'kaur tata laksana', self::attorneyFields(), $baseDocuments),

            self::type('SKBK2', 'Surat Keterangan Belum Pernah Menikah', 'Keterangan', 'manual', 'letters.marriage-women.letters.unmarried-certificate', 'lurah', self::identityFields(), $baseDocuments),
            self::type('SPCCP', 'Surat Persetujuan Calon Pengantin', 'Pernyataan', 'manual', 'letters.married-man.bride-groom-consent-letter', 'kaur tata laksana', self::marriedPersonFields(), $baseDocuments),
            self::type('SKKMP', 'Surat Keterangan Kematian untuk Keperluan Nikah', 'Keterangan', 'manual', 'letters.married-man.death-certificate-for-marriage-letter', 'kamituwa', self::marriedPersonFields(), $baseDocuments),
            self::type('SKUMN', 'Surat Keterangan Umum Pernikahan', 'Keterangan', 'manual', 'letters.married-man.general-certificate-letter', 'kamituwa', self::marriedPersonFields(), $baseDocuments),
            self::type('SPNIK', 'Surat Permohonan Pernikahan', 'Permohonan', 'manual', 'letters.married-man.marriage-application-letter', 'kamituwa', self::marriedPersonFields(), $baseDocuments),
            self::type('SPNIP', 'Surat Pengantar Pernikahan', 'Pengantar', 'manual', 'letters.married-man.marriage-introduction-letter', 'kamituwa', self::marriedPersonFields(), $baseDocuments),
            self::type('SKTNB', 'Surat Keterangan Menumpang Nikah', 'Keterangan', 'manual', 'letters.married-man.marriage-lodging-certificate-letter', 'kamituwa', self::marriedPersonFields(), $baseDocuments),
            self::type('FPDN', 'Formulir Pendaftaran Data Nikah', 'Permohonan', 'manual', 'letters.married-man.marriage-registration-data-sheet', 'kaur tata laksana', self::marriedPersonFields(), $baseDocuments),
            self::type('SKBKN', 'Surat Keterangan Belum Pernah Menikah', 'Keterangan', 'manual', 'letters.married-man.never-married-certificate-letter', 'lurah', self::identityFields(), $baseDocuments),
            self::type('SPBML', 'Surat Pernyataan Tidak Menikah Lagi', 'Pernyataan', 'manual', 'letters.married-man.not-remarried-statement-letter', 'kamituwa', self::marriedPersonFields(), $baseDocuments),
            self::type('SPOT', 'Surat Persetujuan Orang Tua', 'Pernyataan', 'manual', 'letters.married-man.parental-consent-letter', 'kaur tata laksana', self::marriedPersonFields(), $baseDocuments),

            self::type('SPPWF', 'Formulir Surat Pindah Warga Negara Indonesia', 'Permohonan', 'digital', 'letters.relocation-certificate-form', 'kaur tata laksana', self::relocationFields(), $baseDocuments),
            self::type('SKDT', 'Formulir Pendataan Penduduk Datang', 'Permohonan', 'digital', 'letters.resident-arrival-form', 'kaur tata laksana', self::relocationFields(), $baseDocuments),
            self::type('SKTMS', 'Surat Keterangan Tidak Mampu untuk Sekolah', 'Keterangan', 'digital', 'letters.sktm-school', 'kaur danarta', self::schoolFields(), $baseDocuments),
            self::type('SPDP', 'Surat Pernyataan Perubahan Data Penduduk', 'Pernyataan', 'digital', 'letters.statement-population-data-change', 'kaur tata laksana', self::populationChangeFields(), $baseDocuments),
            self::type('SKTS', 'Surat Permohonan Tinggal Sementara', 'Permohonan', 'digital', 'letters.temporary-resident-request', 'kaur tata laksana', self::temporaryStayFields(), $baseDocuments),
            self::type('SPTTS', 'Formulir Permohonan Tinggal Sementara', 'Permohonan', 'digital', 'letters.temporary-stay-application-form', 'kaur tata laksana', self::temporaryStayFields(), $baseDocuments),
            self::type('SKJG', 'Surat Keterangan Jalan dari Pemerintah', 'Keterangan', 'digital', 'letters.travel-permit-letter-gov', 'kaur tata laksana', self::identityPurposeFields(), $baseDocuments),
            self::type('SKJV', 'Surat Keterangan Jalan dari Kalurahan', 'Keterangan', 'digital', 'letters.travel-permit-letter-vill', 'lurah', self::identityPurposeFields(), $baseDocuments),
            self::type('SPTJP', 'SPTJM Perkawinan Belum Tercatat', 'Pernyataan', 'digital', 'letters.unregistered-marriage-responsibility-letter', 'kaur tata laksana', self::marriedPersonFields(), $baseDocuments),
        ];
    }

    /** @return array<string, mixed> */
    private static function type(
        string $code,
        string $name,
        string $category,
        string $signatureMethod,
        string $bladeView,
        string $signerRole,
        array $fields,
        array $documents,
    ): array {
        return compact('code', 'name', 'category', 'signatureMethod', 'bladeView', 'signerRole', 'fields', 'documents');
    }

    private static function field(string $label, string $key, string $type = 'text', ?string $section = null, bool $required = false, ?array $options = null): array
    {
        return array_filter([
            'label' => $label,
            'key' => $key,
            'type' => $type,
            'section' => $section,
            'is_required' => $required,
            'options' => $options,
        ], static fn ($value) => $value !== null);
    }

    private static function identityFields(): array
    {
        return [
            self::field('Tempat Lahir', 'birth_place'),
            self::field('Tanggal Lahir', 'birth_date', 'date'),
            self::field('Jenis Kelamin', 'gender', 'select', null, false, ['Laki-laki', 'Perempuan']),
            self::field('Agama', 'religion', 'select', null, false, ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']),
            self::field('Pekerjaan', 'occupation'),
            self::field('Alamat', 'address', 'textarea'),
        ];
    }

    private static function identityPurposeFields(): array
    {
        return array_merge(self::identityFields(), [
            self::field('Keperluan', 'purpose', 'textarea'),
            self::field('Instansi Tujuan', 'destination_agency'),
        ]);
    }

    private static function birthFields(): array
    {
        return array_merge(self::identityFields(), [
            self::field('Nomor Kartu Keluarga', 'kk_number'),
            self::field('Nama Ayah', 'nama_ayah'),
            self::field('Nama Ibu', 'nama_ibu'),
            self::field('Tanggal Lapor', 'tanggal_lapor', 'date'),
        ]);
    }

    private static function deathFields(): array
    {
        return array_merge([
            self::field('Nomor Kartu Keluarga', 'nomor_kk', 'text', 'Data Keluarga'),
            self::field('Nama Kepala Keluarga', 'nama_kepala_keluarga', 'text', 'Data Keluarga'),
            self::field('NIK Jenazah', 'nik_jenazah', 'text', 'Data Jenazah'),
            self::field('Nama Jenazah', 'nama_jenazah', 'text', 'Data Jenazah'),
            self::field('Jenis Kelamin Jenazah', 'jenis_kelamin_jenazah', 'select', 'Data Jenazah', false, ['Laki-laki', 'Perempuan']),
            self::field('Tempat Kelahiran Jenazah', 'tempat_kelahiran_jenazah', 'text', 'Data Jenazah'),
            self::field('Tanggal Lahir Jenazah', 'tanggal_lahir_jenazah', 'date', 'Data Jenazah'),
            self::field('Tempat/Tanggal Lahir Jenazah', 'ttl_jenazah', 'text', 'Data Jenazah'),
            self::field('Umur Jenazah', 'umur_jenazah', 'number', 'Data Jenazah'),
            self::field('Agama Jenazah', 'agama_jenazah', 'select', 'Data Jenazah', false, ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']),
            self::field('Pekerjaan Jenazah', 'pekerjaan_jenazah', 'text', 'Data Jenazah'),
            self::field('Alamat Jenazah', 'alamat_jenazah', 'textarea', 'Data Jenazah'),
            self::field('Anak Ke', 'anak_ke_jenazah', 'text', 'Data Jenazah'),
            self::field('Hari/Tanggal Meninggal', 'meninggal_hari_tanggal', 'text', 'Data Jenazah'),
            self::field('Lokasi Meninggal', 'lokasi_meninggal', 'text', 'Data Jenazah'),
            self::field('Kabupaten/Kota Meninggal', 'kota_tempat_meninggal', 'text', 'Data Jenazah'),
            self::field('Jam Meninggal', 'jam_meninggal', 'text', 'Data Jenazah'),
            self::field('Sebab Kematian', 'sebab_kematian', 'text', 'Data Jenazah'),
            self::field('Yang Menerangkan', 'yang_menerangkan', 'text', 'Data Jenazah'),
            self::field('NIK Pelapor', 'nik_pelapor', 'text', 'Data Pelapor'),
            self::field('Nama Pelapor', 'nama_pelapor', 'text', 'Data Pelapor'),
            self::field('Alamat Pelapor', 'alamat_pelapor', 'textarea', 'Data Pelapor'),
            self::field('Jenis Kelamin Pelapor', 'jenis_kelamin_pelapor', 'select', 'Data Pelapor', false, ['Laki-laki', 'Perempuan']),
            self::field('Hubungan dengan Jenazah', 'hubungan_pelapor', 'text', 'Data Pelapor'),
            self::field('Tanggal Lapor', 'tanggal_lapor', 'date', 'Data Pelapor'),
        ], self::deathPersonFields('ibu', 'Ibu Kandung'), self::deathPersonFields('ayah', 'Ayah Kandung'), [
            self::field('Tempat/Tanggal Lahir Pelapor', 'ttl_pelapor', 'text', 'Data Pelapor'),
            self::field('Umur Pelapor', 'umur_pelapor', 'number', 'Data Pelapor'),
            self::field('Pekerjaan Pelapor', 'pekerjaan_pelapor', 'text', 'Data Pelapor'),
        ], self::deathPersonFields('saksi_1', 'Saksi I'), self::deathPersonFields('saksi_2', 'Saksi II'), [
            self::field('Nama Pemberi Kuasa', 'nama_pemberi_kuasa', 'text', 'Surat Kuasa'),
            self::field('Pekerjaan Pemberi Kuasa', 'pekerjaan_pemberi_kuasa', 'text', 'Surat Kuasa'),
            self::field('Alamat Pemberi Kuasa', 'alamat_pemberi_kuasa', 'textarea', 'Surat Kuasa'),
            self::field('Nama Penerima Kuasa', 'nama_penerima_kuasa', 'text', 'Surat Kuasa'),
            self::field('Pekerjaan Penerima Kuasa', 'pekerjaan_penerima_kuasa', 'text', 'Surat Kuasa'),
            self::field('Alamat Penerima Kuasa', 'alamat_penerima_kuasa', 'textarea', 'Surat Kuasa'),
        ]);
    }

    private static function deathPersonFields(string $prefix, string $section): array
    {
        return [
            self::field("NIK {$section}", "nik_{$prefix}", 'text', $section),
            self::field("Nama {$section}", "nama_{$prefix}", 'text', $section),
            self::field("Tempat/Tanggal Lahir {$section}", "ttl_{$prefix}", 'text', $section),
            self::field("Umur {$section}", "umur_{$prefix}", 'number', $section),
            self::field("Pekerjaan {$section}", "pekerjaan_{$prefix}", 'text', $section),
            self::field("Alamat {$section}", "alamat_{$prefix}", 'textarea', $section),
        ];
    }

    private static function familyFields(): array
    {
        return [
            self::field('Nama Kepala Keluarga', 'head_name', 'text', 'Data Kepala Keluarga'),
            self::field('Alamat Kepala Keluarga', 'address', 'textarea', 'Data Kepala Keluarga'),
            self::field('RT', 'rt', 'text', 'Data Kepala Keluarga'),
            self::field('RW', 'rw', 'text', 'Data Kepala Keluarga'),
            self::field('Desa/Kalurahan', 'village', 'text', 'Data Kepala Keluarga'),
            self::field('Kapanewon', 'district', 'text', 'Data Kepala Keluarga'),
            self::field('Jumlah Anggota Keluarga', 'member_count', 'number', 'Data Keluarga'),
            self::field('Nama Anggota Keluarga', 'member_name', 'text', 'Data Keluarga'),
            self::field('NIK Anggota Keluarga', 'member_nik', 'text', 'Data Keluarga'),
            self::field('Nomor KK', 'kk_number', 'text', 'Data Keluarga'),
            self::field('Tanggal Pengisian', 'submission_date', 'date'),
        ];
    }

    private static function attorneyFields(): array
    {
        return array_merge(self::identityFields(), [
            self::field('Nama Penerima Kuasa', 'attorney_name'),
            self::field('NIK Penerima Kuasa', 'attorney_nik'),
            self::field('Alamat Penerima Kuasa', 'attorney_address', 'textarea'),
            self::field('Alasan Pemberian Kuasa', 'condition', 'textarea'),
        ]);
    }

    private static function kiaFields(): array
    {
        return [
            self::field('Nomor NIK', 'nik'),
            self::field('Nama Anak', 'name'),
            self::field('Tempat Lahir', 'birth_place'),
            self::field('Tanggal Lahir', 'birth_date', 'date'),
            self::field('Jenis Kelamin', 'gender', 'select', null, false, ['Laki-laki', 'Perempuan']),
            self::field('Golongan Darah', 'blood_type', 'select', null, false, ['A', 'B', 'AB', 'O']),
            self::field('Nomor KK', 'kk_number'),
            self::field('Nama Kepala Keluarga', 'household_head'),
            self::field('Nomor Akta Kelahiran', 'birth_cert_number'),
            self::field('Agama', 'religion', 'select', null, false, ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']),
            self::field('Kewarganegaraan', 'citizenship', 'select', null, false, ['WNI', 'WNA']),
            self::field('Alamat', 'address', 'textarea'),
            self::field('RT', 'rt'),
            self::field('RW', 'rw'),
            self::field('Kelurahan', 'village'),
            self::field('Kecamatan', 'district'),
            self::field('Tanggal Pengajuan', 'submission_date', 'date'),
        ];
    }

    private static function populationOccurrenceFields(): array
    {
        return [
            self::field('Nama Lengkap Pemohon', 'applicant_name', 'text'),
            self::field('Nomor Induk Kependudukan', 'applicant_nik', 'text'),
            self::field('Nomor Kartu Keluarga', 'kk_number', 'text'),
            self::field('Jenis Permohonan', 'application_types', 'textarea', null, false),
            self::field('Persyaratan yang Dilampirkan', 'attached_documents', 'textarea', null, false),
            self::field('Tanggal Pengajuan', 'application_date', 'date', null, false),
        ];
    }

    private static function registerFields(): array
    {
        return [
            self::field('Tahun Register', 'register_year', 'number'),
            self::field('Nama Pemohon', 'applicant_name'),
            self::field('NIK Pemohon', 'applicant_nik'),
            self::field('Tanggal Legalisasi', 'legalization_date', 'date'),
            self::field('Alamat Pemohon', 'address', 'textarea'),
            self::field('Keterangan', 'notes', 'textarea'),
            // Baris tabel diisi oleh form admin sebagai array `people`.
            // Metadata backend tetap memakai key PDF `rows` agar validasi
            // tidak membuang data tabel sebelum proses render.
            self::field('Daftar Penduduk yang Dilegalisasi', 'rows', 'textarea'),
        ];
    }

    private static function landFields(): array
    {
        return [
            self::field('Nomor Letter C', 'land_certificate_number'),
            self::field('Luas Tanah', 'land_area'),
            self::field('Luas dalam Huruf', 'land_area_in_words'),
            self::field('Nama Pemilik', 'land_owner_name'),
            self::field('Padukuhan', 'land_hamlet'),
            self::field('Kalurahan', 'land_village'),
            self::field('Kapanewon', 'land_district'),
            self::field('Kabupaten', 'land_regency'),
            self::field('Harga Tanah Terendah', 'land_price_min'),
            self::field('Harga Tanah Tertinggi', 'land_price_max'),
            self::field('Nomor Surat Ukur', 'land_measurement_letter_number'),
            self::field('Tanggal Surat Ukur', 'land_measurement_letter_date', 'date'),
        ];
    }

    private static function letterCFields(): array
    {
        return array_merge(self::identityFields(), [
            self::field('Padukuhan', 'letter_c_hamlet'),
            self::field('Nama Pemilik', 'letter_c_owner_name'),
            self::field('Nomor Letter C', 'letter_c_number'),
            self::field('Keperluan', 'purpose', 'textarea'),
            self::field('Nama Dukuh', 'dukuh_name'),
        ]);
    }

    private static function marriedPersonFields(): array
    {
        return array_merge(self::identityFields(), [
            self::field('Pendidikan Terakhir', 'education'),
            self::field('Kewarganegaraan', 'nationality'),
            self::field('Nama Alias', 'name_alias'),
            self::field('Status Perkawinan', 'marital_status'),
            self::field('Nama Calon Suami', 'groom_name'),
            self::field('Bin Calon Suami', 'groom_bin'),
            self::field('NIK Calon Suami', 'groom_nik'),
            self::field('Tempat Lahir Calon Suami', 'groom_birth_place'),
            self::field('Tanggal Lahir Calon Suami', 'groom_birth_date', 'date'),
            self::field('Kewarganegaraan Calon Suami', 'groom_citizenship'),
            self::field('Agama Calon Suami', 'groom_religion'),
            self::field('Pekerjaan Calon Suami', 'groom_occupation'),
            self::field('Pendidikan Calon Suami', 'groom_last_education'),
            self::field('Alamat Calon Suami', 'groom_address', 'textarea'),
            self::field('Status Calon Suami', 'groom_status'),
            self::field('Nama Calon Istri', 'bride_name'),
            self::field('Binti Calon Istri', 'bride_binti'),
            self::field('NIK Calon Istri', 'bride_nik'),
            self::field('Tempat Lahir Calon Istri', 'bride_birth_place'),
            self::field('Tanggal Lahir Calon Istri', 'bride_birth_date', 'date'),
            self::field('Kewarganegaraan Calon Istri', 'bride_citizenship'),
            self::field('Agama Calon Istri', 'bride_religion'),
            self::field('Pekerjaan Calon Istri', 'bride_occupation'),
            self::field('Pendidikan Calon Istri', 'bride_last_education'),
            self::field('Alamat Calon Istri', 'bride_address', 'textarea'),
            self::field('Status Calon Istri', 'bride_status'),
            self::field('Nama Suami', 'husband_name'),
            self::field('NIK Suami', 'husband_nik'),
            self::field('Tempat Lahir Suami', 'husband_birth_place'),
            self::field('Tanggal Lahir Suami', 'husband_birth_date', 'date'),
            self::field('Alamat Suami', 'husband_address', 'textarea'),
            self::field('Nama Istri', 'wife_name'),
            self::field('NIK Istri', 'wife_nik'),
            self::field('Tempat Lahir Istri', 'wife_birth_place'),
            self::field('Tanggal Lahir Istri', 'wife_birth_date', 'date'),
            self::field('Alamat Istri', 'wife_address', 'textarea'),
            self::field('Tanggal Pernikahan', 'marriage_date', 'date'),
            self::field('Hari Akad', 'ceremony_day'),
            self::field('Tanggal Akad', 'ceremony_date', 'date'),
            self::field('Waktu Akad', 'ceremony_time'),
            self::field('Tempat Akad', 'ceremony_place'),
            self::field('Nomor Registrasi', 'registration_number'),
            self::field('Tanggal Registrasi', 'registration_date', 'date'),
            self::field('Nama Petugas Registrasi', 'registration_officer_name'),
            self::field('Jabatan Petugas Registrasi', 'registration_position'),
            self::field('Nama Ayah Calon Suami', 'groom_father_name'),
            self::field('NIK Ayah Calon Suami', 'groom_father_nik'),
            self::field('Alamat Ayah Calon Suami', 'groom_father_address', 'textarea'),
            self::field('Nama Ibu Calon Suami', 'groom_mother_name'),
            self::field('NIK Ibu Calon Suami', 'groom_mother_nik'),
            self::field('Alamat Ibu Calon Suami', 'groom_mother_address', 'textarea'),
            self::field('Nama Ayah Calon Istri', 'bride_father_name'),
            self::field('NIK Ayah Calon Istri', 'bride_father_nik'),
            self::field('Alamat Ayah Calon Istri', 'bride_father_address', 'textarea'),
            self::field('Nama Ibu Calon Istri', 'bride_mother_name'),
            self::field('NIK Ibu Calon Istri', 'bride_mother_nik'),
            self::field('Alamat Ibu Calon Istri', 'bride_mother_address', 'textarea'),

            // Field tambahan yang dipakai oleh variasi template pernikahan.
            // Semuanya opsional karena tiap surat hanya memakai sebagian.
            self::field('Nama Istri Terdahulu', 'previous_spouse_name'),
            self::field('Bin/Binti Istri/Suami Terdahulu', 'previous_spouse_bin'),
            self::field('NIK Istri/Suami Terdahulu', 'previous_spouse_nik'),
            self::field('Tempat Lahir Istri/Suami Terdahulu', 'previous_spouse_birth_place'),
            self::field('Tanggal Lahir Istri/Suami Terdahulu', 'previous_spouse_birth_date', 'date'),
            self::field('Tanggal Meninggal Pasangan Terdahulu', 'previous_spouse_death_date', 'date'),
            self::field('Tempat Meninggal Pasangan Terdahulu', 'previous_spouse_death_place'),
            self::field('Nama Ayah', 'father_name'),
            self::field('NIK Ayah', 'father_nik'),
            self::field('Tempat Lahir Ayah', 'father_birth_place'),
            self::field('Tanggal Lahir Ayah', 'father_birth_date', 'date'),
            self::field('Kewarganegaraan Ayah', 'father_nationality'),
            self::field('Agama Ayah', 'father_religion'),
            self::field('Pekerjaan Ayah', 'father_occupation'),
            self::field('Alamat Ayah', 'father_address', 'textarea'),
            self::field('Nama Ibu', 'mother_name'),
            self::field('NIK Ibu', 'mother_nik'),
            self::field('Tempat Lahir Ibu', 'mother_birth_place'),
            self::field('Tanggal Lahir Ibu', 'mother_birth_date', 'date'),
            self::field('Kewarganegaraan Ibu', 'mother_nationality'),
            self::field('Agama Ibu', 'mother_religion'),
            self::field('Pekerjaan Ibu', 'mother_occupation'),
            self::field('Alamat Ibu', 'mother_address', 'textarea'),
            self::field('Nama Pasangan', 'spouse_name'),
            self::field('Bin/Binti Pasangan', 'spouse_bin'),
            self::field('Binti Pasangan', 'spouse_binti'),
            self::field('NIK Pasangan', 'spouse_nik'),
            self::field('Tempat Lahir Pasangan', 'spouse_birth_place'),
            self::field('Tanggal Lahir Pasangan', 'spouse_birth_date', 'date'),
            self::field('Kewarganegaraan Pasangan', 'spouse_nationality'),
            self::field('Agama Pasangan', 'spouse_religion'),
            self::field('Pekerjaan Pasangan', 'spouse_occupation'),
            self::field('Alamat Pasangan', 'spouse_address', 'textarea'),
            self::field('Nama Anak', 'child_name'),
            self::field('NIK Anak', 'child_nik'),
            self::field('Tempat Lahir Anak', 'child_birth_place'),
            self::field('Tanggal Lahir Anak', 'child_birth_date', 'date'),
            self::field('Kewarganegaraan Anak', 'child_nationality'),
            self::field('Agama Anak', 'child_religion'),
            self::field('Pekerjaan Anak', 'child_occupation'),
            self::field('Alamat Anak', 'child_address', 'textarea'),
            self::field('Nama Pasangan Anak', 'child_spouse_name'),
            self::field('NIK Pasangan Anak', 'child_spouse_nik'),
            self::field('Tempat Lahir Pasangan Anak', 'child_spouse_birth_place'),
            self::field('Tanggal Lahir Pasangan Anak', 'child_spouse_birth_date', 'date'),
            self::field('Kewarganegaraan Pasangan Anak', 'child_spouse_nationality'),
            self::field('Agama Pasangan Anak', 'child_spouse_religion'),
            self::field('Pekerjaan Pasangan Anak', 'child_spouse_occupation'),
            self::field('Alamat Pasangan Anak', 'child_spouse_address', 'textarea'),
            self::field('Bin/Binti Pemohon', 'applicant_bin'),
            self::field('Perilaku', 'behavior'),
            self::field('Berlaku Sampai', 'valid_until', 'date'),
            self::field('Keterangan Tambahan', 'additional_note', 'textarea'),
            self::field('Nomor Pendaftaran', 'registration_number'),
            self::field('Tanggal Pendaftaran', 'registration_date', 'date'),
            self::field('Nama Pewaris', 'deceased_name'),
            self::field('NIK Pewaris', 'deceased_nik'),
            self::field('Tempat Lahir Pewaris', 'deceased_birth_place'),
            self::field('Tanggal Lahir Pewaris', 'deceased_birth_date', 'date'),
            self::field('Kewarganegaraan Pewaris', 'deceased_nationality'),
            self::field('Agama Pewaris', 'deceased_religion'),
            self::field('Pekerjaan Pewaris', 'deceased_occupation'),
            self::field('Alamat Pewaris', 'deceased_address', 'textarea'),
            self::field('Tanggal Meninggal', 'deceased_death_date', 'date'),
            self::field('Tempat Meninggal', 'deceased_death_place'),
            self::field('Binti Pewaris', 'deceased_binti'),
            self::field('Saksi Perkawinan', 'marriage_witnesses', 'textarea'),
            self::field('Daftar Anak', 'children', 'textarea'),
            self::field('Nama Saksi I', 'witness1_name'),
            self::field('NIK Saksi I', 'witness1_nik'),
            self::field('Nama Saksi II', 'witness2_name'),
            self::field('NIK Saksi II', 'witness2_nik'),
        ]);
    }

    private static function relocationFields(): array
    {
        return [
            self::field('Nomor KK Asal', 'origin_kk_number'),
            self::field('Kepala Keluarga Asal', 'origin_head_of_family'),
            self::field('Alamat Asal', 'origin_address', 'textarea'),
            self::field('Desa/Kalurahan Asal', 'origin_village'),
            self::field('Kapanewon Asal', 'origin_district'),
            self::field('Kabupaten Asal', 'origin_regency'),
            self::field('Provinsi Asal', 'origin_province'),
            self::field('Alamat Tujuan', 'destination_address', 'textarea'),
            self::field('Dusun/Dukuh/Kampung Tujuan', 'hamlet'),
            self::field('Desa/Kalurahan Tujuan', 'destination_village'),
            self::field('Kapanewon Tujuan', 'destination_district'),
            self::field('Kabupaten Tujuan', 'destination_regency'),
            self::field('Provinsi Tujuan', 'destination_province'),
            self::field('Nomor KK Tujuan', 'destination_kk_number'),
            self::field('Status KK Tujuan', 'destination_kk_status_code'),
            self::field('Alasan Pindah', 'reason', 'textarea'),
            self::field('Tempat Lahir Pemohon', 'birth_place'),
            self::field('Tanggal Lahir Pemohon', 'birth_date', 'date'),
            self::field('Jenis Kelamin Pemohon', 'gender'),
            self::field('Agama Pemohon', 'religion'),
            self::field('Pekerjaan Pemohon', 'occupation'),
            self::field('Daftar Keluarga', 'family_members', 'textarea'),
            self::field('Nama Kepala Keluarga Asal', 'origin_head_of_family'),
            self::field('Nama Kepala Keluarga Tujuan', 'destination_head_of_family'),
            self::field('Tanggal Kedatangan', 'arrival_date', 'date'),
        ];
    }

    private static function schoolFields(): array
    {
        return array_merge(self::identityFields(), [
            self::field('Penghasilan', 'income', 'number'),
            self::field('Kategori', 'category'),
            self::field('Nama Siswa', 'student_name'),
            self::field('NIK Siswa', 'student_nik'),
            self::field('Tempat Lahir Siswa', 'student_birth_place'),
            self::field('Tanggal Lahir Siswa', 'student_birth_date', 'date'),
            self::field('Kelas', 'student_class'),
            self::field('Sekolah', 'student_education'),
            self::field('Alamat Siswa', 'student_address', 'textarea'),
            self::field('Keperluan', 'purpose', 'textarea'),
        ]);
    }

    private static function populationChangeFields(): array
    {
        return [
            self::field('Nama Pemohon', 'applicant_name'),
            self::field('NIK Pemohon', 'applicant_nik'),
            self::field('Alamat Pemohon', 'address', 'textarea'),
            self::field('Nomor KK', 'kk_number'),
            self::field('Nama Anggota Keluarga', 'member_name'),
            self::field('NIK Anggota Keluarga', 'member_nik'),
            self::field('Hubungan dalam Keluarga', 'member_shdk'),
            self::field('Catatan Perubahan', 'change_note', 'textarea'),
            self::field('Daftar Anggota Keluarga', 'family_members', 'textarea'),
            self::field('Perubahan Pendidikan', 'education_changes', 'textarea'),
            self::field('Perubahan Pekerjaan', 'job_changes', 'textarea'),
            self::field('Perubahan Agama', 'religion_changes', 'textarea'),
            self::field('Perubahan Lainnya', 'other_changes', 'textarea'),
            self::field('Elemen Perubahan Lainnya', 'other_element_label'),
            self::field('Elemen Data yang Berubah', 'change_types', 'textarea'),
        ];
    }

    private static function temporaryStayFields(): array
    {
        return [
            self::field('Nama Pemohon', 'applicant_name'),
            self::field('NIK Pemohon', 'applicant_nik'),
            self::field('Tempat Lahir', 'birth_place'),
            self::field('Tanggal Lahir', 'birth_date', 'date'),
            self::field('Jenis Kelamin', 'gender', 'select', null, false, ['Laki-laki', 'Perempuan']),
            self::field('Pekerjaan', 'occupation'),
            self::field('Alamat Asal', 'origin_address', 'textarea'),
            self::field('Alamat Tujuan', 'destination_address', 'textarea'),
            self::field('Alasan', 'reason', 'textarea'),
            self::field('Nama Penjamin', 'guarantor_name'),
            self::field('NIK Penjamin', 'guarantor_nik'),
            self::field('Nomor KK Penjamin', 'host_kk_number'),
            self::field('Pendidikan', 'education'),
            self::field('Agama', 'religion'),
            self::field('Status Perkawinan', 'marital_status'),
            self::field('Daftar Anggota Keluarga', 'family_members', 'textarea'),
            self::field('Jumlah Anggota Keluarga', 'family_member_count'),
            self::field('Nama Keluarga yang Dituju', 'host_name'),
            self::field('Nama Orang Tua/Wali', 'parent_name'),
            self::field('Alamat Orang Tua/Wali', 'parent_address', 'textarea'),
            self::field('Desa Orang Tua/Wali', 'parent_village'),
            self::field('Kecamatan Orang Tua/Wali', 'parent_district'),
            self::field('Kabupaten Orang Tua/Wali', 'parent_regency'),
            self::field('Provinsi Orang Tua/Wali', 'parent_province'),
            self::field('Alamat Penjamin', 'guarantor_address', 'textarea'),
            self::field('Desa Penjamin', 'guarantor_village'),
            self::field('Kecamatan Penjamin', 'guarantor_district'),
            self::field('Kabupaten Penjamin', 'guarantor_regency'),
            self::field('Provinsi Penjamin', 'guarantor_province'),
        ];
    }
}
