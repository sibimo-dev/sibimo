<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LetterType;
use App\Models\LetterTypeDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LetterTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $letterTypes = LetterType::query()
            ->with(['signer'])
            ->withCount('documents')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data tipe surat berhasil diambil.',
            'data' => $letterTypes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:letter_types,code'],
            'letter_name' => ['required', 'string', 'max:100'],
            'category' => ['required', Rule::in(['Perintah', 'Keterangan', 'Pengantar', 'Permohonan', 'Pernyataan'])],
            'number_prefix' => ['nullable', 'string', 'max:50'],
            'processing_time' => ['nullable', 'string', 'max:50'],
            'signature_method' => ['required', Rule::in(['digital', 'manual'])],
            'signer_id' => ['nullable', 'exists:staff,staff_id'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $letterType = LetterType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tipe surat berhasil dibuat.',
            'data' => $letterType,
        ], 201);
    }

    public function show(int $letterType_id): JsonResponse
    {
        $letterType = LetterType::query()
            ->with(['signer', 'documents'])
            ->withCount('documents')
            ->findOrFail($letterType_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail tipe surat berhasil diambil.',
            'data' => $letterType,
        ]);
    }

    public function update(Request $request, int $letterType_id): JsonResponse
    {
        $letterType = LetterType::findOrFail($letterType_id);

        $validated = $request->validate([
            'code' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('letter_types', 'code')->ignore($letterType_id, 'letter_type_id')],
            'letter_name' => ['sometimes', 'required', 'string', 'max:100'],
            'category' => ['sometimes', 'required', Rule::in(['Perintah', 'Keterangan', 'Pengantar', 'Permohonan', 'Pernyataan'])],
            'number_prefix' => ['nullable', 'string', 'max:50'],
            'processing_time' => ['nullable', 'string', 'max:50'],
            'signature_method' => ['sometimes', 'required', Rule::in(['digital', 'manual'])],
            'signer_id' => ['nullable', 'exists:staff,staff_id'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $letterType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tipe surat berhasil diperbarui.',
            'data' => $letterType,
        ]);
    }

    public function destroy(int $letterType_id): JsonResponse
    {
        $letterType = LetterType::findOrFail($letterType_id);
    
        // Hapus semua dokumen persyaratan yang terkait terlebih dahulu
        $letterType->documents()->delete();
    
        // Baru hapus tipe surat
        $letterType->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'Tipe surat berhasil dihapus.',
        ]);
    }

    // ===== Persyaratan Dokumen (nested) =====

    public function documents(int $letterType_id): JsonResponse
    {
        $documents = LetterTypeDocument::where('letter_type_id', $letterType_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Data dokumen persyaratan berhasil diambil.',
            'data' => $documents,
        ]);
    }

    public function storeDocument(Request $request, $letterType_id): JsonResponse
    {
        $letterType_id = (int) $letterType_id;
    
        $validated = $request->validate([
            'document_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_required' => ['boolean'],
        ]);
    
        $validated['letter_type_id'] = $letterType_id;
    
        $document = LetterTypeDocument::create($validated);
    
        return response()->json([
            'success' => true,
            'message' => 'Dokumen persyaratan berhasil ditambahkan.',
            'data' => $document,
        ], 201);
    }

    public function updateDocument(Request $request, int $letterTypeDocument_id): JsonResponse
    {
        $document = LetterTypeDocument::findOrFail($letterTypeDocument_id);

        $validated = $request->validate([
            'document_name' => ['sometimes', 'required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_required' => ['boolean'],
        ]);

        $document->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen persyaratan berhasil diperbarui.',
            'data' => $document,
        ]);
    }

    public function destroyDocument(int $letterTypeDocument_id): JsonResponse
    {
        LetterTypeDocument::findOrFail($letterTypeDocument_id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen persyaratan berhasil dihapus.',
        ]);
    }
}
