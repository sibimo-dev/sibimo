<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LetterType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\LetterTypeDocument;

class LetterTypeController extends Controller
{

    public function index(): JsonResponse
    {
        $letterTypes = LetterType::query()->with(['documents'])->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data jenis surat berhasil diambil.',
            'data' => $letterTypes,
        ]);
    }


    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required','string','max:20','unique:letter_types,code'],
            'letter_name' => ['required','string','max:100'],
            'description' => ['nullable','string'],
            'blade_view' => ['nullable','string','max:225'],
            'number_prefix' => ['nullable','string','max:50'],
            'is_active' => ['boolean']
        ]);

        $letterType = LetterType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jenis surat berhasil dibuat.',
            'data' => $letterType,
        ], 201);
    }


    public function show(int $letter_type_id): JsonResponse
    {
        $letterType = LetterType::query()->with(['documents'])->findOrFail($letter_type_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail jenis surat berhasil diambil.',
            'data' => $letterType,
        ]);
    }

    public function update(Request $request, int $letter_type_id): JsonResponse
    {
        $letterType = LetterType::findOrFail($letter_type_id);

        $validated = $request->validate([
            'code' => ['sometimes','required','string','max:20', Rule::unique('letter_types','code')->ignore($letterType_id, 'letter_type_id')],
            'letter_name' => ['sometimes','required','string','max:100'],
            'description' => ['nullable','string'],
            'blade_view' => ['nullable','string','max:225'],
            'number_prefix' => ['nullable','string','max:50'],
            'is_active' => ['boolean']
        ]);

        $letterType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jenis surat berhasil diperbarui.',
            'data' => $letterType,
        ]);
    }

    public function destroy(int $letter_type_id): JsonResponse
    {
        $letterType = LetterType::findOrFail($letter_type_id);
        $letterType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jenis surat berhasil dihapus.',
        ]);
    }

    public function documents(int $letterTypeId): JsonResponse
    {
        $documents = LetterTypeDocument::where('letter_type_id', $letterTypeId)->get();

        return response()->json([
            'success' => true,
            'message' => 'Data dokumen persyaratan berhasil diambil.',
            'data' => $documents,
        ]);
    }

    public function storeDocument(Request $request, int $letterTypeId): JsonResponse
    {
        $validated = $request->validate([
            'document_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_required' => ['boolean'],
        ]);
        $validated['letter_type_id'] = $letterTypeId;

        $document = LetterTypeDocument::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen persyaratan berhasil ditambahkan.',
            'data' => $document,
        ], 201);
    }

    public function destroyDocument(int $documentId): JsonResponse
    {
        $document = LetterTypeDocument::findOrFail($documentId);
        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen persyaratan berhasil dihapus.',
        ]);
    }
}
