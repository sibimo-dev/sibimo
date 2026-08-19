<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LetterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\LetterRequestAttachment;
use App\Models\LetterRequestStatusHistory;

class LetterRequestController extends Controller
{

    public function index(): JsonResponse
    {
        $letterRequests = LetterRequest::query()->with(['citizen', 'letterType', 'attachments', 'statusHistories'])->latest('submitted_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data permohonan surat berhasil diambil.',
            'data' => $letterRequests,
        ]);
    }


    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'citizen_id' => ['required','exists:citizens,citizen_id'],
            'letter_type_id' => ['required','exists:letter_types,letter_type_id'],
            'form_data' => ['nullable','array'],
            'signature_type' => ['nullable', Rule::in(['manual','digital'])],
            'remarks' => ['nullable','string']
        ]);
        
        $validated['status'] = 'Submitted';
        $validated['submitted_at'] = now();
        
        $letterRequest = LetterRequest::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Permohonan surat berhasil dibuat.',
            'data' => $letterRequest,
        ], 201);
    }


    public function show(int $letterRequest_id): JsonResponse
    {
        $letterRequest = LetterRequest::query()->with(['citizen', 'letterType', 'attachments', 'statusHistories'])->findOrFail($letterRequest_id);

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
            'status' => ['sometimes','required','string'],
            'form_data' => ['nullable','array'],
            'letter_number' => ['nullable','string','max:100'],
            'signature_type' => ['nullable', Rule::in(['manual','digital'])],
            'verified_by' => ['nullable','exists:users,user_id'],
            'authorized_at' => ['nullable','date'],
            'result_file_path' => ['nullable','string','max:100'],
            'remarks' => ['nullable','string']
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
        $letterRequest = LetterRequest::findOrFail($letterRequest_id);
        $letterRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permohonan surat berhasil dihapus.',
        ]);
    }

    public function updateStatus(Request $request, int $letterRequest_id): JsonResponse
    {
        $letterRequest = LetterRequest::findOrFail($letterRequest_id);

        $validated = $request->validate([
            'status' => ['required', 'string'],
            'note' => ['nullable', 'string'],
        ]);

        $letterRequest->update(['status' => $validated['status']]);

        $history = LetterRequestStatusHistory::create([
            'letter_request_id' => $letterRequest->letter_request_id,
            'status' => $validated['status'],
            'note' => $validated['note'] ?? null,
            'change_by' => $request->user()->user_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status surat berhasil diperbarui.',
            'data' => ['letter_request' => $letterRequest, 'history' => $history],
        ]);
    }

    public function statusHistories(int $letterRequest_id): JsonResponse
    {
        $histories = LetterRequestStatusHistory::where('letter_request_id', $letterRequest_id)->latest('changed_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat status surat berhasil diambil.',
            'data' => $histories,
        ]);
    }

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
