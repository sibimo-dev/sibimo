<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VillagePotential;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class VillagePotentialController extends Controller
{

    public function index(): JsonResponse
    {
        $potentials = VillagePotential::query()->latest()->get();

        return response()->json([
            'succes' => true,
            'message' => 'Data potensi desa berhasil diambil.',
            'data' => $potentials,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => ['required', Rule::in(['UMKM','Agriculture','Tourism','BUMDes'])],
            'title' => ['required','string','max:200'],
            'description' => ['nullable','string'],
            'image' => ['nullable','string','max:255'],
            'location' => ['nullable','string','max:255']
        ]);

        $potential = VillagePotential::create($validated);

        return response()->json([
            'succes' => true,
            'message' => 'Potensi desa berhasil dibuat.',
            'data' => $potential,
        ], 201);
    }


    public function show(int $potential_id): JsonResponse
    {
        $potential = VillagePotential::query()->findOrFail($potential_id);

        return response()->json([
            'succes' => true,
            'message' => 'Detail potensi desa berhasil diambil.',
            'data' => $potential,
        ]);
    }


    public function update(Request $request, int $potential_id): JsonResponse
    {
        $potential = VillagePotential::findOrFail($potential_id);

        $validated = $request->validate([
            'category' => ['sometimes','required', Rule::in(['UMKM','Agriculture','Tourism','BUMDes'])],
            'title' => ['sometimes','required','string','max:200'],
            'description' => ['nullable','string'],
            'image' => ['nullable','string','max:255'],
            'location' => ['nullable','string','max:255']
        ]);

        $potential->update($validated);

        return response()->json([
            'succes' => true,
            'message' => 'Potensi desa berhasil diperbarui.',
            'data' => $potential,
        ]);
    }

 
    public function destroy(int $potential_id): JsonResponse
    {
        $potential = VillagePotential::findOrFail($potential_id);
        $potential->delete();

        return response()->json([
            'succes' => true,
            'message' => 'Potensi desa berhasil dihapus.',
        ]);
    }

}
