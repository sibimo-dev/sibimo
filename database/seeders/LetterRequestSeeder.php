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
        $signerIds = DB::table('staff')
            ->where('is_signer', true)
            ->orderBy('staff_id')
            ->pluck('staff_id');

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
            ->get(['letter_type_id', 'code', 'letter_name', 'signature_method', 'number_prefix'])
            ->values()
            ->each(function ($type, int $index) use (
                $citizens,
                $userIds,
                $signerIds,
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
                    ? $signerIds->first()
                    : null;
                $userId = $userIds->first();
                $citizen = $citizens->isNotEmpty()
                    ? $citizens->get($index % $citizens->count())
                    : null;
                $formData = $this->generateFormData(
                    $fieldsByLetterType->get($type->letter_type_id, collect()),
                    $index,
                );

                DB::table('letter_requests')->updateOrInsert(
                    ['request_code' => $requestCode],
                    [
                        'citizen_id' => $citizen?->citizen_id,
                        'applicant_name' => $citizen?->full_name ?? fake('id_ID')->name(),
                        'applicant_nik' => sprintf('340000000000%04d', $index + 1),
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

    private function generateFormData($fields, int $requestIndex): array
    {
        $data = [];

        foreach ($fields as $fieldIndex => $field) {
            $data[$field->field_key] = $this->fakeValueForField(
                $field,
                ($requestIndex * 100) + $fieldIndex + 1,
            );
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

        if ($key === 'rt' || str_ends_with($key, '_rt')) {
            return str_pad((string) fake()->numberBetween(1, 20), 3, '0', STR_PAD_LEFT);
        }

        if ($key === 'rw' || str_ends_with($key, '_rw')) {
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
            return fake('id_ID')->sentence(8);
        }

        return fake('id_ID')->words(3, true);
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
