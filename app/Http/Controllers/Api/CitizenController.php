<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Citizen;
use App\Models\Region;
use App\Models\Rw;
use App\Models\Rt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class CitizenController extends Controller
{
    private const EVENT_TYPES = [
        'LAHIR' => 'Lahir',
        'DATANG' => 'Datang',
        'PINDAH' => 'Pindah',
        'MATI' => 'Mati',
    ];
    
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $perPage = min(max($perPage, 1), 100);
        $search = trim((string) $request->query('search', ''));

        // Endpoint ini dipakai oleh tabel/pagination admin. Gunakan join
        // ringan untuk wilayah dan jangan memuat relasi keluarga/accessor
        // untuk setiap baris. Detail warga tetap memuat relasi melalui show().
        // Tidak ada batas total record; hanya jumlah baris per halaman yang
        // dibatasi agar response tetap kecil.
        $citizenFields = [
            'citizen_id', 'record_type', 'national_id', 'family_card_number', 'full_name',
            'birth_place', 'birth_date', 'age', 'gender', 'address', 'rt_id', 'rt',
            'phone_number', 'birth_certificate_status', 'birth_certificate_number',
            'blood_type', 'occupation', 'education', 'marital_status',
            'marriage_certificate_status', 'marriage_certificate_number', 'marriage_date',
            'divorce_certificate_status', 'divorce_certificate_number', 'divorce_date',
            'family_relationship', 'physical_disability', 'disability_type',
            'disability_status', 'religion', 'belief_organization', 'nationality',
            'mother_national_id', 'mother_name', 'father_national_id', 'father_name',
            'spouse_national_id', 'ktp_address', 'previous_address', 'passport_number',
            'passport_expiry_date', 'status', 'created_at', 'updated_at',
        ];

        $selects = collect($citizenFields)
            ->map(fn (string $field) => 'citizens.' . $field)
            ->all();
        $selects[] = 'rt_area.number as area_rt_number';
        $selects[] = 'rt_area.head_name as rt_head_name';
        $selects[] = 'rw_area.number as area_rw_number';
        $selects[] = 'rw_area.head_name as rw_head_name';
        $selects[] = 'region_area.name as area_dusun';
        $selects[] = 'region_area.head_name as hamlet_head_name';

        $query = DB::table('citizens')
            ->leftJoin('rts as rt_area', 'rt_area.id', '=', 'citizens.rt_id')
            ->leftJoin('rws as rw_area', 'rw_area.id', '=', 'rt_area.rw_id')
            ->leftJoin('regions as region_area', 'region_area.region_id', '=', 'rw_area.region_id')
            ->select($selects)
            ->when($search !== '', function ($query) use ($search): void {
                $like = '%' . $search . '%';
                $query->where(function ($nested) use ($like): void {
                    $nested->where('citizens.full_name', 'like', $like)
                        ->orWhere('citizens.national_id', 'like', $like)
                        ->orWhere('citizens.family_card_number', 'like', $like)
                        ->orWhere('citizens.address', 'like', $like)
                        ->orWhere('citizens.phone_number', 'like', $like)
                        ->orWhere('citizens.rt', 'like', $like)
                        ->orWhere('rt_area.number', 'like', $like)
                        ->orWhere('rw_area.number', 'like', $like)
                        ->orWhere('region_area.name', 'like', $like);
                });
            })
            ->orderByDesc('citizens.citizen_id');

        $mapRow = function (object $row): array {
            $data = (array) $row;
            $data['rt'] = $data['rt'] ?? $data['area_rt_number'];
            $data['rw'] = $data['area_rw_number'];
            $data['dusun'] = $data['area_dusun'];
            $data['rt_head_name'] = $data['rt_head_name'];
            $data['rw_head_name'] = $data['rw_head_name'];
            $data['hamlet_head_name'] = $data['hamlet_head_name'];
            unset($data['area_rt_number'], $data['area_rw_number'], $data['area_dusun']);
            return $data;
        };

        $isAll = $request->boolean('all') || $request->query('per_page') === 'all';

        if ($isAll) {
            $collection = $query->get();
            $citizens = $collection->map($mapRow)->values();

            return response()->json([
                'success' => true,
                'message' => 'Seluruh data citizen berhasil diambil.',
                'data' => $citizens,
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $citizens->count(),
                    'total' => $citizens->count(),
                    'active_total' => DB::table('citizens')->where('status', 'Active')->count(),
                ],
            ]);
        }

        $paginator = $query->paginate($perPage);

        $citizens = $paginator->getCollection()
            ->map($mapRow)
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Data citizen berhasil diambil.',
            'data' => $citizens,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                // Dashboard memakai pagination yang sama. Kirim jumlah aktif
                // dari database, bukan menghitung hanya dari 10 baris halaman 1.
                'active_total' => DB::table('citizens')->where('status', 'Active')->count(),
            ],
        ]);
    }

    public function nationalIds(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Citizen::query()->orderBy('citizen_id')->pluck('national_id'),
        ]);
    }

    /**
     * Public aggregate-only citizen data for the public statistics page.
     * No names, NIKs, addresses, or contact details are exposed here.
     */
    public function statistics(): JsonResponse
    {
        $citizens = Citizen::query()
            ->where('status', 'Active')
            ->get([
                'family_card_number',
                'birth_date',
                'gender',
                'education',
                'occupation',
                'religion',
                'blood_type',
                'rt_id',
            ]);

        $ageGroups = [
            '0–4', '5–9', '10–14', '15–19', '20–24', '25–29', '30–34', '35–39',
            '40–44', '45–49', '50–54', '55–59', '60–64', '65–69', '70–74', '75+',
        ];
        $ageRows = collect($ageGroups)->mapWithKeys(fn (string $label) => [
            $label => ['group' => $label, 'total' => 0, 'male' => 0, 'female' => 0],
        ])->all();

        $aggregate = function (callable $labeler, array $initial = []) use ($citizens): array {
            $rows = $initial;
            foreach ($citizens as $citizen) {
                $label = $labeler($citizen) ?: 'Tidak diisi';
                $rows[$label] ??= ['group' => $label, 'total' => 0, 'male' => 0, 'female' => 0];
                $rows[$label]['total']++;
                if ($citizen->gender === 'Laki-laki') $rows[$label]['male']++;
                if ($citizen->gender === 'Perempuan') $rows[$label]['female']++;
            }

            return array_values($rows);
        };

        $ageLabel = function (Citizen $citizen) use ($ageGroups): string {
            if (!$citizen->birth_date) return 'Tidak diisi';
            if ($citizen->birth_date->year < 1900 || $citizen->birth_date->isFuture()) return 'Tidak diisi';
            $age = $citizen->birth_date->age;
            if ($age < 5) return $ageGroups[0];
            if ($age >= 75) return '75+';

            return $ageGroups[(int) floor($age / 5)];
        };

        $genderRows = [
            'Laki-laki' => ['group' => 'Laki-laki', 'total' => 0, 'male' => 0, 'female' => 0],
            'Perempuan' => ['group' => 'Perempuan', 'total' => 0, 'male' => 0, 'female' => 0],
        ];
        $bloodTypeRows = collect(['A', 'B', 'AB', 'O', 'Tidak diisi'])->mapWithKeys(fn (string $label) => [
            $label => ['group' => $label, 'total' => 0, 'male' => 0, 'female' => 0],
        ])->all();

        $maleCount = $citizens->where('gender', 'Laki-laki')->count();
        $femaleCount = $citizens->where('gender', 'Perempuan')->count();

        // Statistik wilayah memakai JOIN langsung agar tidak bentrok dengan kolom
        // legacy `citizens.rt` yang berupa nomor RT teks:
        // citizens.rt_id → rts.rw_id → rws.region_id → regions.region_id.
        // $regionRows = Citizen::query()
        //     ->where('citizens.status', 'Active')
        //     ->leftJoin('rts', 'citizens.rt_id', '=', 'rts.id')
        //     ->leftJoin('rws', 'rts.rw_id', '=', 'rws.id')
        //     ->leftJoin('regions', 'rws.region_id', '=', 'regions.region_id')
        //     // Data import lama menyimpan nama dusun di kolom address karena
        //     // kolom dusun memang tidak tersedia di tabel citizens. Pakai
        //     // address sebagai fallback hanya jika nilainya sama persis dengan
        //     // nama padukuhan, sehingga alamat jalan biasa tidak salah dihitung.
        //     ->leftJoin('regions as legacy_regions', function ($join): void {
        //         $join->whereRaw('LOWER(TRIM(legacy_regions.name)) = LOWER(TRIM(citizens.address))');
        //     })
        //     ->selectRaw("COALESCE(NULLIF(regions.name, ''), legacy_regions.name, 'Tidak diisi') AS `group`")
        //     ->selectRaw('COUNT(*) AS total')
        //     ->selectRaw("SUM(CASE WHEN citizens.gender = 'Laki-laki' THEN 1 ELSE 0 END) AS male")
        //     ->selectRaw("SUM(CASE WHEN citizens.gender = 'Perempuan' THEN 1 ELSE 0 END) AS female")
        //     // Group berdasarkan nama dusun agar data lama yang sudah punya
        //     // rt_id dan data lama yang memakai fallback address bergabung ke
        //     // satu baris dusun yang sama.
        //     ->groupByRaw("COALESCE(NULLIF(regions.name, ''), legacy_regions.name, 'Tidak diisi')")
        //     ->orderByRaw("CASE WHEN `group` = 'Tidak diisi' THEN 1 ELSE 0 END, `group`")
        //     ->get()
        //     ->map(fn ($row) => [
        //         'group' => $row->group,
        //         'total' => (int) $row->total,
        //         'male' => (int) $row->male,
        //         'female' => (int) $row->female,
        //     ])
        //     ->values()
        //     ->all();

        $regionRows = Citizen::query()
            ->where('citizens.status', 'Active')
            ->leftJoin('rts', 'citizens.rt_id', '=', 'rts.id')
            ->leftJoin('rws', 'rts.rw_id', '=', 'rws.id')
            ->leftJoin('regions', 'rws.region_id', '=', 'regions.region_id')
            ->leftJoin('regions as legacy_regions', function ($join): void {
                $join->whereRaw(
                    'LOWER(TRIM(legacy_regions.name)) = LOWER(TRIM(citizens.address))'
                );
            })
            ->selectRaw("
                COALESCE(
                    NULLIF(regions.name, ''),
                    legacy_regions.name,
                    'Tidak diisi'
                ) AS region_name,
                citizens.gender
            ")
            ->toBase();

        $regionRows = DB::query()
            ->fromSub($regionRows, 'citizen_regions')
            ->selectRaw("
                region_name AS `group`,
                COUNT(*) AS total,
                SUM(CASE WHEN gender = 'Laki-laki' THEN 1 ELSE 0 END) AS male,
                SUM(CASE WHEN gender = 'Perempuan' THEN 1 ELSE 0 END) AS female
            ")
            ->groupBy('region_name')
            ->orderByRaw("
                CASE WHEN region_name = 'Tidak diisi' THEN 1 ELSE 0 END,
                region_name
            ")
            ->get()
            ->map(fn ($row) => [
                'group' => $row->group,
                'total' => (int) $row->total,
                'male' => (int) $row->male,
                'female' => (int) $row->female,
            ])
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_population' => $citizens->count(),
                    'kk_count' => $citizens->pluck('family_card_number')->filter()->unique()->count(),
                    'male_count' => $maleCount,
                    'female_count' => $femaleCount,
                ],
                'categories' => [
                    'age' => $aggregate($ageLabel, $ageRows),
                    'education' => $aggregate(fn (Citizen $citizen) => $citizen->education),
                    'occupation' => $aggregate(fn (Citizen $citizen) => $citizen->occupation),
                    'gender' => $aggregate(fn (Citizen $citizen) => $citizen->gender, $genderRows),
                    'religion' => $aggregate(fn (Citizen $citizen) => $citizen->religion),
                    'blood_type' => $aggregate(fn (Citizen $citizen) => $citizen->blood_type, $bloodTypeRows),
                ],
                'regions' => array_values($regionRows),
            ],
        ]);
    }

    public function eventCounters(Request $request): JsonResponse
    {
        $year = (int) ($request->query('year') ?: now()->year);
        abort_if($year < 2000 || $year > 2200, 422, 'Tahun peristiwa tidak valid.');

        $data = collect(self::EVENT_TYPES)->map(function (string $label, string $type) use ($year): array {
            $counter = DB::table('citizen_event_counters')
                ->where('event_type', $type)
                ->where('event_year', $year)
                ->first();
            $lastNumber = $counter?->last_number ?? $this->legacyLastEventNumber($type, $year);

            return [
                'type' => $type,
                'label' => $label,
                'year' => $year,
                'last_number' => (int) $lastNumber,
                'next_number' => (int) $lastNumber + 1,
                'next_code' => $label . str_pad((string) ((int) $lastNumber + 1), 2, '0', STR_PAD_LEFT),
            ];
        })->values();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function validateImport(Request $request): JsonResponse
    {
        // Berkas dibatasi 1.000 baris, tetapi pembacaan CSV di server tetap
        // diberi ruang waktu yang cukup pada hosting dengan max_execution_time
        // bawaan yang terlalu pendek.
        set_time_limit(300);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $file = $validated['file'];
        abort_if(
            strtolower($file->getClientOriginalExtension()) !== 'csv',
            422,
            'File import harus berformat CSV.'
        );

        $handle = fopen($file->getRealPath(), 'rb');
        abort_if($handle === false, 422, 'File CSV tidak dapat dibaca.');

        try {
            $firstLine = fgets($handle);
            abort_if($firstLine === false, 422, 'File CSV tidak berisi header.');

            $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
            rewind($handle);

            $headers = fgetcsv($handle, 0, $delimiter) ?: [];
            $normalizedHeaders = collect($headers)
                // Header template menggunakan huruf kapital (mis. NIK dan NAMA LENGKAP),
                // sedangkan pengecekan memakai alias huruf kecil. Samakan case agar
                // validasi tidak menolak file template yang sebenarnya benar.
                ->map(fn ($header) => strtolower((string) preg_replace('/[^a-z0-9]+/i', '', trim((string) $header))))
                ->filter()
                ->values();

            abort_if(
                !$normalizedHeaders->contains(fn (string $header) => in_array($header, ['nik', 'nationalid'], true))
                || !$normalizedHeaders->contains(fn (string $header) => in_array($header, ['fullname', 'nama', 'namalengkap'], true)),
                422,
                'Header file harus memiliki kolom NIK dan Nama Lengkap.'
            );

            $rowCount = 0;
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (collect($row)->every(fn ($value) => trim((string) $value) === '')) {
                    continue;
                }

                $rowCount++;
                abort_if(
                    $rowCount > 1000,
                    422,
                    'Import dibatalkan. File berisi lebih dari 1.000 data warga.'
                );
            }
        } finally {
            fclose($handle);
        }

        abort_if($rowCount === 0, 422, 'File tidak berisi data warga.');

        return response()->json([
            'success' => true,
            'message' => 'File siap diimpor.',
            'data' => ['row_count' => $rowCount, 'max_rows' => 1000],
        ]);
    }

    public function updateImportRelations(Request $request): JsonResponse
    {
        // Satu batch relasi dapat berisi sampai 1.000 warga dan melakukan
        // validasi serta pembaruan dalam satu transaksi.
        set_time_limit(300);

        $validated = $request->validate([
            'relations' => ['required', 'array', 'max:1000'],
            'relations.*.citizen_id' => ['required', 'integer', 'distinct', Rule::exists('citizens', 'citizen_id')],
            'relations.*.mother_national_id' => ['sometimes', 'nullable', 'string', 'size:16', 'regex:/^\d{16}$/'],
            'relations.*.father_national_id' => ['sometimes', 'nullable', 'string', 'size:16', 'regex:/^\d{16}$/'],
            'relations.*.spouse_national_id' => ['sometimes', 'nullable', 'string', 'size:16', 'regex:/^\d{16}$/'],
        ]);

        $relationFields = ['mother_national_id', 'father_national_id', 'spouse_national_id'];
        $relationNiks = collect($validated['relations'])
            ->flatMap(fn (array $row) => collect($relationFields)->map(fn (string $field) => $row[$field] ?? null))
            ->filter()
            ->unique()
            ->values();
        $citizensByNik = $relationNiks->isEmpty()
            ? collect()
            : Citizen::query()->whereIn('national_id', $relationNiks)->pluck('national_id', 'national_id');

        $updatedCount = 0;
        $unresolvedCount = 0;

        DB::transaction(function () use ($validated, $relationFields, $citizensByNik, &$updatedCount, &$unresolvedCount): void {
            foreach ($validated['relations'] as $row) {
                $changes = [];
                foreach ($relationFields as $field) {
                    if (!array_key_exists($field, $row)) continue;
                    $nik = trim((string) ($row[$field] ?? ''));
                    if ($nik === '') {
                        $changes[$field] = null;
                    } elseif ($citizensByNik->has($nik)) {
                        $changes[$field] = $nik;
                    } else {
                        $unresolvedCount++;
                    }
                }

                if ($changes !== []) {
                    Citizen::where('citizen_id', $row['citizen_id'])->update($changes);
                    $updatedCount++;
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Relasi keluarga berhasil diproses.',
            'data' => [
                'updated_count' => $updatedCount,
                'unresolved_count' => $unresolvedCount,
            ],
        ]);
    }

    /**
     * Recalculate the stored age for every citizen in one database operation.
     * The Citizen model also calculates age dynamically, but this keeps the
     * persisted age column synchronized for exports and other consumers.
     */
    public function refreshAges(): JsonResponse
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $updatedCount = DB::update(<<<'SQL'
                UPDATE citizens
                SET age = CASE
                    WHEN birth_date IS NULL OR birth_date < '1900-01-01' OR birth_date > CURDATE() THEN NULL
                    ELSE TIMESTAMPDIFF(YEAR, birth_date, CURDATE())
                END
            SQL);
        } elseif ($driver === 'pgsql') {
            $updatedCount = DB::update(<<<'SQL'
                UPDATE citizens
                SET age = CASE
                    WHEN birth_date IS NULL OR birth_date < DATE '1900-01-01' OR birth_date > CURRENT_DATE THEN NULL
                    ELSE EXTRACT(YEAR FROM AGE(CURRENT_DATE, birth_date))::integer
                END
            SQL);
        } elseif ($driver === 'sqlite') {
            $updatedCount = DB::update(<<<'SQL'
                UPDATE citizens
                SET age = CASE
                    WHEN birth_date IS NULL OR date(birth_date) < date('1900-01-01') OR date(birth_date) > date('now') THEN NULL
                    ELSE CAST((julianday('now') - julianday(birth_date)) / 365.2425 AS INTEGER)
                END
            SQL);
        } else {
            abort(422, 'Driver database belum didukung untuk pembaruan umur massal.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Umur warga berhasil diperbarui.',
            'data' => [
                'updated_count' => $updatedCount,
                'updated_at' => now()->toISOString(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->normalizeInput($request);
        $validated = $request->validate([
            'record_type'                  => ['nullable', 'string', 'max:100'],
            'national_id'                  => ['required','string','size:16','regex:/^\d{16}$/','unique:citizens,national_id'],
            'family_card_number'           => ['nullable','string','size:16','regex:/^\d{16}$/'],
            'full_name'                    => ['required','string','max:100'],
            'birth_place'                  => ['nullable','string','max:50'],
            'birth_date'                   => ['nullable', 'date', 'after_or_equal:1900-01-01', 'before_or_equal:today'],
            'age'                          => ['nullable', 'integer', 'min:0', 'max:150'],
            'gender'                       => ['nullable', Rule::in(['Laki-laki','Perempuan'])],
            'address'                      => ['nullable','string'],
            // Wilayah — rt_id adalah FK utama; rt string adalah fallback teks lama.
            'rt_id'                        => ['nullable', 'integer', Rule::exists('rts', 'id')],
            'rt'                           => ['nullable', 'string', 'max:20'],
            'phone_number'                 => ['nullable','string','max:15'],
            'birth_certificate_status'     => ['nullable', 'string', 'max:50'],
            'birth_certificate_number'     => ['nullable', 'string', 'max:50'],
            'blood_type'                   => ['nullable', 'string', 'max:10'],
            'occupation'                   => ['nullable', 'string', 'max:100'],
            'education'                    => ['nullable', 'string', 'max:50'],
            'marital_status'               => ['nullable', 'string', 'max:30'],
            'marriage_certificate_status'  => ['nullable', 'string', 'max:50'],
            'marriage_certificate_number'  => ['nullable', 'string', 'max:50'],
            'marriage_date'                => ['nullable', 'date'],
            'divorce_certificate_status'   => ['nullable', 'string', 'max:50'],
            'divorce_certificate_number'   => ['nullable', 'string', 'max:50'],
            'divorce_date'                 => ['nullable', 'date'],
            'family_relationship'          => ['nullable', 'string', 'max:100'],
            'physical_disability'          => ['nullable', 'string', 'max:100'],
            'disability_status'            => ['nullable', 'string', 'max:100'],
            'disability_type'              => ['nullable', 'string', 'max:100'],
            'religion'                     => ['nullable', 'string', 'max:100'],
            'belief_organization'          => ['nullable', 'string', 'max:150'],
            // NIK keluarga wajib sudah terdaftar di data warga (boleh dikosongkan).
            'mother_national_id'           => ['nullable', 'string', 'size:16', 'regex:/^\d{16}$/', Rule::exists('citizens', 'national_id')],
            'mother_name'                  => ['nullable', 'string', 'max:150'],
            'father_national_id'           => ['nullable', 'string', 'size:16', 'regex:/^\d{16}$/', Rule::exists('citizens', 'national_id')],
            'father_name'                  => ['nullable', 'string', 'max:150'],
            'spouse_national_id'           => ['nullable', 'string', 'size:16', 'regex:/^\d{16}$/', Rule::exists('citizens', 'national_id')],
            'nationality'                  => ['nullable', 'string', 'max:50'],
            'ktp_address'                  => ['nullable', 'string'],
            'previous_address'             => ['nullable', 'string'],
            'passport_number'              => ['nullable', 'string', 'max:50'],
            'passport_expiry_date'         => ['nullable', 'date'],
            'status'                       => ['nullable', Rule::in(['Active', 'Pindah', 'Mati'])],
        ]);

        $validated = $this->resolveAreaRelations($validated);
        // Umur adalah nilai turunan dari birth_date. Jangan gunakan angka
        // umur mentah dari file/form karena dapat melampaui tipe kolom DB;
        // event saving pada model akan menghitungnya kembali.
        $validated['age'] = null;
        $citizen = Citizen::create($this->orderCitizenAttributes($validated));
        $citizen->load('rtArea.rw.region');

        return response()->json([
            'success' => true,
            'message' => 'Citizen berhasil dibuat.',
            'data'    => $this->appendAreaData($citizen),
        ], 201);
    }

    public function show(int $citizen_id): JsonResponse
    {
        $citizen = Citizen::query()
            ->with(['mother', 'father', 'spouse', 'rtArea.rw.region'])
            ->findOrFail($citizen_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail citizen berhasil diambil.',
            'data'    => $this->enrichCitizenRelations($citizen),
        ]);
    }

    public function showByNik(string $nik): JsonResponse
    {
        $citizen = Citizen::query()
            ->with(['mother', 'father', 'spouse', 'rtArea.rw.region'])
            ->where('national_id', $nik)
            ->first();

        if (! $citizen) {
            return response()->json([
                'success' => false,
                'message' => 'Data warga tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data citizen berhasil diambil.',
            'data'    => $this->enrichCitizenRelations($citizen),
        ]);
    }

 
    public function update(Request $request, int $citizen_id): JsonResponse
    {
        $citizen = Citizen::findOrFail($citizen_id);
        $this->normalizeInput($request);

        $validated = $request->validate([
            'record_type'                  => ['sometimes', 'nullable', 'string', 'max:100'],
            'national_id'                  => ['sometimes','required','string','size:16','regex:/^\d{16}$/', Rule::unique('citizens','national_id')->ignore($citizen_id, 'citizen_id')],
            'family_card_number'           => ['nullable','string','size:16','regex:/^\d{16}$/'],
            'full_name'                    => ['sometimes','required','string','max:100'],
            'birth_place'                  => ['nullable','string','max:50'],
            'birth_date'                   => ['nullable', 'date', 'after_or_equal:1900-01-01', 'before_or_equal:today'],
            'age'                          => ['nullable', 'integer', 'min:0', 'max:150'],
            'gender'                       => ['nullable', Rule::in(['Laki-laki','Perempuan'])],
            'address'                      => ['nullable','string'],
            'rt_id'                        => ['nullable', 'integer', Rule::exists('rts', 'id')],
            'rt'                           => ['nullable', 'string', 'max:20'],
            'phone_number'                 => ['nullable','string','max:15'],
            'birth_certificate_status'     => ['nullable', 'string', 'max:50'],
            'birth_certificate_number'     => ['nullable', 'string', 'max:50'],
            'blood_type'                   => ['nullable', 'string', 'max:10'],
            'occupation'                   => ['nullable', 'string', 'max:100'],
            'education'                    => ['nullable', 'string', 'max:50'],
            'marital_status'               => ['nullable', 'string', 'max:30'],
            'marriage_certificate_status'  => ['nullable', 'string', 'max:50'],
            'marriage_certificate_number'  => ['nullable', 'string', 'max:50'],
            'marriage_date'                => ['nullable', 'date'],
            'divorce_certificate_status'   => ['nullable', 'string', 'max:50'],
            'divorce_certificate_number'   => ['nullable', 'string', 'max:50'],
            'divorce_date'                 => ['nullable', 'date'],
            'family_relationship'          => ['nullable', 'string', 'max:100'],
            'physical_disability'          => ['nullable', 'string', 'max:100'],
            'disability_status'            => ['nullable', 'string', 'max:100'],
            'disability_type'              => ['nullable', 'string', 'max:100'],
            'religion'                     => ['nullable', 'string', 'max:100'],
            'belief_organization'          => ['nullable', 'string', 'max:150'],
            'mother_national_id'           => ['nullable', 'string', 'size:16', 'regex:/^\d{16}$/', Rule::exists('citizens', 'national_id')],
            'mother_name'                  => ['nullable', 'string', 'max:150'],
            'father_national_id'           => ['nullable', 'string', 'size:16', 'regex:/^\d{16}$/', Rule::exists('citizens', 'national_id')],
            'father_name'                  => ['nullable', 'string', 'max:150'],
            'spouse_national_id'           => ['nullable', 'string', 'size:16', 'regex:/^\d{16}$/', Rule::exists('citizens', 'national_id')],
            'nationality'                  => ['nullable', 'string', 'max:50'],
            'ktp_address'                  => ['nullable', 'string'],
            'previous_address'             => ['nullable', 'string'],
            'passport_number'              => ['nullable', 'string', 'max:50'],
            'passport_expiry_date'         => ['nullable', 'date'],
            'status'                       => ['nullable', Rule::in(['Active', 'Pindah', 'Mati'])],
        ]);

        $validated = $this->resolveAreaRelations($validated);
        $citizen->update($this->orderCitizenAttributes($validated));
        $citizen->load('rtArea.rw.region');

        return response()->json([
            'success' => true,
            'message' => 'Citizen berhasil diperbarui.',
            'data'    => $this->appendAreaData($citizen),
        ]);
    }

    public function destroy(int $citizen_id): JsonResponse
    {
        $citizen = Citizen::findOrFail($citizen_id);
        $citizen->delete();

        return response()->json([
            'success' => true,
            'message' => 'Citizen berhasil dihapus.',
        ]);
    }

    private function normalizeInput(Request $request): void
    {
        $birthDate = trim((string) $request->input('birth_date', ''));
        if ($birthDate !== '') {
            // Lindungi API dari client lama yang masih mengirim tahun pendek
            // hasil parsing CSV, misalnya 0020-12-11 untuk 11 Desember 2020.
            // Terima juga format lama dengan jam, misalnya 0083-08-02
            // 00:00:00. Parser lama menghasilkan tahun 00xx dan harus
            // dikembalikan ke abad yang benar sebelum validasi tanggal.
            if (preg_match('/^0*(\d{1,4})[-\/]?(\d{2})[-\/]?(\d{2})(?:\s.*)?$/', $birthDate, $matches)) {
                $year = (int) $matches[1];
                if ($year < 100) {
                    $year += $year >= 30 ? 1900 : 2000;
                }
                $month = (int) $matches[2];
                $day = (int) $matches[3];
                $birthDate = checkdate($month, $day, $year)
                    ? sprintf('%04d-%02d-%02d', $year, $month, $day)
                    : '';
            }

            $request->merge(['birth_date' => $birthDate !== '' ? $birthDate : null]);
        }

        $age = $request->input('age');
        if ($age !== null && $age !== '' && (!is_numeric($age) || (int) $age < 0 || (int) $age > 150)) {
            $request->merge(['age' => null]);
        }

        $gender = trim((string) $request->input('gender', ''));
        if ($gender !== '') {
            $request->merge([
                'gender' => strcasecmp($gender, 'perempuan') === 0 ? 'Perempuan' : 'Laki-laki',
            ]);
        }

        $birthCertificateStatus = trim((string) $request->input('birth_certificate_status', ''));
        if ($birthCertificateStatus !== 'Ada') {
            $request->merge(['birth_certificate_number' => null]);
        }
    }

    /**
     * Eloquent biasanya mempertahankan urutan atribut dari request. Import
     * Excel dapat mengirim gender sebelum birth_place, sedangkan beberapa
     * driver/versi model membangun SQL berdasarkan urutan atribut model.
     * Susun ulang secara eksplisit agar setiap nilai selalu sejajar dengan
     * kolom citizens yang benar.
     */
    private function orderCitizenAttributes(array $data): array
    {
        $fields = [
            'record_type',
            'national_id',
            'family_card_number',
            'full_name',
            'birth_place',
            'birth_date',
            'age',
            'gender',
            'address',
            'rt_id',
            'rt',
            'phone_number',
            'birth_certificate_status',
            'birth_certificate_number',
            'blood_type',
            'occupation',
            'education',
            'marital_status',
            'marriage_certificate_status',
            'marriage_certificate_number',
            'marriage_date',
            'divorce_certificate_status',
            'divorce_certificate_number',
            'divorce_date',
            'family_relationship',
            'physical_disability',
            'disability_type',
            'disability_status',
            'religion',
            'belief_organization',
            'nationality',
            'mother_national_id',
            'mother_name',
            'father_national_id',
            'father_name',
            'spouse_national_id',
            'ktp_address',
            'previous_address',
            'passport_number',
            'passport_expiry_date',
            'status',
        ];

        return collect($fields)
            ->filter(fn (string $field) => array_key_exists($field, $data))
            ->mapWithKeys(fn (string $field) => [$field => $data[$field]])
            ->all();
    }

    /**
     * Tambahkan field wilayah turunan (rw, dusun, padukuhan_id, rw_id) ke
     * response citizen dengan menelusuri rantai rt_id → rts → rws → regions.
     */
    private function appendAreaData(\App\Models\Citizen $citizen, bool $includeAppends = true): array
    {
        if (!$includeAppends) {
            $citizen->setAppends([]);
        }
        $data = $citizen->toArray();

        $rt = $citizen->getRtModel();
        if ($rt) {
            $rw     = $rt->rw;
            $region = $rw?->region;

            // Selalu ekspos nomor RT/RW dari FK; kolom string lama sebagai fallback.
            $data['rt']          = $citizen->getAttribute('rt') ?? $rt->number;
            $data['rw']          = $rw?->number;
            $data['dusun']       = $region?->name;
            $data['rt_id']       = $rt->id;
            $data['rw_id']       = $rw?->id;
            $data['padukuhan_id']= $region?->region_id;
            $data['rt_head_name']  = $rt->head_name;
            $data['rw_head_name']  = $rw?->head_name;
            $data['hamlet_head_name'] = $region?->head_name;
        }

        return $data;
    }

    /**
     * Lengkapi relasi keluarga (father, mother, spouse, family_members)
     * jika NIK orang tua/pasangan belum tersimpan secara eksplisit di kolom foreign key.
     */
    private function enrichCitizenRelations(\App\Models\Citizen $citizen): array
    {
        $familyMembers = collect();
        if (!empty($citizen->family_card_number)) {
            $familyMembers = Citizen::query()
                ->with(['rtArea.rw.region'])
                ->where('family_card_number', $citizen->family_card_number)
                ->get();
        }

        // Jika relasi father belum terhubung via father_national_id, cari dari nama ayah di dalam KK
        if (!$citizen->father && !empty($citizen->father_name) && $familyMembers->isNotEmpty()) {
            $matchedFather = $familyMembers->first(function ($c) use ($citizen) {
                return $c->gender === 'Laki-laki' &&
                    (strcasecmp(trim($c->full_name), trim($citizen->father_name)) === 0 ||
                     stripos($c->family_relationship ?? '', 'Kepala') !== false);
            });
            if (!$matchedFather && !empty($citizen->father_national_id)) {
                $matchedFather = Citizen::where('national_id', $citizen->father_national_id)->first();
            }
            if ($matchedFather) {
                $citizen->setRelation('father', $matchedFather);
            }
        }

        // Jika relasi mother belum terhubung via mother_national_id, cari dari nama ibu di dalam KK
        if (!$citizen->mother && !empty($citizen->mother_name) && $familyMembers->isNotEmpty()) {
            $matchedMother = $familyMembers->first(function ($c) use ($citizen) {
                return $c->gender === 'Perempuan' &&
                    (strcasecmp(trim($c->full_name), trim($citizen->mother_name)) === 0 ||
                     stripos($c->family_relationship ?? '', 'Istri') !== false);
            });
            if (!$matchedMother && !empty($citizen->mother_national_id)) {
                $matchedMother = Citizen::where('national_id', $citizen->mother_national_id)->first();
            }
            if ($matchedMother) {
                $citizen->setRelation('mother', $matchedMother);
            }
        }

        // Jika relasi spouse belum terhubung via spouse_national_id
        if (!$citizen->spouse && $familyMembers->isNotEmpty()) {
            $oppositeGender = $citizen->gender === 'Laki-laki' ? 'Perempuan' : 'Laki-laki';
            $matchedSpouse = $familyMembers->first(function ($c) use ($oppositeGender, $citizen) {
                return $c->citizen_id !== $citizen->citizen_id &&
                    $c->gender === $oppositeGender &&
                    (stripos($c->family_relationship ?? '', 'Istri') !== false ||
                     stripos($c->family_relationship ?? '', 'Suami') !== false ||
                     stripos($c->family_relationship ?? '', 'Kepala') !== false);
            });
            if ($matchedSpouse) {
                $citizen->setRelation('spouse', $matchedSpouse);
            }
        }

        $data = $this->appendAreaData($citizen);
        if ($citizen->relationLoaded('father') && $citizen->father) {
            $data['father'] = $this->appendAreaData($citizen->father);
        }
        if ($citizen->relationLoaded('mother') && $citizen->mother) {
            $data['mother'] = $this->appendAreaData($citizen->mother);
        }
        if ($citizen->relationLoaded('spouse') && $citizen->spouse) {
            $data['spouse'] = $this->appendAreaData($citizen->spouse);
        }
        if ($familyMembers->isNotEmpty()) {
            $data['family_members'] = $familyMembers->map(fn ($m) => $this->appendAreaData($m))->values()->all();
        }

        return $data;
    }

    /**
     * Resolve the canonical area IDs while keeping the old text columns in
     * sync for existing forms/imports that still send dusun/rw/rt strings.
     * Juga sinkronisasi rt string dari rt_id jika hanya rt_id yang dikirim.
     */
    private function resolveAreaRelations(array $data): array
    {
        $rt = !empty($data['rt_id']) ? Rt::with('rw.region')->find($data['rt_id']) : null;
        $rw = !empty($data['rw_id']) ? Rw::with('region')->find($data['rw_id']) : null;
        $region = !empty($data['padukuhan_id']) ? Region::find($data['padukuhan_id']) : null;

        if ($rt) {
            if ($rw && (int) $rt->rw_id !== (int) $rw->id) {
                abort(422, 'RT yang dipilih tidak berada di RW tersebut.');
            }
            $rw ??= $rt->rw;
            if ($region && (int) $rw->region_id !== (int) $region->region_id) {
                abort(422, 'RW yang dipilih tidak berada di padukuhan tersebut.');
            }
            $region ??= $rw->region;
        } elseif ($rw) {
            if ($region && (int) $rw->region_id !== (int) $region->region_id) {
                abort(422, 'RW yang dipilih tidak berada di padukuhan tersebut.');
            }
            $region ??= $rw->region;
        } elseif ($region) {
            // Region saja tetap valid untuk data lama yang belum memilih RW/RT.
        } elseif (array_key_exists('dusun', $data) || array_key_exists('rw', $data) || array_key_exists('rt', $data)) {
            $dusun = trim((string) ($data['dusun'] ?? ''));
            // Fallback untuk template/data lama yang menyimpan nama dusun
            // pada kolom ALAMAT. Hanya dipakai jika cocok persis dengan master
            // regions, bukan untuk alamat jalan bebas.
            if ($dusun === '' && filled($data['address'] ?? null)) {
                $dusun = trim((string) $data['address']);
            }
            $region = Region::query()
                ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($dusun)])
                ->first();
            $rw = $region && filled($data['rw'] ?? null)
                ? Rw::where('region_id', $region->region_id)->get()->first(fn ($item) => $this->sameAreaNumber($item->number, $data['rw']))
                : null;
            $rt = $rw && filled($data['rt'] ?? null)
                ? Rt::where('rw_id', $rw->id)->get()->first(fn ($item) => $this->sameAreaNumber($item->number, $data['rt']))
                : null;
        }

        if ($region) {
            $data['padukuhan_id'] = $region->region_id;
            $data['dusun'] = $region->name;
        }
        if ($rw) {
            $data['rw_id'] = $rw->id;
            $data['rw'] = $rw->number;
        }
        if ($rt) {
            $data['rt_id'] = $rt->id;
            $data['rt'] = $rt->number;
        }

        return $data;
    }

    private function sameAreaNumber(mixed $left, mixed $right): bool
    {
        $normalize = static fn ($value) => preg_replace('/^0+/', '', trim((string) $value)) ?: '0';

        return $normalize($left) === $normalize($right);
    }

    private function normalizeEventType(?string $value): string
    {
        $normalized = strtoupper(trim((string) $value));
        foreach (self::EVENT_TYPES as $type => $label) {
            if ($normalized === $type || $normalized === strtoupper($label) || str_starts_with($normalized, $type)) {
                return $type;
            }
        }

        return 'LAHIR';
    }

    private function legacyLastEventNumber(string $eventType, int $year): int
    {
        $label = self::EVENT_TYPES[$eventType];
        $events = Citizen::query()
            ->where(function ($query) use ($year): void {
                $query->where('record_year', $year)
                    ->orWhere(function ($fallback) use ($year): void {
                        $fallback->whereNull('record_year')->whereYear('created_at', $year);
                    });
            })
            ->pluck('record_event');

        return $events->map(function ($event) use ($label): int {
            if (preg_match('/^' . preg_quote($label, '/') . '(\d+)$/i', trim((string) $event), $matches)) {
                return (int) $matches[1];
            }

            return 0;
        })->max() ?: 0;
    }

    private function allocateRecordEvent(string $eventType, int $year): string
    {
        $label = self::EVENT_TYPES[$eventType] ?? self::EVENT_TYPES['LAHIR'];

        return DB::transaction(function () use ($eventType, $year, $label): string {
            $initialNumber = $this->legacyLastEventNumber($eventType, $year);
            DB::table('citizen_event_counters')->insertOrIgnore([
                'event_type' => $eventType,
                'event_year' => $year,
                'last_number' => $initialNumber,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $counter = DB::table('citizen_event_counters')
                ->where('event_type', $eventType)
                ->where('event_year', $year)
                ->lockForUpdate()
                ->first();
            $nextNumber = ((int) ($counter?->last_number ?? $initialNumber)) + 1;

            DB::table('citizen_event_counters')
                ->where('id', $counter->id)
                ->update(['last_number' => $nextNumber, 'updated_at' => now()]);

            return $label . str_pad((string) $nextNumber, 2, '0', STR_PAD_LEFT);
        });
    }

}
