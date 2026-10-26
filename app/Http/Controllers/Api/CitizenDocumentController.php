<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Citizen;
use App\Models\CitizenDocument;
use App\Http\Controllers\Api\PublicLetterController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CitizenDocumentController extends Controller
{
    public function index(Request $request, int $citizen_id): JsonResponse
    {
        $citizen = Citizen::query()->findOrFail($citizen_id);
        app(PublicLetterController::class)->syncLegacyDocuments($citizen);

        $documents = $citizen->documents()
            ->where('is_active', true)
            ->latest('last_used_at')
            ->latest('created_at')
            ->get()
            ->map(fn (CitizenDocument $document): array => $this->payload($document))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen warga berhasil diambil.',
            'data' => $documents,
        ]);
    }

    public function store(Request $request, int $citizen_id): JsonResponse
    {
        $citizen = Citizen::query()->findOrFail($citizen_id);
        $validated = $request->validate([
            'document_type' => ['required', 'string', 'max:80'],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $document = $this->saveFile($citizen, $validated['file'], $validated['document_type']);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen warga berhasil ditambahkan.',
            'data' => $this->payload($document),
        ], 201);
    }

    public function show(int $citizen_id, int $document_id): JsonResponse
    {
        $document = $this->findDocument($citizen_id, $document_id);

        return response()->json([
            'success' => true,
            'data' => $this->payload($document),
        ]);
    }

    public function update(Request $request, int $citizen_id, int $document_id): JsonResponse
    {
        $citizen = Citizen::query()->findOrFail($citizen_id);
        $document = $this->findDocument($citizen_id, $document_id);
        $validated = $request->validate([
            'document_type' => ['sometimes', 'required', 'string', 'max:80'],
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        if ($request->hasFile('file')) {
            $document = $this->saveFile(
                $citizen,
                $validated['file'],
                $validated['document_type'] ?? $document->document_type,
                $document,
            );
        } else {
            $document->update([
                'document_type' => $validated['document_type'] ?? $document->document_type,
                'is_active' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Dokumen warga berhasil diperbarui.',
            'data' => $this->payload($document->fresh()),
        ]);
    }

    public function destroy(int $citizen_id, int $document_id): JsonResponse
    {
        $document = $this->findDocument($citizen_id, $document_id);
        // Soft-delete agar lampiran pengajuan lama tetap dapat dibuka dan riwayat tetap aman.
        $document->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen warga berhasil dihapus dari daftar aktif.',
            'data' => null,
        ]);
    }

    public function download(int $citizen_id, int $document_id)
    {
        $document = $this->findDocument($citizen_id, $document_id);
        abort_unless(Storage::disk('local')->exists($document->storage_path), 404, 'File dokumen tidak ditemukan.');

        return Storage::disk('local')->download(
            $document->storage_path,
            $document->file_name,
            ['Content-Type' => $document->mime_type ?: 'application/octet-stream'],
        );
    }

    private function findDocument(int $citizenId, int $documentId): CitizenDocument
    {
        return CitizenDocument::query()
            ->where('citizen_id', $citizenId)
            ->where('is_active', true)
            ->findOrFail($documentId);
    }

    private function saveFile(Citizen $citizen, $file, string $documentType, ?CitizenDocument $current = null): CitizenDocument
    {
        $hash = hash_file('sha256', $file->getRealPath());
        $extension = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $storagePath = "citizen-documents/{$citizen->citizen_id}/{$hash}.{$extension}";

        if (! Storage::disk('local')->exists($storagePath)) {
            Storage::disk('local')->putFileAs(
                "citizen-documents/{$citizen->citizen_id}",
                $file,
                "{$hash}.{$extension}",
            );
        }

        $document = $current ?: CitizenDocument::query()->firstOrNew([
            'citizen_id' => $citizen->citizen_id,
            'document_type' => $documentType,
            'file_hash' => $hash,
        ]);
        $document->fill([
            'citizen_id' => $citizen->citizen_id,
            'document_type' => $documentType,
            'file_name' => $file->getClientOriginalName(),
            'storage_path' => $storagePath,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_hash' => $hash,
            'is_active' => true,
            'last_used_at' => now(),
        ]);
        $document->save();

        return $document;
    }

    private function payload(CitizenDocument $document): array
    {
        return [
            'citizen_document_id' => $document->citizen_document_id,
            'document_type' => $document->document_type,
            'file_name' => $document->file_name,
            'mime_type' => $document->mime_type,
            'file_size' => $document->file_size,
            'uploaded_at' => $document->created_at?->toISOString(),
            'last_used_at' => $document->last_used_at?->toISOString(),
        ];
    }
}
