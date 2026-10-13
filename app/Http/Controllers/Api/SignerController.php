<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Signer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SignerController extends Controller
{
    public function index(): JsonResponse
    {
        $signers = Signer::query()->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data pejabat penandatangan berhasil diambil.',
            'data' => $signers,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:100'],
        ]);

        $signer = Signer::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pejabat penandatangan berhasil ditambahkan.',
            'data' => $signer,
        ], 201);
    }

    public function update(Request $request, int $signer_id): JsonResponse
    {
        $signer = Signer::findOrFail($signer_id);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'position' => ['sometimes', 'required', 'string', 'max:100'],
        ]);

        $signer->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pejabat penandatangan berhasil diperbarui.',
            'data' => $signer,
        ]);
    }

    public function destroy(int $signer_id): JsonResponse
    {
        Signer::findOrFail($signer_id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pejabat penandatangan berhasil dihapus.',
        ]);
    }
}
