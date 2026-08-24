<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Citizen;
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

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'national_id' => ['required','string','size:16','unique:citizens,national_id'],
            'family_card_number' => ['nullable','string','size:16',],
            'full_name' => ['required','string','max:100'],
            'birth_place' => ['nullable','string','max:50'],
            'birth_date' => ['nullable','date'],
            'gender' => ['nullable', Rule::in(['Laki-laki','Perempuan'])],
            'address' => ['nullable','string'],
            'phone_number' => ['nullable','string','max:15'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'education' => ['nullable', 'string', 'max:50'],
            'marital_status' => ['nullable', 'string', 'max:30'],
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

        $validated = $request->validate([
            'national_id' => ['sometimes','required','string','size:16', Rule::unique('citizens','national_id')->ignore($citizen_id, 'citizen_id')],
            'family_card_number' => ['nullable','string','size:16',],
            'full_name' => ['sometimes','required','string','max:100'],
            'birth_place' => ['nullable','string','max:50'],
            'birth_date' => ['nullable','date'],
            'gender' => ['nullable', Rule::in(['Laki-laki','Perempuan'])],
            'address' => ['nullable','string'],
            'phone_number' => ['nullable','string','max:15'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'education' => ['nullable', 'string', 'max:50'],
            'marital_status' => ['nullable', 'string', 'max:30'],
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

}
