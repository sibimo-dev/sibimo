<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LetterRequest;
use App\Models\LetterRequestAttachment;
use App\Models\LetterRequestStatusHistory;
use App\Models\LetterType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LetterRequestController extends Controller
{
    public function index(): JsonResponse
    {
        $letterRequests = LetterRequest::query()
        ->with([
            'citizen',
            'letterType.signer',
            'verifier',
            'authorizedSigner'
        ])
        ->latest('submitted_at')
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data permohonan surat berhasil diambil.',
            'data' => $letterRequests,
        ]);
    }

    // Dipanggil dari LetterCreateView.vue -- step 3 "Cetak Surat"
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'letter_type_id' => ['required', 'exists:letter_types,letter_type_id'],
            'citizen_id' => ['nullable', 'exists:citizens,citizen_id'],
            'applicant_name' => ['required', 'string', 'max:100'],
            'applicant_nik' => ['required', 'string', 'size:16'],
            'applicant_phone' => ['nullable', 'string', 'max:15'],
            'applicant_address' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'source' => ['nullable', Rule::in(['Online', 'Manual (Kelurahan)'])],
        ]);

        $letterType = LetterType::findOrFail($validated['letter_type_id']);

        // signature_type ikut default dari letter_type, bisa diubah lagi saat otorisasi
        $validated['signature_type'] = $letterType->signature_method === 'digital' ? 'digital' : 'manual';
        $validated['source'] = $validated['source'] ?? 'Manual (Kelurahan)';

        $letterRequest = LetterRequest::create($validated);
        $letterRequest->load(['citizen', 'letterType.signer']);

        return response()->json([
            'success' => true,
            'message' => 'Permohonan surat berhasil dibuat.',
            'data' => $letterRequest,
        ], 201);
    }

    public function show(int $letterRequest_id): JsonResponse
    {
        $letterRequest = LetterRequest::query()
            ->with(['citizen', 'letterType.signer', 'verifier', 'authorizedSigner', 'attachments', 'statusHistories'])
            ->findOrFail($letterRequest_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail permohonan surat berhasil diambil.',
            'data' => $letterRequest,
        ]);
    }

    public function update(Request $request, int $letterRequest_id): JsonResponse
    {
        $letterRequest = LetterRequest::findOrFail($letterRequest_id);

        $validated = $request->validate([
            'applicant_name' => ['sometimes', 'required', 'string', 'max:100'],
            'applicant_phone' => ['nullable', 'string', 'max:15'],
            'applicant_address' => ['sometimes', 'required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $letterRequest->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Permohonan surat berhasil diperbarui.',
            'data' => $letterRequest,
        ]);
    }

    public function destroy(int $letterRequest_id): JsonResponse
    {
        LetterRequest::findOrFail($letterRequest_id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permohonan surat berhasil dihapus.',
        ]);
    }

    // ===== Halaman Verifikasi Surat (LetterVerificationView.vue) =====
    // Tombol "Setujui Permohonan" -> status jadi Diverifikasi
    // Tombol "Tolak Permohonan"   -> status jadi Ditolak
    public function verify(Request $request, int $letterRequest_id): JsonResponse
    {
        $letterRequest = LetterRequest::findOrFail($letterRequest_id);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['verified', 'rejected'])],
            'notes' => ['nullable', 'string'],
            'verified_by' => ['required', 'exists:users,user_id'],
        ]);

        $letterRequest->update([
            'status' => $validated['status'],
            'verified_by' => $validated['verified_by'],
            'notes' => $validated['notes'] ?? $letterRequest->notes,
            'verified_at' => now(),
        ]);

        LetterRequestStatusHistory::create([
            'letter_request_id' => $letterRequest->letter_request_id,
            'status' => $validated['status'],
            'note' => $validated['notes'] ?? null,
            'change_by' => $validated['verified_by'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Verifikasi permohonan surat berhasil disimpan.',
            'data' => $letterRequest,
        ]);
    }

    // ===== Halaman Otorisasi Surat (LetterAuthorizationView.vue) =====
    // Pilih penandatangan + jenis TTD, keputusan Diverifikasi (masih menunggu) / Disetujui
    public function authorize(Request $request, int $letterRequest_id): JsonResponse
    {
        $letterRequest = LetterRequest::findOrFail($letterRequest_id);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['verified', 'authorized'])],
            'authorized_by_signer_id' => ['required_if:status,authorized', 'nullable', 'exists:signers,signer_id'],
            'signature_type' => ['required_if:status,authorized', 'nullable', Rule::in(['digital', 'manual'])],
        ]);

        $updateData = [
            'status' => $validated['status'],
        ];

        if ($validated['status'] === 'authorized') {
            $updateData['authorized_by_signer_id'] = $validated['authorized_by_signer_id'];
            $updateData['signature_type'] = $validated['signature_type'];
            $updateData['authorized_at'] = now();
            $updateData['letter_number'] = $this->generateLetterNumber($letterRequest->letterType);
        }

        $letterRequest->update($updateData);

        LetterRequestStatusHistory::create([
            'letter_request_id' => $letterRequest->letter_request_id,
            'status' => $validated['status'],
            'note' => null,
            'change_by' => $request->user()->user_id ?? null,
        ]);

        $letterRequest->load(['authorizedSigner', 'letterType']);

        return response()->json([
            'success' => true,
            'message' => 'Otorisasi permohonan surat berhasil disimpan.',
            'data' => $letterRequest,
        ]);
    }

    private function generateLetterNumber(LetterType $letterType): string
    {
        $prefix = $letterType->number_prefix ?? '';
        $countSamePrefix = LetterRequest::where('letter_number', 'like', "{$prefix}%")->count();
        $sequence = str_pad($countSamePrefix + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $sequence;
    }

    // ===== Riwayat status =====
    public function statusHistories(int $letterRequest_id): JsonResponse
    {
        $histories = LetterRequestStatusHistory::where('letter_request_id', $letterRequest_id)
            ->latest('changed_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat status surat berhasil diambil.',
            'data' => $histories,
        ]);
    }

    // ===== Lampiran dokumen =====
    public function storeAttachment(Request $request, int $letterRequest_id): JsonResponse
    {
        $validated = $request->validate([
            'letter_type_document_id' => ['required', 'exists:letter_type_documents,letter_type_document_id'],
            'file_name' => ['required', 'string', 'max:225'],
            'file_path' => ['required', 'string', 'max:225'],
        ]);
        $validated['letter_request_id'] = $letterRequest_id;

        $attachment = LetterRequestAttachment::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lampiran berhasil diunggah.',
            'data' => $attachment,
        ], 201);
    }

    public function attachments(int $letterRequest_id): JsonResponse
    {
        $attachments = LetterRequestAttachment::where('letter_request_id', $letterRequest_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Data lampiran berhasil diambil.',
            'data' => $attachments,
        ]);
    }
}
