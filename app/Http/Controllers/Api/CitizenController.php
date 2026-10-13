<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Citizen;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class CitizenController extends Controller
{
    
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
            ->get(['family_card_number', 'birth_date', 'gender', 'education', 'occupation', 'religion']);

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
                ],
                'regions' => Region::query()
                    ->latest()
                    ->get(['name', 'population', 'male_count', 'female_count'])
                    ->map(fn (Region $region) => [
                        'group' => $region->name,
                        'total' => (int) $region->population,
                        'male' => (int) $region->male_count,
                        'female' => (int) $region->female_count,
                    ])
                    ->values(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->normalizeInput($request);
        $validated = $request->validate([
            'record_type' => ['nullable', 'string', 'max:100'],
            'record_event' => ['nullable', 'string', 'max:100'],
            'national_id' => ['required','string','size:16','unique:citizens,national_id'],
            'family_card_number' => ['nullable','string','size:16',],
            'dusun' => ['nullable', 'string', 'max:100'],
            'full_name' => ['required','string','max:100'],
            'birth_place' => ['nullable','string','max:50'],
            'birth_date' => ['nullable','date'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'gender' => ['nullable', Rule::in(['Laki-laki','Perempuan'])],
            'address' => ['nullable','string'],
            'rt' => ['nullable', 'string', 'max:20'],
            'rw' => ['nullable', 'string', 'max:20'],
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
            'mother_national_id' => ['nullable', 'string', 'size:16'],
            'mother_name' => ['nullable', 'string', 'max:100'],
            'father_national_id' => ['nullable', 'string', 'size:16'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:50'],
            'ktp_address' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['Active', 'Pindah'])],
        ]);

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
            'national_id' => ['sometimes','required','string','size:16', Rule::unique('citizens','national_id')->ignore($citizen_id, 'citizen_id')],
            'family_card_number' => ['nullable','string','size:16',],
            'dusun' => ['sometimes', 'nullable', 'string', 'max:100'],
            'full_name' => ['sometimes','required','string','max:100'],
            'birth_place' => ['nullable','string','max:50'],
            'birth_date' => ['nullable','date'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'gender' => ['nullable', Rule::in(['Laki-laki','Perempuan'])],
            'address' => ['nullable','string'],
            'rt' => ['nullable', 'string', 'max:20'],
            'rw' => ['nullable', 'string', 'max:20'],
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
            'mother_national_id' => ['nullable', 'string', 'size:16'],
            'mother_name' => ['nullable', 'string', 'max:100'],
            'father_national_id' => ['nullable', 'string', 'size:16'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:50'],
            'ktp_address' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['Active', 'Pindah'])],
        ]);

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
    }

}
