<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LetterRequestSeeder extends Seeder
{
    public function run(): void
    {
        $citizens = DB::table('citizens')
            ->orderBy('citizen_id')
            ->get([
                'citizen_id',
                'full_name',
                'national_id',
                'phone_number',
                'address',
                'ktp_address',
            ]);
        $userIds = DB::table('users')->orderBy('user_id')->pluck('user_id');
        if ($userIds->isEmpty()) {
            throw new RuntimeException('Users must be seeded before letter requests.');
        }

        $fieldsByLetterType = DB::table('letter_type_fields')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('letter_type_id');

        $statuses = ['submitted', 'verified', 'authorized', 'completed', 'rejected'];

        DB::table('letter_types')
            ->orderBy('letter_type_id')
            ->get(['letter_type_id', 'code', 'letter_name', 'signature_method', 'number_prefix', 'signer_id'])
            ->values()
            ->each(function ($type, int $index) use (
                $citizens,
                $userIds,
                $fieldsByLetterType,
                $statuses,
            ): void {
                $status = $statuses[$index % count($statuses)];
                $requestCode = 'SEED-REQ-' . $type->code;
                $submittedAt = now()->subDays($index + 1);
                $verifiedAt = in_array($status, ['verified', 'authorized', 'completed'], true)
                    ? $submittedAt->copy()->addHours(2)
                    : null;
                $authorizedAt = in_array($status, ['authorized', 'completed'], true)
                    ? $submittedAt->copy()->addHours(4)
                    : null;
                $completedAt = $status === 'completed'
                    ? $submittedAt->copy()->addHours(6)
                    : null;
                $signerId = in_array($status, ['authorized', 'completed'], true)
                    ? $type->signer_id
                    : null;
                $userId = $userIds->first();
                $citizen = $citizens->isNotEmpty()
                    ? $citizens->get($index % $citizens->count())
                    : null;
                $applicantName = $citizen?->full_name ?? fake('id_ID')->name();
                $applicantNik = sprintf('340000000000%04d', $index + 1);
                $formData = $this->generateFormData(
                    $type->code,
                    $fieldsByLetterType->get($type->letter_type_id, collect()),
                    $index,
                    $applicantName,
                    $applicantNik,
                );

                DB::table('letter_requests')->updateOrInsert(
                    ['request_code' => $requestCode],
                    [
                        'citizen_id' => $citizen?->citizen_id,
                        'applicant_name' => $applicantName,
                        'applicant_nik' => $applicantNik,
                        'applicant_phone' => $citizen?->phone_number ?? ('08' . fake()->numerify('##########')),
                        'applicant_address' => $citizen?->address
                            ?? $citizen?->ktp_address
                            ?? fake('id_ID')->address(),
                        'letter_type_id' => $type->letter_type_id,
                        'status' => $status,
                        'form_data' => json_encode($formData, JSON_THROW_ON_ERROR),
                        'letter_number' => in_array($status, ['authorized', 'completed'], true)
                            ? ($type->number_prefix ?? ($type->code . '/')) . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT)
                            : null,
                        'signature_type' => $type->signature_method,
                        'verified_by' => $verifiedAt ? $userId : null,
                        'authorized_by_signer_id' => $signerId,
                        'source' => $index % 2 === 0 ? 'Online' : 'Manual (Kelurahan)',
                        'notes' => 'Permohonan ' . $type->letter_name . ' untuk keperluan administrasi warga.',
                        'authorized_by' => $authorizedAt ? $userId : null,
                        'submitted_at' => $submittedAt,
                        'verified_at' => $verifiedAt,
                        'authorized_at' => $authorizedAt,
                        'completed_at' => $completedAt,
                        'result_file_path' => $completedAt
                            ? 'results/seed-' . strtolower($type->code) . '.pdf'
                            : null,
                        'remarks' => $status === 'rejected'
                            ? 'Dokumen persyaratan belum lengkap dan perlu diperbaiki.'
                            : null,
                    ],
                );
            });
    }

    private function generateFormData(
        string $code,
        $fields,
        int $requestIndex,
        string $applicantName,
        string $applicantNik,
    ): array
    {
        $data = [];

        foreach ($fields as $fieldIndex => $field) {
            $data[$field->field_key] = $this->fakeValueForField(
                $field,
                ($requestIndex * 100) + $fieldIndex + 1,
            );
        }

        return $this->enrichTemplateData(
            strtoupper($code),
            $data,
            $requestIndex,
            $applicantName,
            $applicantNik,
        );
    }

    /**
     * Sebagian template lama memakai nama key canonical yang tidak selalu
     * tersedia sebagai field dinamis di form. Data turunan ini membuat contoh
     * seeder tetap penuh ketika ditampilkan di detail admin maupun PDF.
     */
    private function enrichTemplateData(
        string $code,
        array $data,
        int $requestIndex,
        string $applicantName,
        string $applicantNik,
    ): array {
        $nik = static fn (int $offset): string => sprintf(
            '340000%010d',
            (($requestIndex + 1) * 100) + $offset,
        );
        $name = static fn (): string => fake('id_ID')->name();
        $date = static fn (): string => fake()->dateTimeBetween('-2 years', '+1 year')->format('Y-m-d');

        if (in_array($code, ['FPK', 'LK', 'SKKL'], true)) {
            $data['hamlet_name'] ??= fake()->randomElement([
                'Krebet', 'Rogobangsan', 'Kalibulus', 'Macanan', 'Cokrogaten',
            ]);
            $data['hamlet_head_name'] ??= $name();
            $data['registrar_name'] ??= 'Petugas Registrasi Bimomartani';
            $data['birth_type'] ??= 'new';
            $data['report_kind'] ??= 'Lahir Baru';
            $data['application_date'] ??= $data['tanggal_lapor'] ?? $date();
        }

        if (in_array($code, ['FPK', 'SKKL'], true)) {
            // Field ini sudah ada pada seeder utama, tetapi alias canonical
            // diperlukan oleh birthLetterFromForm dan ditampilkan lebih jelas
            // pada data tambahan admin.
            $data['kk_number'] ??= $data['nomor_kk'] ?? $nik(1);
            $data['head_of_family_name'] ??= $data['nama_kepala_keluarga'] ?? $name();
            $data['child_nik'] ??= $data['nik_anak'] ?? $nik(2);
            $data['child_name'] ??= $data['nama_anak'] ?? $name();
        }

        if ($code === 'LK') {
            $data['child_name'] ??= $data['nama_anak'] ?? $name();
        }

        if ($code === 'SMLPI') {
            $data['recipient_name'] ??= $data['ditujukan_kepada'] ?? $name();
            $data['recipient_address'] ??= $data['lokasi_tujuan_surat'] ?? 'Bimomartani';
            $data['ref_letter_number'] ??= $data['nomor_surat_asal'] ?? '001/SMLPI/2026';
            $data['event_date'] ??= $data['tanggal_kegiatan'] ?? $date();
            $data['event_time'] ??= $data['waktu_kegiatan'] ?? '09.00 WIB';
            $data['event_place'] ??= $data['tempat_kegiatan'] ?? 'Balai Kalurahan Bimomartani';
            $data['event_objective'] ??= $data['nama_acara'] ?? 'Kegiatan administrasi warga';
            if (! is_array($data['tembusan'] ?? null)) {
                $data['tembusan'] = ['Arsip Kalurahan', 'Pemohon'];
            }
        }

        if (in_array($code, ['N1P', 'N1L'], true)) {
            $isBride = $code === 'N1P';
            $prefix = $isBride ? 'putri' : 'putra';
            $data["nama_catin_{$prefix}"] ??= $applicantName;
            $data["nik_catin_{$prefix}"] ??= $applicantNik;
            $data["tanggal_lahir_catin_{$prefix}"] ??= $date();
            $data["tempat_lahir_catin_{$prefix}"] ??= 'Sleman';
            $data["kewarganegaraan_catin_{$prefix}"] ??= 'WNI';
            $data["agama_catin_{$prefix}"] ??= 'Islam';
            $data["pekerjaan_catin_{$prefix}"] ??= 'Wiraswasta';
            $data["pendidikan_catin_{$prefix}"] ??= 'SMA';
            $data["alamat_catin_{$prefix}"] ??= 'Bimomartani, Ngemplak, Sleman';
        }

        if (in_array($code, ['SPTKP', 'SPBMLP'], true)) {
            $data['bride_name'] ??= $applicantName;
            $data['bride_nik'] ??= $applicantNik;
            $data['bride_birth_place'] ??= $data['tempat_lahir'] ?? 'Sleman';
            $data['bride_birth_date'] ??= $data['tanggal_lahir'] ?? $date();
            $data['bride_citizenship'] ??= $data['kewarganegaraan'] ?? 'WNI';
            $data['bride_religion'] ??= $data['agama'] ?? 'Islam';
            $data['bride_occupation'] ??= $data['pekerjaan'] ?? 'Wiraswasta';
            $data['bride_education'] ??= $data['pendidikan'] ?? $data['pendidikan_terakhir'] ?? 'SMA';
            $data['bride_status'] ??= $data['status_perkawinan'] ?? 'Belum Kawin';
            $data['bride_address'] ??= 'Bimomartani, Ngemplak, Sleman';
        }

        if ($code === 'SKNNP') {
            $data['nama_warga'] ??= $applicantName;
            $data['nik_warga'] ??= $applicantNik;
            $data['bride_name'] ??= $applicantName;
            $data['bride_nik'] ??= $applicantNik;
        }

        if ($code === 'SPTMDK') {
            $data['birth_place'] ??= $data['tempat_lahir'] ?? 'Sleman';
            $data['birth_date'] ??= $data['tanggal_lahir'] ?? $date();
            $data['mother_name'] ??= $data['nama_ibu'] ?? $name();
            $data['father_name'] ??= $data['nama_ayah'] ?? $name();
        }

        return $data;
    }

    private function fakeValueForField(object $field, int $identitySequence): mixed
    {
        $key = strtolower((string) $field->field_key);
        $label = strtolower((string) $field->field_label);

        if ($field->field_type === 'select') {
            return $this->randomSelectValue($field->options);
        }

        if ($field->field_type === 'date') {
            return $this->dateForKey($key);
        }

        // Beberapa template lama mengharapkan TTL sebagai satu teks,
        // sedangkan template kelahiran mengharapkan tanggal ISO yang dapat
        // diparsing Carbon. Bedakan keduanya agar data seeder tidak blank
        // atau menyebabkan error saat PDF dirender.
        if (str_contains($key, 'ttl')) {
            return 'Yogyakarta, 10-01-1990';
        }

        // Tidak semua field tanggal didefinisikan dengan field_type=date.
        // Seeder field lama masih memiliki beberapa key tanggal bertipe text.
        if ($this->isDateLikeKey($key)) {
            return $this->dateForKey($key);
        }

        // Data akademik harus tetap terlihat seperti data mahasiswa, bukan
        // kalimat acak dari fallback Faker. Gunakan satu profil akademik per
        // request supaya program studi dan fakultasnya tetap konsisten.
        $requestSequence = max(1, intdiv(max(1, $identitySequence) - 1, 100) + 1);
        $academicProfiles = [
            ['program' => 'Ilmu Administrasi Negara', 'faculty' => 'Fakultas Ilmu Sosial dan Ilmu Politik'],
            ['program' => 'Teknik Informatika', 'faculty' => 'Fakultas Teknik'],
            ['program' => 'Ilmu Hukum', 'faculty' => 'Fakultas Hukum'],
            ['program' => 'Pendidikan Guru Sekolah Dasar', 'faculty' => 'Fakultas Keguruan dan Ilmu Pendidikan'],
            ['program' => 'Manajemen', 'faculty' => 'Fakultas Ekonomi dan Bisnis'],
        ];
        $academicProfile = $academicProfiles[($requestSequence - 1) % count($academicProfiles)];

        if ($key === 'nim' || str_contains($key, 'nomor_mahasiswa')) {
            return sprintf('2023%05d', $requestSequence);
        }

        if ($key === 'program_studi' || str_contains($key, 'study_program') || str_contains($key, 'jurusan')) {
            return $academicProfile['program'];
        }

        if ($key === 'fakultas' || str_contains($key, 'faculty')) {
            return $academicProfile['faculty'];
        }

        if ($key === 'hari_kegiatan') {
            return fake('id_ID')->randomElement([
                'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu',
            ]);
        }

        if (str_contains($key, 'waktu') || str_contains($key, 'jam')) {
            return fake()->randomElement([
                '08.00 WIB', '09.00 WIB', '10.00 WIB', '13.00 WIB', '14.00 WIB',
            ]);
        }

        if ($key === 'hari_akad') {
            return fake('id_ID')->randomElement([
                'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu',
            ]);
        }

        if ($key === 'tempat' || $key === 'tempat_akad' || str_contains($key, 'tempat_pengambilan')) {
            return fake('id_ID')->randomElement([
                'Balai Kalurahan Bimomartani',
                'Gedung Serbaguna Bimomartani',
                'Aula Kapanewon Ngemplak',
                'Balai Padukuhan setempat',
            ]);
        }

        if (str_contains($key, 'tempat_kelahiran')) {
            return fake('id_ID')->randomElement(['Sleman', 'Yogyakarta', 'Klaten', 'Bantul']);
        }

        if (str_contains($key, 'tempat_kematian')) {
            return fake('id_ID')->randomElement(['Kabupaten Sleman', 'Kabupaten Bantul', 'Kota Yogyakarta']);
        }

        if ($key === 'pergi_ke') {
            return fake('id_ID')->randomElement([
                'Kapanewon Ngemplak', 'Kota Yogyakarta', 'Kabupaten Sleman', 'Kabupaten Bantul',
            ]);
        }

        if ($key === 'penanggung_jawab') {
            return fake('id_ID')->name();
        }

        if ($key === 'untuk_kegiatan_acara') {
            return fake('id_ID')->randomElement([
                'Rapat koordinasi warga', 'Kegiatan sosial masyarakat', 'Pertemuan keluarga',
            ]);
        }

        if (str_contains($key, 'kewarganegaraan') || str_contains($label, 'kewarganegaraan')) {
            return 'WNI';
        }

        if (str_contains($key, 'pendidikan') || str_contains($label, 'pendidikan')) {
            return fake('id_ID')->randomElement([
                'SMA', 'SMK', 'Diploma', 'S1',
            ]);
        }

        if (str_contains($key, 'kelas_semester')) {
            return fake('id_ID')->randomElement([
                'Kelas X', 'Kelas XI', 'Kelas XII', 'Semester 4', 'Semester 6',
            ]);
        }

        if (str_contains($key, 'status_bangunan')) {
            return fake('id_ID')->randomElement(['Milik Sendiri', 'Sewa', 'Milik Keluarga']);
        }

        if (str_contains($key, 'kegunaan_bangunan')) {
            return fake('id_ID')->randomElement(['Tempat Tinggal', 'Tempat Usaha', 'Perkantoran']);
        }

        if (str_contains($key, 'jenis_usaha')) {
            return fake('id_ID')->randomElement([
                'Warung Kelontong', 'Usaha Kuliner', 'Bengkel Motor',
                'Jasa Laundry', 'Toko Sembako',
            ]);
        }

        if (str_contains($key, 'kategori')) {
            return fake('id_ID')->randomElement([
                'Keluarga Kurang Mampu', 'Pelajar/Mahasiswa', 'Masyarakat Umum',
            ]);
        }

        if (str_contains($key, 'kelakuan')) {
            return 'Baik';
        }

        if (str_contains($key, 'anak_ke') || str_contains($key, 'kelahiran_ke')) {
            return fake('id_ID')->randomElement(['Pertama', 'Kedua', 'Ketiga']);
        }

        if (str_contains($key, 'umur_kelahiran')) {
            return 'Cukup bulan';
        }

        if (str_contains($key, 'bin_') || str_contains($key, 'binti_') || str_contains($key, 'bin_binti_')) {
            return fake('id_ID')->name();
        }

        if (str_contains($key, 'shdk')) {
            return fake('id_ID')->randomElement([
                'Kepala Keluarga', 'Istri', 'Anak', 'Orang Tua',
            ]);
        }

        if (str_contains($key, 'perbedaan_')) {
            return fake('id_ID')->randomElement([
                'Perbedaan penulisan pada dokumen lama',
                'Perbedaan pencantuman nama pada dokumen kependudukan',
                'Perbedaan penulisan alamat pada dokumen',
            ]);
        }

        if (str_contains($key, 'agama_')) {
            return fake('id_ID')->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']);
        }

        if (str_contains($key, 'hubungan_wali')) {
            return fake('id_ID')->randomElement(['Paman', 'Kakak kandung', 'Kakek', 'Saudara kandung']);
        }

        if (str_contains($key, 'alasan_pindah_lainnya')) {
            return 'Mengikuti tempat tinggal keluarga';
        }

        if (str_contains($key, 'nomor_akta')) {
            return sprintf('%03d/%s/%d', fake()->numberBetween(1, 999), 'AK', now()->year);
        }

        if ($key === 'menerangkan_bahwa' || str_contains($key, 'dipergunakan_untuk')) {
            return fake('id_ID')->randomElement([
                'Keperluan administrasi kependudukan',
                'Keperluan pendaftaran sekolah',
                'Keperluan pengajuan bantuan sosial',
                'Keperluan administrasi pekerjaan',
            ]);
        }

        if (str_contains($key, 'alasan_gugat_cerai')) {
            return fake('id_ID')->randomElement([
                'Perselisihan dan pertengkaran yang terjadi secara terus-menerus',
                'Tidak adanya keharmonisan dalam rumah tangga',
                'Perbedaan prinsip yang tidak dapat diselesaikan',
            ]);
        }

        if (str_contains($key, 'sebab_wali')) {
            return fake('id_ID')->randomElement([
                'Wali nasab tidak dapat hadir',
                'Wali nasab tidak diketahui keberadaannya',
                'Wali nasab tidak memenuhi ketentuan',
            ]);
        }

        if (str_contains($key, 'alasan_kuasa')) {
            return 'Pemohon berhalangan hadir sehingga memberikan kuasa kepada penerima kuasa.';
        }

        if (str_contains($key, 'daftar_berkas')) {
            return 'KTP, Kartu Keluarga, dan surat pengantar kalurahan';
        }

        if (str_contains($key, 'keterangan_lain') || str_contains($key, 'catatan')) {
            return 'Tidak ada keterangan tambahan.';
        }

        if (str_contains($key, 'sebab_kematian')) {
            return fake('id_ID')->randomElement([
                'Sakit', 'Usia lanjut', 'Kecelakaan', 'Sebab lain sesuai keterangan keluarga',
            ]);
        }

        if (str_contains($key, 'konsumsi_bbm')) {
            return '20 liter per minggu';
        }

        if (str_contains($key, 'bbm_jenis') || str_contains($key, 'konsumen_jenis_bbm')) {
            return fake('id_ID')->randomElement(['Pertalite', 'Solar', 'Pertamax']);
        }

        if (str_contains($key, 'pengecer_solar')) {
            return 'Tidak';
        }

        if (str_contains($key, 'nomor_lembaga_penyalur')) {
            return 'SPBU 44.555.12';
        }

        if (str_contains($key, 'alokasi_volume')) {
            return '100 liter per bulan';
        }

        if (str_contains($key, 'pangkat_golongan')) {
            return fake('id_ID')->randomElement(['Penata Muda / III-a', 'Pengatur / II-c', 'Penata / III-c']);
        }

        if ($key === 'jabatan') {
            return fake('id_ID')->randomElement(['Staf Administrasi', 'Kepala Seksi Pemerintahan', 'Sekretaris Kalurahan']);
        }

        if (str_contains($key, 'pejabat_berwenang')) {
            return 'Lurah Bimomartani';
        }

        if (str_contains($key, 'alat_angkutan')) {
            return fake('id_ID')->randomElement(['Kendaraan dinas roda empat', 'Kendaraan pribadi', 'Kendaraan umum']);
        }

        if (str_contains($key, 'pengikut')) {
            return 'Tidak ada';
        }

        if (str_contains($key, 'anggaran_')) {
            return fake('id_ID')->randomElement([
                'APB Kalurahan Bimomartani', 'Swadaya masyarakat', 'Anggaran instansi terkait',
            ]);
        }

        if (str_contains($key, 'pemerintah_provinsi')) {
            return 'Daerah Istimewa Yogyakarta';
        }

        if (str_contains($key, 'pemerintah_kabupaten')) {
            return 'Sleman';
        }

        if (str_contains($key, 'kode_pos')) {
            return '55584';
        }

        if (str_contains($key, 'kontak_')) {
            return '0812' . fake()->numerify('######');
        }

        if ($key === 'nama_acara' || str_contains($key, 'nama_kegiatan')) {
            return fake('id_ID')->randomElement([
                'Rapat Koordinasi Warga',
                'Kegiatan Sosialisasi Administrasi Kependudukan',
                'Pelatihan Kewirausahaan Masyarakat',
                'Pertemuan Karang Taruna',
                'Kegiatan Posyandu Kalurahan',
            ]);
        }

        if (
            in_array($key, [
                'tempat_kegiatan', 'tempat_akad', 'tempat_pengambilan',
                'tempat_meninggal', 'lokasi_meninggal', 'lokasi',
                'lokasi_tujuan_surat', 'tempat_tujuan', 'tempat_berangkat',
            ], true)
        ) {
            return fake('id_ID')->randomElement([
                'Balai Kalurahan Bimomartani',
                'Gedung Serbaguna Bimomartani',
                'Aula Kapanewon Ngemplak',
                'Balai Padukuhan setempat',
                'Kalurahan Bimomartani, Ngemplak, Sleman',
            ]);
        }

        if ($key === 'ditujukan_kepada' && str_contains($label, 'universitas')) {
            return fake('id_ID')->randomElement([
                'Universitas Negeri Yogyakarta',
                'Universitas Gadjah Mada',
                'Institut Teknologi Yogyakarta',
            ]);
        }

        if ($key === 'ditujukan_kepada' || str_contains($key, 'instansi_tujuan')) {
            return fake('id_ID')->randomElement([
                'Kepala Kapanewon Ngemplak',
                'Kepala Dinas Kependudukan dan Pencatatan Sipil',
                'Kepala Sekolah setempat',
                'Pimpinan Instansi terkait',
            ]);
        }

        if (str_contains($key, 'nomor_surat') || str_contains($key, 'nomor_surat_asal')) {
            return sprintf('%03d/%s/%d', fake()->numberBetween(1, 999), strtoupper(fake()->randomLetter()), now()->year);
        }

        if (str_contains($key, 'pangkalan_instansi') || str_contains($key, 'nama_organisasi')) {
            return fake('id_ID')->randomElement([
                'Pemerintah Kalurahan Bimomartani',
                'Karang Taruna Bimomartani',
                'Kelompok Masyarakat Bimomartani',
                'Lembaga Pendidikan setempat',
            ]);
        }

        if (str_contains($key, 'perihal')) {
            return fake('id_ID')->randomElement([
                'Permohonan tindak lanjut kegiatan',
                'Permohonan izin kegiatan masyarakat',
                'Penyampaian informasi administrasi',
            ]);
        }

        if ($field->field_type === 'number') {
            return $this->numberForKey($key);
        }

        if (str_contains($key, 'nik') || str_contains($key, 'nomor_kk') || str_contains($key, 'national')) {
            return sprintf('340000000000%04d', $identitySequence);
        }

        if (str_contains($key, 'nama') || str_contains($label, 'nama')) {
            return fake('id_ID')->name();
        }

        if (str_contains($key, 'alamat') || str_contains($label, 'alamat')) {
            return fake('id_ID')->address();
        }

        if (str_contains($key, 'tempat_lahir') || str_contains($key, 'kota_') || str_contains($key, 'kabupaten_')) {
            return fake('id_ID')->city();
        }

        if (str_contains($key, 'pekerjaan')) {
            return fake()->randomElement([
                'Wiraswasta',
                'Petani',
                'Guru',
                'Karyawan Swasta',
                'Pedagang',
                'Mahasiswa',
            ]);
        }

        if (str_contains($key, 'telepon') || str_contains($key, 'no_hp') || str_contains($key, 'nomor_hp')) {
            return '08' . fake()->numerify('##########');
        }

        if ($key === 'rt' || str_starts_with($key, 'rt_') || str_ends_with($key, '_rt')) {
            return str_pad((string) fake()->numberBetween(1, 20), 3, '0', STR_PAD_LEFT);
        }

        if ($key === 'rw' || str_starts_with($key, 'rw_') || str_ends_with($key, '_rw')) {
            return str_pad((string) fake()->numberBetween(1, 10), 3, '0', STR_PAD_LEFT);
        }

        if (str_contains($key, 'provinsi')) {
            return 'Daerah Istimewa Yogyakarta';
        }

        if (str_contains($key, 'kecamatan')) {
            return 'Ngemplak';
        }

        if (str_contains($key, 'kelurahan') || $key === 'desa' || str_contains($key, 'desa_')) {
            return 'Bimomartani';
        }

        if (str_contains($key, 'dusun')) {
            return fake()->randomElement(['Krebet', 'Rogobangsan', 'Kalibulus', 'Macanan', 'Cokrogaten']);
        }

        if (str_contains($key, 'keperluan') || str_contains($key, 'tujuan') || str_contains($key, 'maksud')) {
            return fake('id_ID')->randomElement([
                'Keperluan administrasi kependudukan',
                'Keperluan pendaftaran sekolah',
                'Keperluan pengajuan bantuan sosial',
                'Keperluan administrasi pekerjaan',
            ]);
        }

        if ($field->field_type === 'textarea') {
            return 'Data pendukung administrasi warga.';
        }

        return 'Data administrasi warga';
    }

    private function randomSelectValue(?string $optionsJson): ?string
    {
        $options = $optionsJson ? json_decode($optionsJson, true) : null;

        return is_array($options) && $options !== []
            ? (string) fake()->randomElement($options)
            : null;
    }

    private function dateForKey(string $key): string
    {
        $date = str_contains($key, 'lahir')
            ? fake()->dateTimeBetween('-60 years', '-18 years')
            : fake()->dateTimeBetween('-2 years', '+1 year');

        return $date->format('Y-m-d');
    }

    private function isDateLikeKey(string $key): bool
    {
        return str_contains($key, 'tanggal')
            || str_contains($key, 'tgl_')
            || str_contains($key, 'hari_tanggal')
            || str_contains($key, 'berlaku_')
            || str_contains($key, 'akad_')
            || str_contains($key, 'meninggal')
            || str_contains($key, 'pernikahan')
            || str_contains($key, 'pencatatan')
            || str_contains($key, 'tanggal_pengambilan')
            || str_contains($key, 'berakhir');
    }

    private function numberForKey(string $key): int
    {
        return match (true) {
            str_contains($key, 'penghasilan') => fake()->numberBetween(2500000, 15000000),
            str_contains($key, 'jumlah_karyawan') => fake()->numberBetween(1, 50),
            str_contains($key, 'peserta') => fake()->numberBetween(10, 500),
            str_contains($key, 'umur') => fake()->numberBetween(18, 75),
            default => fake()->numberBetween(1, 100),
        };
    }
}
