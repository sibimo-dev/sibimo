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
        $mainAddress = 'Jl. Prambanan-Cangkringan Km. 6,5, Bimomartani, Ngemplak, Sleman';
        $mainBirthDate = fake()->dateTimeBetween('-55 years', '-20 years')->format('Y-m-d');
        $person = function (int $offset, ?string $personName = null) use ($requestIndex, $mainAddress, $nik, $date): array {
            return [
                'name' => $personName ?? $this->sampleName($requestIndex + $offset),
                'nik' => $nik($offset),
                'birth_place' => 'Sleman',
                'birth_date' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
                'birth_place_date' => 'Sleman, ' . fake()->dateTimeBetween('-60 years', '-18 years')->format('d-m-Y'),
                'gender' => $offset % 2 === 0 ? 'Laki-laki' : 'Perempuan',
                'religion' => 'Islam',
                'occupation' => $offset % 2 === 0 ? 'Wiraswasta' : 'Ibu Rumah Tangga',
                'education' => 'SMA',
                'nationality' => 'WNI',
                'marital_status' => 'Menikah',
                'address' => $mainAddress,
                'age' => fake()->numberBetween(20, 65),
            ];
        };

        // Template yang memakai data bertingkat tidak cukup diisi dari field
        // datar. Bentuk data di sini mengikuti kontrak Blade masing-masing.
        if ($code === 'FBWNI') {
            $memberOne = $person(1, $applicantName);
            $memberTwo = $person(2);
            $data = array_merge($data, [
                'head_name' => $applicantName,
                'address' => $mainAddress,
                'postal_code' => '55584',
                'rt' => '004',
                'rw' => '002',
                'phone' => '081234567890',
                'province' => 'Daerah Istimewa Yogyakarta',
                'regency' => 'Sleman',
                'district' => 'Ngemplak',
                'village' => 'Bimomartani',
                'hamlet' => 'Kepuh',
                'member_count' => 2,
                'member_name' => $memberOne['name'],
                'member_nik' => $memberOne['nik'],
                'kk_number' => sprintf('340400%010d', $requestIndex + 120),
                'members' => [
                    array_merge($memberOne, ['family_status' => 'Kepala Keluarga', 'previous_address' => $mainAddress]),
                    array_merge($memberTwo, ['family_status' => 'Anak', 'previous_address' => $mainAddress]),
                ],
            ]);
        }

        if ($code === 'RL') {
            $data = array_merge($data, [
                'register_year' => now()->year,
                'applicant_name' => $applicantName,
                'applicant_nik' => $applicantNik,
                'legalization_date' => $date(),
                'address' => $mainAddress,
                'rows' => [
                    [
                        'date' => $date(),
                        'kk_number' => sprintf('340400%010d', $requestIndex + 120),
                        'address' => $mainAddress,
                        'rt' => '004', 'rw' => '002',
                        'nik' => $applicantNik,
                        'name' => $applicantName,
                        'gender' => 'Laki-laki',
                        'birth_place' => 'Sleman',
                        'birth_date' => $mainBirthDate,
                        'religion' => 'Islam',
                        'marital_status' => 'Menikah',
                        'family_status' => 'Kepala Keluarga',
                        'education' => 'SMA',
                        'occupation' => 'Wiraswasta',
                    ],
                ],
            ]);
        }

        if ($code === 'SPKIA') {
            $children = [
                ['name' => 'Zahra Aulia Putri', 'gender' => 'Perempuan', 'birth_place' => 'Sleman'],
                ['name' => 'Raka Aditya Pratama', 'gender' => 'Laki-laki', 'birth_place' => 'Yogyakarta'],
                ['name' => 'Nabila Khairunnisa', 'gender' => 'Perempuan', 'birth_place' => 'Bantul'],
                ['name' => 'Muhammad Fajar Nugraha', 'gender' => 'Laki-laki', 'birth_place' => 'Klaten'],
            ];
            $child = $children[$requestIndex % count($children)];
            $childNik = $nik(60);
            $kkNumber = sprintf('340400%010d', $requestIndex + 120);
            $birthYear = now()->year - 8 - ($requestIndex % 5);

            $data = array_merge($data, [
                'nik_anak' => $childNik,
                'nama_anak' => $child['name'],
                'tempat_lahir_anak' => $child['birth_place'],
                'tanggal_lahir_anak' => sprintf('%d-%02d-%02d', $birthYear, ($requestIndex % 9) + 1, ($requestIndex % 20) + 1),
                'jenis_kelamin_anak' => $child['gender'],
                'golongan_darah_anak' => ['A', 'B', 'O', 'AB'][$requestIndex % 4],
                'nomor_kk' => $kkNumber,
                'nama_kepala_keluarga' => $this->sampleName($requestIndex + 3),
                'nomor_akta_kelahiran' => sprintf('3471-LT-%02d%02d%04d', ($requestIndex % 12) + 1, ($requestIndex % 27) + 1, $requestIndex + 1),
                'agama_anak' => 'Islam',
                'kewarganegaraan' => 'WNI',
                'alamat_anak' => 'Kepuh, Bimomartani, Ngemplak, Sleman',
                'rt' => '004',
                'rw' => '002',
                'kelurahan' => 'Bimomartani',
                'kecamatan' => 'Ngemplak',
                'tgl_pengambilan' => $date(),
            ]);
        }

        if ($code === 'SPKIAF') {
            $data = array_merge($data, [
                'applicant_name' => $applicantName,
                'applicant_nik' => $applicantNik,
                'kk_number' => sprintf('340400%010d', $requestIndex + 120),
                'application_types' => [
                    'child_card_new',
                    'id_card_new',
                ],
                'attached_documents' => [
                    'old_family_card',
                    'occurrence_evidence',
                ],
                'application_date' => $date(),
            ]);
        }

        if ($code === 'SPBNI') {
            $data = array_merge($data, [
                'other_name' => $applicantName . ' (penulisan dokumen lama)',
                'other_address' => $mainAddress,
                'other_nik' => $applicantNik,
            ]);
        }

        if ($code === 'SPKLC') {
            $data = array_merge($data, [
                'birth_place' => 'Sleman',
                'birth_date' => $mainBirthDate,
                'gender' => 'Laki-laki',
                'religion' => 'Islam',
                'occupation' => 'Wiraswasta',
                'address' => $mainAddress,
                'letter_c_hamlet' => 'Kepuh',
                'letter_c_owner_name' => $applicantName,
                'letter_c_number' => sprintf('LC-%03d', $requestIndex + 1),
                'purpose' => 'Pengurusan administrasi waris dan pertanahan',
                'dukuh_name' => 'Rasyifa Anom Sudaryono',
            ]);
        }

        if (in_array($code, ['SPAKL', 'LPKL', 'PAK', 'FPK', 'LK', 'SKAK', 'PPKT', 'LKLD', 'SKKL'], true)) {
            $child = $person(3, $code === 'SPAKL' ? $applicantName : $this->sampleName($requestIndex + 3));
            $mother = $person(4);
            $father = $person(5);
            $data = array_merge($data, [
                'birth_type' => $code === 'PPKT' ? 'late' : ($code === 'LKLD' ? 'out_of_domicile' : 'new'),
                'kk_number' => sprintf('340400%010d', $requestIndex + 120),
                'head_of_family_name' => $applicantName,
                'child_nik' => $child['nik'],
                'child_name' => $child['name'],
                'child_gender' => $child['gender'],
                'child_birth_place' => $child['birth_place'],
                'child_birth_date' => $child['birth_date'],
                'child_birth_time' => '07.30',
                'child_delivery_place' => 'RSUD Sleman',
                'child_plurality' => 'Tunggal',
                'child_birth_order' => 'Pertama',
                'child_birth_attendant' => 'Bidan',
                'child_weight' => '3,2',
                'child_length' => '49',
                'mother_nik' => $mother['nik'],
                'mother_name' => $mother['name'],
                'mother_birth_place' => $mother['birth_place'],
                'mother_birth_date' => $mother['birth_date'],
                'mother_occupation' => $mother['occupation'],
                'mother_address' => $mother['address'],
                'mother_nationality' => 'WNI',
                'father_nik' => $father['nik'],
                'father_name' => $father['name'],
                'father_birth_place' => $father['birth_place'],
                'father_birth_date' => $father['birth_date'],
                'father_occupation' => $father['occupation'],
                'father_address' => $father['address'],
                'father_nationality' => 'WNI',
                'reporter_nik' => $applicantNik,
                'reporter_name' => $applicantName,
                'reporter_birth_place' => 'Sleman',
                'reporter_age' => 32,
                'reporter_occupation' => 'Wiraswasta',
                'reporter_address' => $mainAddress,
                'report_date' => $date(),
                'application_date' => $date(),
                'birth_witnesses' => [
                    ['nik' => $nik(6), 'name' => $this->sampleName($requestIndex + 6), 'age' => 41, 'address' => $mainAddress],
                    ['nik' => $nik(7), 'name' => $this->sampleName($requestIndex + 7), 'age' => 38, 'address' => $mainAddress],
                ],
            ]);
        }

        if (in_array($code, ['DKF', 'DKA', 'DKR', 'DKU', 'DKS', 'DKP', 'DKC', 'DKH'], true)) {
            $deceased = $person(8);
            $reporter = $person(9, $applicantName);
            $mother = $person(10);
            $father = $person(11);
            $data = array_merge($data, [
                'nomor_kk' => sprintf('340400%010d', $requestIndex + 120),
                'nama_kepala_keluarga' => $applicantName,
                'nik_jenazah' => $deceased['nik'],
                'nama_jenazah' => $deceased['name'],
                'jenis_kelamin_jenazah' => $deceased['gender'],
                'tempat_kelahiran_jenazah' => $deceased['birth_place'],
                'tanggal_lahir_jenazah' => $deceased['birth_date'],
                'ttl_jenazah' => $deceased['birth_place_date'],
                'umur_jenazah' => $deceased['age'],
                'agama_jenazah' => 'Islam',
                'pekerjaan_jenazah' => $deceased['occupation'],
                'alamat_jenazah' => $deceased['address'],
                'anak_ke_jenazah' => 'Kedua',
                'meninggal_hari_tanggal' => $date(),
                'lokasi_meninggal' => 'RSUD Sleman',
                'kota_tempat_meninggal' => 'Kabupaten Sleman',
                'jam_meninggal' => '04.15',
                'sebab_kematian' => 'Sakit',
                'yang_menerangkan' => 'Keluarga yang bersangkutan',
                'nik_ibu' => $mother['nik'], 'nama_ibu' => $mother['name'], 'ttl_ibu' => $mother['birth_place_date'], 'pekerjaan_ibu' => $mother['occupation'], 'alamat_ibu' => $mother['address'],
                'nik_ayah' => $father['nik'], 'nama_ayah' => $father['name'], 'ttl_ayah' => $father['birth_place_date'], 'pekerjaan_ayah' => $father['occupation'], 'alamat_ayah' => $father['address'],
                'nik_pelapor' => $reporter['nik'], 'nama_pelapor' => $reporter['name'], 'ttl_pelapor' => $reporter['birth_place_date'], 'umur_pelapor' => $reporter['age'], 'pekerjaan_pelapor' => $reporter['occupation'], 'alamat_pelapor' => $reporter['address'], 'jenis_kelamin_pelapor' => $reporter['gender'], 'hubungan_pelapor' => 'Anak', 'tanggal_lapor' => $date(),
                'nik_saksi_1' => $nik(12), 'nama_saksi_1' => $this->sampleName($requestIndex + 12), 'ttl_saksi_1' => 'Sleman, 12-02-1985', 'umur_saksi_1' => 41, 'pekerjaan_saksi_1' => 'Petani', 'alamat_saksi_1' => $mainAddress,
                'nik_saksi_2' => $nik(13), 'nama_saksi_2' => $this->sampleName($requestIndex + 13), 'ttl_saksi_2' => 'Sleman, 20-08-1988', 'umur_saksi_2' => 38, 'pekerjaan_saksi_2' => 'Pedagang', 'alamat_saksi_2' => $mainAddress,
                'nama_pemberi_kuasa' => $applicantName, 'pekerjaan_pemberi_kuasa' => $reporter['occupation'], 'alamat_pemberi_kuasa' => $mainAddress,
                'nama_penerima_kuasa' => $this->sampleName($requestIndex + 14), 'pekerjaan_penerima_kuasa' => 'Karyawan Swasta', 'alamat_penerima_kuasa' => $mainAddress,
            ]);
        }

        if (in_array($code, ['SKWKA', 'SKC'], true)) {
            $attorney = $person(15);
            $data = array_merge($data, [
                'attorney_name' => $attorney['name'], 'attorney_nik' => $attorney['nik'],
                'attorney_birth_place' => $attorney['birth_place'], 'attorney_birth_date' => $attorney['birth_date'],
                'attorney_gender' => $attorney['gender'], 'attorney_occupation' => $attorney['occupation'], 'attorney_address' => $attorney['address'],
                'deceased_name' => $this->sampleName($requestIndex + 16), 'death_place' => 'RSUD Sleman',
                'condition' => 'Pemohon berhalangan hadir sehingga memberikan kuasa kepada penerima kuasa.',
                'endorser_office' => 'Kalurahan Bimomartani', 'endorser_name' => 'Rasyifa Anom Sudaryono',
            ]);
        }

        if (in_array($code, ['SKTA', 'SKTH'], true)) {
            $data = array_merge($data, [
                'land_certificate_number' => sprintf('C-%03d', $requestIndex + 12),
                'land_area' => '650', 'land_area_in_words' => 'enam ratus lima puluh meter persegi',
                'land_owner_name' => $applicantName, 'land_hamlet' => 'Kepuh', 'land_village' => 'Bimomartani',
                'land_district' => 'Ngemplak', 'land_regency' => 'Sleman',
                'land_price_min' => '150000', 'land_price_max' => '225000',
                'land_measurement_letter_number' => sprintf('SU-%03d', $requestIndex + 1),
                'land_measurement_letter_date' => $date(),
            ]);
        }

        if (in_array($code, ['SPPWF', 'SKDT'], true)) {
            $origin = [
                'kk_number' => sprintf('340400%010d', $requestIndex + 120), 'head_of_family' => $applicantName,
                'address' => $mainAddress, 'rt' => '004', 'rw' => '002', 'hamlet' => 'Kepuh',
                'village' => 'Bimomartani', 'district' => 'Ngemplak', 'regency' => 'Sleman',
                'province' => 'Daerah Istimewa Yogyakarta', 'postal_code' => '55584', 'phone' => '081234567890',
            ];
            $destination = [
                'kk_number' => sprintf('340400%010d', $requestIndex + 220), 'head_of_family' => $this->sampleName($requestIndex + 17),
                'address' => 'Jl. Kaliurang Km. 12, Sleman', 'rt' => '006', 'rw' => '003', 'hamlet' => 'Sembung',
                'village' => 'Umbulmartani', 'district' => 'Ngemplak', 'regency' => 'Sleman',
                'province' => 'Daerah Istimewa Yogyakarta', 'postal_code' => '55584', 'phone' => '081298765432',
            ];
            $data = array_merge($data, [
                'origin' => $origin, 'destination' => $destination,
                'origin_kk_number' => $origin['kk_number'], 'origin_head_of_family' => $origin['head_of_family'],
                'origin_address' => $origin['address'], 'destination_address' => $destination['address'],
                'destination_kk_number' => $destination['kk_number'], 'destination_kk_status_code' => '1',
                'reason' => 'Mengikuti tempat tinggal keluarga', 'relocation_type_label' => 'Antar Desa Dalam Satu Kecamatan',
            ]);
        }

        if ($code === 'SPDP') {
            $data = array_merge($data, [
                'kk_number' => sprintf('340400%010d', $requestIndex + 120), 'member_name' => $applicantName, 'member_nik' => $applicantNik, 'member_shdk' => 'Kepala Keluarga',
                'family_members' => [['name' => $applicantName, 'nik' => $applicantNik, 'shdk' => 'Kepala Keluarga', 'note' => 'Perubahan data administrasi']],
                'education_job_changes' => [['education_before' => 'SMA', 'education_after' => 'Diploma', 'education_basis' => 'Ijazah', 'job_before' => 'Pelajar', 'job_after' => 'Wiraswasta', 'job_basis' => 'Surat keterangan kerja', 'note' => '']],
                'religion_other_changes' => [['religion_before' => 'Islam', 'religion_after' => 'Islam', 'religion_basis' => 'KTP-el', 'other_before' => 'Data lama', 'other_after' => 'Data terbaru', 'other_basis' => 'Dokumen pendukung', 'note' => '']],
                'other_element_label' => 'Elemen data lainnya', 'change_note' => 'Penyesuaian data pendidikan dan pekerjaan.',
            ]);
        }

        if (in_array($code, ['SKTS', 'SPTTS'], true)) {
            $data = array_merge($data, [
                'applicant_name' => $applicantName, 'applicant_nik' => $applicantNik, 'kk_number' => sprintf('340400%010d', $requestIndex + 120),
                'birth_place' => 'Sleman', 'birth_date' => $mainBirthDate, 'gender' => 'Laki-laki', 'occupation' => 'Wiraswasta', 'education' => 'SMA',
                'origin_address' => $mainAddress, 'origin_village' => 'Bimomartani', 'origin_district' => 'Ngemplak', 'origin_regency' => 'Sleman', 'origin_province' => 'Daerah Istimewa Yogyakarta',
                'destination_address' => 'Jl. Kaliurang Km. 12, Sleman', 'destination_address_1' => 'Padukuhan Sembung', 'destination_address_2' => 'Umbulmartani, Ngemplak, Sleman',
                'village' => 'Bimomartani', 'district' => 'Ngemplak', 'hamlet' => 'Kepuh', 'rt' => '004', 'rw' => '002',
                'reason' => 'Bekerja dan tinggal sementara bersama keluarga', 'religion' => 'Islam', 'marital_status' => 'Menikah',
                'guarantor_name' => 'Rasyifa Anom Sudaryono', 'guarantor_nik' => $nik(18), 'guarantor_address' => $mainAddress,
                'guarantor_village' => 'Bimomartani', 'guarantor_district' => 'Ngemplak', 'guarantor_regency' => 'Sleman', 'guarantor_province' => 'Daerah Istimewa Yogyakarta',
                'host_name' => 'Rasyifa Anom Sudaryono', 'host_kk_number' => sprintf('340400%010d', $requestIndex + 320), 'family_member_count' => 2,
                'family_members' => [['name' => $applicantName, 'nik' => $applicantNik], ['name' => $this->sampleName($requestIndex + 19), 'nik' => $nik(19)]],
                'letter_date' => $date(), 'applicant_number' => '471.23/', 'applicant_date' => $date(),
            ]);
        }

        if (in_array($code, ['SPCCP', 'SKKMP', 'SKUMN', 'SPNIK', 'SPNIP', 'SKTNB', 'FPDN', 'SKBKN', 'SPBML', 'SPOT', 'SPTJP'], true)) {
            $groom = $person(20, $this->sampleName($requestIndex + 20));
            $bride = $person(21, $applicantName);
            $groomFather = $person(22); $groomMother = $person(23); $brideFather = $person(24); $brideMother = $person(25);
            $data = array_merge($data, [
                'applicant_name' => $applicantName, 'name_alias' => $applicantName,
                'birth_place' => 'Sleman', 'birth_date' => $mainBirthDate, 'gender' => 'Perempuan', 'religion' => 'Islam', 'occupation' => 'Wiraswasta', 'education' => 'SMA', 'nationality' => 'WNI', 'marital_status' => 'Belum Kawin',
                'husband_name' => $groom['name'], 'husband_nik' => $groom['nik'], 'husband_birth_place' => $groom['birth_place'], 'husband_birth_date' => $groom['birth_date'], 'husband_occupation' => $groom['occupation'], 'husband_address' => $groom['address'],
                'wife_name' => $bride['name'], 'wife_nik' => $bride['nik'], 'wife_birth_place' => $bride['birth_place'], 'wife_birth_date' => $bride['birth_date'], 'wife_occupation' => $bride['occupation'], 'wife_address' => $bride['address'],
                'groom_name' => $groom['name'], 'groom_bin' => $groomFather['name'], 'groom_nik' => $groom['nik'], 'groom_birth_place' => $groom['birth_place'], 'groom_birth_date' => $groom['birth_date'], 'groom_citizenship' => 'WNI', 'groom_religion' => 'Islam', 'groom_occupation' => 'Wiraswasta', 'groom_last_education' => 'SMA', 'groom_address' => $groom['address'], 'groom_status' => 'Jejaka',
                'bride_name' => $bride['name'], 'bride_binti' => $brideMother['name'], 'bride_nik' => $bride['nik'], 'bride_birth_place' => $bride['birth_place'], 'bride_birth_date' => $bride['birth_date'], 'bride_citizenship' => 'WNI', 'bride_religion' => 'Islam', 'bride_occupation' => 'Wiraswasta', 'bride_last_education' => 'SMA', 'bride_address' => $bride['address'], 'bride_status' => 'Perawan',
                'groom_father_name' => $groomFather['name'], 'groom_father_nik' => $groomFather['nik'], 'groom_father_address' => $groomFather['address'],
                'groom_mother_name' => $groomMother['name'], 'groom_mother_nik' => $groomMother['nik'], 'groom_mother_address' => $groomMother['address'],
                'bride_father_name' => $brideFather['name'], 'bride_father_nik' => $brideFather['nik'], 'bride_father_address' => $brideFather['address'],
                'bride_mother_name' => $brideMother['name'], 'bride_mother_nik' => $brideMother['nik'], 'bride_mother_address' => $brideMother['address'],
                'ceremony_day' => 'Sabtu', 'ceremony_date' => $date(), 'ceremony_time' => '09.00 WIB', 'ceremony_place' => 'Balai Kalurahan Bimomartani',
                'registration_number' => sprintf('472/%03d', $requestIndex + 1), 'registration_date' => $date(), 'registration_officer_name' => 'Rasyifa Anom Sudaryono', 'registration_position' => 'Kamituwa',
                'marriage_date' => $date(), 'children' => [], 'marriage_witnesses' => [['name' => $this->sampleName($requestIndex + 26), 'nik' => $nik(26)], ['name' => $this->sampleName($requestIndex + 27), 'nik' => $nik(27)]],
            ]);

            // Beberapa template Model N memakai nama blok lama, bukan blok
            // groom/bride. Simpan alias yang sama agar semua bagian formulir
            // tetap terisi ketika dirender dari request hasil seeder.
            $data = array_merge($data, [
                'father_name' => $brideFather['name'],
                'father_nik' => $brideFather['nik'],
                'father_birth_place' => $brideFather['birth_place'],
                'father_birth_date' => $brideFather['birth_date'],
                'father_nationality' => 'WNI',
                'father_religion' => 'Islam',
                'father_occupation' => $brideFather['occupation'],
                'father_address' => $brideFather['address'],
                'mother_name' => $brideMother['name'],
                'mother_nik' => $brideMother['nik'],
                'mother_birth_place' => $brideMother['birth_place'],
                'mother_birth_date' => $brideMother['birth_date'],
                'mother_nationality' => 'WNI',
                'mother_religion' => 'Islam',
                'mother_occupation' => $brideMother['occupation'],
                'mother_address' => $brideMother['address'],
                'child_name' => $bride['name'],
                'child_nik' => $bride['nik'],
                'child_birth_place' => $bride['birth_place'],
                'child_birth_date' => $bride['birth_date'],
                'child_nationality' => 'WNI',
                'child_religion' => 'Islam',
                'child_occupation' => $bride['occupation'],
                'child_address' => $bride['address'],
                'child_bin_or_binti' => $brideMother['name'],
                'child_spouse_name' => $groom['name'],
                'child_spouse_nik' => $groom['nik'],
                'child_spouse_birth_place' => $groom['birth_place'],
                'child_spouse_birth_date' => $groom['birth_date'],
                'child_spouse_nationality' => 'WNI',
                'child_spouse_religion' => 'Islam',
                'child_spouse_occupation' => $groom['occupation'],
                'child_spouse_address' => $groom['address'],
                'child_spouse_bin_or_binti' => $groomFather['name'],
                'spouse_name' => $bride['name'],
                'spouse_binti' => $brideMother['name'],
                'spouse_nik' => $bride['nik'],
                'spouse_birth_place' => $bride['birth_place'],
                'spouse_birth_date' => $bride['birth_date'],
                'spouse_nationality' => 'WNI',
                'spouse_religion' => 'Islam',
                'spouse_occupation' => $bride['occupation'],
                'spouse_address' => $bride['address'],
                'deceased_full_name_alias' => $bride['name'],
                'deceased_name' => $bride['name'],
                'deceased_nik' => $bride['nik'],
                'deceased_birth_place' => $bride['birth_place'],
                'deceased_birth_date' => $bride['birth_date'],
                'deceased_nationality' => 'WNI',
                'deceased_religion' => 'Islam',
                'deceased_occupation' => $bride['occupation'],
                'deceased_address' => $bride['address'],
                'deceased_death_date' => $date(),
                'deceased_death_place' => 'RSUD Sleman',
            ]);

            if ($code === 'SKKMP') {
                $data = array_merge($data, [
                    'spouse_name' => $groom['name'],
                    'spouse_bin' => $groomFather['name'],
                    'spouse_nik' => $groom['nik'],
                    'spouse_birth_place' => $groom['birth_place'],
                    'spouse_birth_date' => $groom['birth_date'],
                    'spouse_nationality' => 'WNI',
                    'spouse_religion' => 'Islam',
                    'spouse_occupation' => $groom['occupation'],
                    'spouse_address' => $groom['address'],
                ]);
            }

            if ($code === 'SKTNB') {
                $data = array_merge($data, [
                    'applicant_name' => $groom['name'],
                    'name_alias' => $groom['name'],
                    'gender' => 'Laki-laki',
                    'marital_status' => 'Jejaka',
                ]);
            }
        }

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
            $data['jenis_kelamin'] = $isBride ? 'Perempuan' : 'Laki-laki';
            $data[$isBride ? 'status_pernikahan' : 'status_perkawinan'] = $isBride ? 'Perawan' : 'Jejaka';
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

    private function sampleName(int $sequence): string
    {
        $names = [
            'Ahmad Hidayat',
            'Siti Aminah',
            'Budi Wijaya',
            'Rasyifa Anom Sudaryono',
            'Dewi Lestari',
            'Agus Setiawan',
            'Sri Wahyuni',
            'Joko Santoso',
            'Maya Puspitasari',
            'Fajar Nugroho',
            'Nadia Kurniawati',
            'Teguh Pratama',
        ];

        return $names[abs($sequence) % count($names)];
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

        if ($key === 'blood_type' || str_contains($key, 'golongan_darah')) {
            return fake()->randomElement(['A', 'B', 'AB', 'O']);
        }

        if ($key === 'birth_cert_number' || str_contains($key, 'nomor_akta_kelahiran')) {
            return sprintf('3471-LT-%04d', $identitySequence);
        }

        if ($key === 'household_head' || str_contains($key, 'kepala_keluarga')) {
            return fake('id_ID')->name();
        }

        if ($key === 'village' || str_contains($key, 'kelurahan')) {
            return 'Bimomartani';
        }

        if ($key === 'district' || str_contains($key, 'kecamatan')) {
            return 'Ngemplak';
        }

        if ($key === 'menerangkan_bahwa' || str_contains($key, 'dipergunakan_untuk')) {
            return fake('id_ID')->randomElement([
                'Keperluan administrasi kependudukan',
                'Keperluan pendaftaran sekolah',
                'Keperluan pengajuan bantuan sosial',
                'Keperluan administrasi pekerjaan',
            ]);
        }

        if ($key === 'register_year' || str_ends_with($key, '_year')) {
            return now()->year;
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

        if (str_contains($key, 'letter_c_number')) {
            return sprintf('LC-%03d', $identitySequence);
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

        if (str_contains($key, 'nik') || str_contains($key, 'nomor_kk') || str_contains($key, 'kk_number') || str_contains($key, 'national')) {
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
