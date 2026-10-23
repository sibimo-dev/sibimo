<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Citizen;
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
    
    public function index(): JsonResponse
    {
        $citizens = Citizen::query()->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data citizen berhasil diambil.',
            'data' => $citizens,
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
            ->get(['family_card_number', 'birth_date', 'gender', 'education', 'occupation', 'religion', 'blood_type', 'dusun']);

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
                // Statistik wilayah dihitung dari dusun pada data warga aktif,
                // sehingga hasil import citizens langsung tercermin di public.
                'regions' => $aggregate(fn (Citizen $citizen) => $citizen->dusun),
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

    public function store(Request $request): JsonResponse
    {
        $this->normalizeInput($request);
        $validated = $request->validate([
            'record_type' => ['nullable', 'string', 'max:100'],
            'record_event' => ['nullable', 'string', 'max:100'],
            'record_event_type' => ['nullable', 'string', 'max:20'],
            'record_year' => ['nullable', 'integer', 'between:2000,2200'],
            'title_code' => ['nullable', 'string', 'max:10'],
            'national_id' => ['required','string','size:16','regex:/^\d{16}$/','unique:citizens,national_id'],
            'family_card_number' => ['nullable','string','size:16','regex:/^\d{16}$/'],
            'dusun' => ['nullable', 'string', 'max:100'],
            'full_name' => ['required','string','max:100'],
            'birth_place' => ['nullable','string','max:50'],
            'birth_date' => ['nullable','date'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'gender' => ['nullable', Rule::in(['Laki-laki','Perempuan'])],
            'address' => ['nullable','string'],
            'rt' => ['nullable', 'string', 'max:20'],
            'rw' => ['nullable', 'string', 'max:20'],
            'rt_head_name' => ['nullable', 'string', 'max:100'],
            'rw_head_name' => ['nullable', 'string', 'max:100'],
            'phone_number' => ['nullable','string','max:15'],
            'birth_certificate_status' => ['nullable', 'string', 'max:50'],
            'birth_certificate_number' => ['nullable', 'string', 'max:50'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'education' => ['nullable', 'string', 'max:50'],
            'marital_status' => ['nullable', 'string', 'max:30'],
            'marriage_certificate_status' => ['nullable', 'string', 'max:50'],
            'marriage_certificate_number' => ['nullable', 'string', 'max:50'],
            'marriage_date' => ['nullable', 'date'],
            'divorce_certificate_status' => ['nullable', 'string', 'max:50'],
            'divorce_certificate_number' => ['nullable', 'string', 'max:50'],
            'divorce_date' => ['nullable', 'date'],
            'family_relationship' => ['nullable', 'string', 'max:100'],
            'physical_disability' => ['nullable', 'string', 'max:100'],
            'disability_status' => ['nullable', 'string', 'max:100'],
            'religion' => ['nullable', 'string', 'max:30'],
            'belief_organization' => ['nullable', 'string', 'max:150'],
            'mother_national_id' => ['nullable', 'string', 'size:16', 'regex:/^\d{16}$/'],
            'mother_name' => ['nullable', 'string', 'max:100'],
            'father_national_id' => ['nullable', 'string', 'size:16', 'regex:/^\d{16}$/'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:50'],
            'ktp_address' => ['nullable', 'string'],
            'previous_address' => ['nullable', 'string'],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'passport_expiry_date' => ['nullable', 'date'],
            'status' => ['nullable', Rule::in(['Active', 'Pindah'])],
        ]);

        $eventType = $this->normalizeEventType($request->input('record_event_type') ?: $request->input('record_event'));
        $validated['record_event'] = $this->allocateRecordEvent($eventType, (int) now()->year);
        $validated['record_year'] = (int) now()->year;
        unset($validated['record_event_type']);

        $citizen = Citizen::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Citizen berhasil dibuat.',
            'data' => $citizen,
        ], 201);
    }

    public function show(int $citizen_id): JsonResponse
    {
        $citizen = Citizen::query()->findOrFail($citizen_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail citizen berhasil diambil.',
            'data' => $citizen,
        ]);
    }

 
    public function update(Request $request, int $citizen_id): JsonResponse
    {
        $citizen = Citizen::findOrFail($citizen_id);
        $this->normalizeInput($request);

        $validated = $request->validate([
            'record_type' => ['sometimes', 'nullable', 'string', 'max:100'],
            'record_event' => ['sometimes', 'nullable', 'string', 'max:100'],
            'record_event_type' => ['sometimes', 'nullable', 'string', 'max:20'],
            'record_year' => ['sometimes', 'nullable', 'integer', 'between:2000,2200'],
            'title_code' => ['sometimes', 'nullable', 'string', 'max:10'],
            'national_id' => ['sometimes','required','string','size:16','regex:/^\d{16}$/', Rule::unique('citizens','national_id')->ignore($citizen_id, 'citizen_id')],
            'family_card_number' => ['nullable','string','size:16','regex:/^\d{16}$/'],
            'dusun' => ['sometimes', 'nullable', 'string', 'max:100'],
            'full_name' => ['sometimes','required','string','max:100'],
            'birth_place' => ['nullable','string','max:50'],
            'birth_date' => ['nullable','date'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'gender' => ['nullable', Rule::in(['Laki-laki','Perempuan'])],
            'address' => ['nullable','string'],
            'rt' => ['nullable', 'string', 'max:20'],
            'rw' => ['nullable', 'string', 'max:20'],
            'rt_head_name' => ['nullable', 'string', 'max:100'],
            'rw_head_name' => ['nullable', 'string', 'max:100'],
            'phone_number' => ['nullable','string','max:15'],
            'birth_certificate_status' => ['nullable', 'string', 'max:50'],
            'birth_certificate_number' => ['nullable', 'string', 'max:50'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'education' => ['nullable', 'string', 'max:50'],
            'marital_status' => ['nullable', 'string', 'max:30'],
            'marriage_certificate_status' => ['nullable', 'string', 'max:50'],
            'marriage_certificate_number' => ['nullable', 'string', 'max:50'],
            'marriage_date' => ['nullable', 'date'],
            'divorce_certificate_status' => ['nullable', 'string', 'max:50'],
            'divorce_certificate_number' => ['nullable', 'string', 'max:50'],
            'divorce_date' => ['nullable', 'date'],
            'family_relationship' => ['nullable', 'string', 'max:100'],
            'physical_disability' => ['nullable', 'string', 'max:100'],
            'disability_status' => ['nullable', 'string', 'max:100'],
            'religion' => ['nullable', 'string', 'max:30'],
            'belief_organization' => ['nullable', 'string', 'max:150'],
            'mother_national_id' => ['nullable', 'string', 'size:16', 'regex:/^\d{16}$/'],
            'mother_name' => ['nullable', 'string', 'max:100'],
            'father_national_id' => ['nullable', 'string', 'size:16', 'regex:/^\d{16}$/'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:50'],
            'ktp_address' => ['nullable', 'string'],
            'previous_address' => ['nullable', 'string'],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'passport_expiry_date' => ['nullable', 'date'],
            'status' => ['nullable', Rule::in(['Active', 'Pindah'])],
        ]);

        if ($request->filled('record_event_type')) {
            $requestedType = $this->normalizeEventType($request->input('record_event_type'));
            $currentType = $this->normalizeEventType($citizen->record_event);
            if (!$citizen->record_event || $requestedType !== $currentType) {
                $validated['record_event'] = $this->allocateRecordEvent($requestedType, (int) now()->year);
                $validated['record_year'] = (int) now()->year;
            }
        }
        unset($validated['record_event_type']);

        $citizen->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Citizen berhasil diperbarui.',
            'data' => $citizen,
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
