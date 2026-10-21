<?php

namespace App\Console\Commands;

use App\Models\Citizen;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCitizens extends Command
{
    protected $signature = 'citizens:import
        {file : File CSV UTF-8 hasil export Sheet2}
        {--limit=50 : Jumlah maksimum baris data yang diproses}
        {--offset=0 : Jumlah baris data yang dilewati setelah header}
        {--write : Menulis data ke database; tanpa opsi ini hanya dry-run}
        {--update-existing : Memperbarui NIK yang sudah ada; hanya berlaku bersama --write}';

    protected $description = 'Validasi dan impor data penduduk dari CSV ke tabel citizens secara aman';

    private const REQUIRED_HEADERS = [
        'NIK',
        'NAMA LENGKAP',
    ];

    private const HEADER_ALIASES = [
        'TULIS PENDUDUK/MATI/PINDAH/ORANG LUAR' => 'RECORD TYPE',
        'TULIS LAHIR01 DST/DATANG01 DST/PINDAH01 DST/MATI01 DST' => 'RECORD EVENT',
        'NOMOR KK' => 'FAMILY CARD NUMBER',
        'DUSUN' => 'DUSUN',
        'ALAMAT' => 'ALAMAT',
        'RT' => 'RT',
        'RW' => 'RW',
        'NIK' => 'NIK',
        'NAMA LENGKAP' => 'NAMA LENGKAP',
        'L/P' => 'L/P',
        'TEMPAT LAHIR' => 'TEMPAT LAHIR',
        'TANGGAL LAHIR' => 'TANGGAL LAHIR',
        'UMUR' => 'UMUR',
        'AKTA LAHIR' => 'AKTA LAHIR',
        'NOMOR AKTA LAHIR' => 'NOMOR AKTA LAHIR',
        'GOL. DARAH' => 'GOL. DARAH',
        'AGAMA' => 'AGAMA',
        'STATUS KAWIN' => 'STATUS KAWIN',
        'AKTA KAWIN' => 'AKTA KAWIN',
        'NOMOR AKTA KAWIN' => 'NOMOR AKTA KAWIN',
        'TANGGAL KAWIN' => 'TANGGAL KAWIN',
        'AKTA CERAI' => 'AKTA CERAI',
        'NOMOR AKTA CERAI' => 'NOMOR AKTA CERAI',
        'TANGGAL CERAI' => 'TANGGAL CERAI',
        'STATUS HUB DLM KELUARGA' => 'STATUS HUB DLM KELUARGA',
        'KELAINAN FISIK' => 'KELAINAN FISIK',
        'PENYANDANG CACAT' => 'PENYANDANG CACAT',
        'PENDIDIKAN TERAKHIR' => 'PENDIDIKAN TERAKHIR',
        'JENIS PEKERJAAN' => 'JENIS PEKERJAAN',
        'NIK IBU' => 'NIK IBU',
        'NAMA LENGKAP IBU' => 'NAMA LENGKAP IBU',
        'NIK AYAH' => 'NIK AYAH',
        'NAMA LENGKAP AYAH' => 'NAMA LENGKAP AYAH',
        'KEWARGANEGARAAN' => 'KEWARGANEGARAAN',
    ];

    public function handle(): int
    {
        $path = $this->resolvePath((string) $this->argument('file'));
        $limit = filter_var($this->option('limit'), FILTER_VALIDATE_INT);
        $offset = filter_var($this->option('offset'), FILTER_VALIDATE_INT);

        if ($limit === false || $limit < 1) {
            $this->error('Opsi --limit harus berupa angka minimal 1.');

            return self::FAILURE;
        }

        if ($offset === false || $offset < 0) {
            $this->error('Opsi --offset harus berupa angka minimal 0.');

            return self::FAILURE;
        }

        if ($this->option('update-existing') && ! $this->option('write')) {
            $this->error('Opsi --update-existing hanya boleh dipakai bersama --write.');

            return self::FAILURE;
        }

        if (! is_file($path) || ! is_readable($path)) {
            $this->error("File tidak ditemukan atau tidak bisa dibaca: {$path}");

            return self::FAILURE;
        }

        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'csv') {
            $this->error('Command ini menerima CSV UTF-8. Buka Excel, pilih Sheet2, lalu Save As > CSV UTF-8 (*.csv).');

            return self::FAILURE;
        }

        try {
            [$headers, $rows] = $this->readCsv($path, $offset, $limit);
        } catch (\Throwable $exception) {
            $this->error('CSV tidak bisa dibaca: '.$exception->getMessage());

            return self::FAILURE;
        }

        $missingHeaders = array_values(array_diff(self::REQUIRED_HEADERS, $headers));
        if ($missingHeaders !== []) {
            $this->error('Header wajib tidak ditemukan: '.implode(', ', $missingHeaders));

            return self::FAILURE;
        }

        $prepared = [];
        $errors = [];
        $seenNik = [];

        foreach ($rows as $row) {
            [$payload, $rowErrors] = $this->mapRow($row['values'], $headers, $row['line']);
            $errors = [...$errors, ...$rowErrors];

            if ($payload === null) {
                continue;
            }

            $nik = $payload['national_id'];
            if (isset($seenNik[$nik])) {
                $errors[] = [$row['line'], 'NIK', "duplikat dengan baris CSV {$seenNik[$nik]}"];
                continue;
            }

            $seenNik[$nik] = $row['line'];
            $prepared[] = $payload;
        }

        if ($errors !== []) {
            $this->table(['Baris CSV', 'Field', 'Masalah'], $errors);
            $this->error('Import dibatalkan. Perbaiki data CSV lalu jalankan dry-run lagi.');

            return self::FAILURE;
        }

        $existing = Citizen::query()
            ->whereIn('national_id', array_column($prepared, 'national_id'))
            ->pluck('national_id')
            ->map(static fn ($value): string => (string) $value)
            ->all();
        $existing = array_fill_keys($existing, true);

        $toInsert = [];
        $toUpdate = [];
        $skipped = [];

        foreach ($prepared as $payload) {
            $nik = $payload['national_id'];

            if (isset($existing[$nik])) {
                if ($this->option('update-existing')) {
                    $toUpdate[] = $payload;
                } else {
                    $skipped[] = $nik;
                }

                continue;
            }

            $toInsert[] = $payload;
        }

        $this->info(sprintf(
            'Valid: %d baris | Insert baru: %d | Sudah ada: %d%s',
            count($prepared),
            count($toInsert),
            count($skipped),
            $this->option('update-existing') ? ' | Update: '.count($toUpdate) : '',
        ));

        if (! $this->option('write')) {
            $this->comment('Dry-run selesai. Tidak ada perubahan database. Tambahkan --write jika hasil sudah benar.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($toInsert, $toUpdate): void {
            foreach ($toInsert as $payload) {
                Citizen::query()->create($payload);
            }

            foreach ($toUpdate as $payload) {
                $nik = $payload['national_id'];
                unset($payload['national_id']);

                Citizen::query()
                    ->where('national_id', $nik)
                    ->update($payload);
            }
        });

        $this->info(sprintf(
            'Berhasil disimpan. Insert: %d | Update: %d | Dilewati karena sudah ada: %d',
            count($toInsert),
            count($toUpdate),
            count($skipped),
        ));

        return self::SUCCESS;
    }

    private function readCsv(string $path, int $offset, int $limit): array
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new \RuntimeException('gagal membuka file.');
        }

        try {
            $firstLine = fgets($handle);
            if ($firstLine === false) {
                throw new \RuntimeException('file kosong.');
            }

            $delimiter = $this->detectDelimiter($firstLine);
            rewind($handle);

            $rawHeaders = fgetcsv($handle, 0, $delimiter);
            if ($rawHeaders === false) {
                throw new \RuntimeException('header tidak ditemukan.');
            }

            $headers = array_map(function ($header): string {
                $header = $this->normalizeHeader($header);

                return self::HEADER_ALIASES[$header] ?? $header;
            }, $rawHeaders);
            $headers = array_values(array_filter($headers, static fn (string $header): bool => $header !== ''));

            $rows = [];
            $dataIndex = 0;
            $line = 1;

            while (($values = fgetcsv($handle, 0, $delimiter)) !== false) {
                $line++;
                if ($this->isEmptyRow($values)) {
                    continue;
                }

                $dataIndex++;
                if ($dataIndex <= $offset) {
                    continue;
                }

                if (count($values) > count($headers)) {
                    throw new \RuntimeException("baris {$line} memiliki kolom lebih banyak daripada header.");
                }

                $values = array_pad($values, count($headers), null);
                $rows[] = ['line' => $line, 'values' => array_combine($headers, $values)];

                if (count($rows) >= $limit) {
                    break;
                }
            }

            return [$headers, $rows];
        } finally {
            fclose($handle);
        }
    }

    private function mapRow(array $row, array $headers, int $line): array
    {
        $errors = [];
        $value = fn (string $header): ?string => $this->nullableString($row[$header] ?? null);

        $nik = $this->normalizeIdentifier($value('NIK'));
        if ($nik === null || ! preg_match('/^\d{16}$/', $nik)) {
            $errors[] = [$line, 'NIK', 'wajib berisi tepat 16 digit angka'];
        }

        $name = $value('NAMA LENGKAP');
        if ($name === null) {
            $errors[] = [$line, 'NAMA LENGKAP', 'wajib diisi'];
        }

        $familyCardNumber = $this->normalizeIdentifier($value('FAMILY CARD NUMBER'));
        if ($familyCardNumber !== null && ! preg_match('/^\d{16}$/', $familyCardNumber)) {
            $errors[] = [$line, 'Nomor KK', 'harus kosong atau tepat 16 digit angka'];
        }

        $birthDate = $this->parseDate($value('TANGGAL LAHIR'));
        if ($value('TANGGAL LAHIR') !== null && $birthDate === null) {
            $errors[] = [$line, 'TANGGAL LAHIR', 'format tanggal tidak dikenali'];
        }

        $marriageDate = $this->parseDate($value('TANGGAL KAWIN'));
        if ($value('TANGGAL KAWIN') !== null && $marriageDate === null) {
            $errors[] = [$line, 'TANGGAL KAWIN', 'format tanggal tidak dikenali'];
        }

        $divorceDate = $this->parseDate($value('TANGGAL CERAI'));
        if ($value('TANGGAL CERAI') !== null && $divorceDate === null) {
            $errors[] = [$line, 'TANGGAL CERAI', 'format tanggal tidak dikenali'];
        }

        $gender = $this->normalizeGender($value('L/P'));
        if ($value('L/P') !== null && $gender === null) {
            $errors[] = [$line, 'L/P', 'gunakan Laki-Laki/Laki-laki atau Perempuan'];
        }

        $age = $value('UMUR');
        if ($age !== null && (! ctype_digit($age) || (int) $age > 255)) {
            $errors[] = [$line, 'UMUR', 'harus berupa angka 0 sampai 255'];
        }

        if ($errors !== []) {
            return [null, $errors];
        }

        return [[
            'record_type' => $value('RECORD TYPE'),
            'record_event' => $value('RECORD EVENT'),
            'national_id' => $nik,
            'family_card_number' => $familyCardNumber,
            'dusun' => $value('DUSUN'),
            'full_name' => $name,
            'birth_place' => $value('TEMPAT LAHIR'),
            'birth_date' => $birthDate,
            'age' => $age === null ? null : (int) $age,
            'gender' => $gender,
            'address' => $value('ALAMAT'),
            'rt' => $value('RT'),
            'rw' => $value('RW'),
            'phone_number' => null,
            'birth_certificate_status' => $value('AKTA LAHIR'),
            'birth_certificate_number' => $value('NOMOR AKTA LAHIR'),
            'blood_type' => $value('GOL. DARAH'),
            'occupation' => $value('JENIS PEKERJAAN'),
            'education' => $value('PENDIDIKAN TERAKHIR'),
            'marital_status' => $value('STATUS KAWIN'),
            'marriage_certificate_status' => $value('AKTA KAWIN'),
            'marriage_certificate_number' => $value('NOMOR AKTA KAWIN'),
            'marriage_date' => $marriageDate,
            'divorce_certificate_status' => $value('AKTA CERAI'),
            'divorce_certificate_number' => $value('NOMOR AKTA CERAI'),
            'divorce_date' => $divorceDate,
            'family_relationship' => $value('STATUS HUB DLM KELUARGA'),
            'physical_disability' => $value('KELAINAN FISIK'),
            'disability_status' => $value('PENYANDANG CACAT'),
            'religion' => $value('AGAMA'),
            'mother_national_id' => $this->normalizeIdentifier($value('NIK IBU')),
            'mother_name' => $value('NAMA LENGKAP IBU'),
            'father_national_id' => $this->normalizeIdentifier($value('NIK AYAH')),
            'father_name' => $value('NAMA LENGKAP AYAH'),
            'nationality' => $value('KEWARGANEGARAAN'),
            'ktp_address' => null,
            'status' => 'Active',
        ], []];
    }

    private function resolvePath(string $path): string
    {
        if (is_file($path)) {
            return $path;
        }

        return base_path($path);
    }

    private function detectDelimiter(string $line): string
    {
        $counts = [
            ',' => substr_count($line, ','),
            ';' => substr_count($line, ';'),
            "\t" => substr_count($line, "\t"),
        ];

        arsort($counts);

        return (string) array_key_first($counts);
    }

    private function normalizeHeader(mixed $value): string
    {
        $value = preg_replace('/^\xEF\xBB\xBF/', '', (string) $value) ?? (string) $value;
        $value = preg_replace('/\s+/', ' ', trim($value)) ?? trim($value);

        return mb_strtoupper($value, 'UTF-8');
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' || $value === '-' ? null : $value;
    }

    private function normalizeIdentifier(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return ltrim(trim($value), "'\"");
    }

    private function normalizeGender(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return match (mb_strtolower(preg_replace('/\s+/', ' ', trim($value)), 'UTF-8')) {
            'l', 'laki-laki', 'laki laki', 'male' => 'Laki-laki',
            'p', 'perempuan', 'female' => 'Perempuan',
            default => null,
        };
    }

    private function parseDate(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // Excel dapat mengekspor tanggal sebagai serial number saat workbook
        // disimpan ke CSV. Epoch 1899-12-30 mengikuti konvensi Excel/PhpSpreadsheet.
        if (is_numeric($value) && (float) $value >= 20000 && (float) $value <= 100000) {
            return Carbon::create(1899, 12, 30)
                ->addDays((int) floor((float) $value))
                ->format('Y-m-d');
        }

        foreach (['Y-m-d H:i:s', 'Y-m-d', 'd/m/Y', 'd-m-Y', 'd.m.Y', 'm/d/Y', 'm-d-Y'] as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);
                if ($date !== false && $date->format($format) === $value) {
                    return $date->format('Y-m-d');
                }
            } catch (\Throwable) {
                // Coba format berikutnya.
            }
        }

        return null;
    }

    private function isEmptyRow(array $values): bool
    {
        return count(array_filter($values, static fn ($value): bool => trim((string) $value) !== '')) === 0;
    }
}
