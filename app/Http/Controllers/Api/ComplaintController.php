<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintStatusHistory;

class ComplaintController extends Controller
{
    public function index(): JsonResponse
    {
        $complaints = Complaint::query()->with(['citizen', 'attachments', 'statusHistories'])->latest('submitted_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data pengaduan berhasil diambil.',
            'data' => $complaints,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'citizen_id' => ['required', 'exists:citizens,citizen_id'],
            'category' => ['required', Rule::in(['Infrastructure', 'Public Service', 'Environment', 'Security', 'Other'])],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string']
        ]);
    
        $validated['submitted_at'] = now();
    
        $complaint = Complaint::create($validated);
    
        return response()->json([
            'success' => true,
            'message' => 'Pengaduan berhasil dibuat.',
            'data' => $complaint,
        ], 201);
    }

    public function show(int $complaint_id): JsonResponse
    {
        $complaint = Complaint::query()->with(['citizen', 'attachments', 'statusHistories'])->findOrFail($complaint_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail pengaduan berhasil diambil.',
            'data' => $complaint,
        ]);
    }

    public function update(Request $request, int $complaint_id): JsonResponse
    {
        $complaint = Complaint::findOrFail($complaint_id);

        $validated = $request->validate([
            'category' => ['sometimes','required', Rule::in(['Infrastructure','Public Service','Environment','Security','Other'])],
            'title' => ['sometimes','required','string','max:200'],
            'description' => ['sometimes','required','string'],
            'resolved_at' => ['nullable','date']
        ]);

        $complaint->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan berhasil diperbarui.',
            'data' => $complaint,
        ]);
    }

    public function destroy(int $complaint_id): JsonResponse
    {
        $complaint = Complaint::findOrFail($complaint_id);
        $complaint->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan berhasil dihapus.',
        ]);
    }

    public function updateStatus(Request $request, int $complaint_id): JsonResponse
    {
        $complaint = Complaint::findOrFail($complaint_id);
    
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Submitted', 'In Progress', 'Resolved', 'Rejected'])],
            'note' => ['nullable', 'string'],
        ]);
    
        $complaint->update([
            'status' => $validated['status']
        ]);
    
        $history = ComplaintStatusHistory::create([
            'complaint_id' => $complaint->complaint_id,
            'status' => $validated['status'],
            'note' => $validated['note'] ?? null,
            'user_id' => $request->user()->user_id,
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Status pengaduan berhasil diperbarui.',
            'data' => [
                'complaint' => $complaint,
                'history' => $history
            ],
        ]);
    }

    public function statusHistories(int $complaint_id): JsonResponse
    {
        $histories = ComplaintStatusHistory::where('complaint_id', $complaint_id)->latest('changed_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat status pengaduan berhasil diambil.',
            'data' => $histories,
        ]);
    }

    public function storeAttachment(Request $request, int $complaint_id): JsonResponse
    {
        $validated = $request->validate([
            'file_name' => ['required', 'string', 'max:255'],
            'file_path' => ['required', 'string', 'max:255'],
        ]);
        $validated['complaint_id'] = $complaint_id;

        $attachment = ComplaintAttachment::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lampiran pengaduan berhasil diunggah.',
            'data' => $attachment,
        ], 201);
    }

    public function attachments(int $complaint_id): JsonResponse
    {
        $attachments = ComplaintAttachment::where('complaint_id', $complaint_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Data lampiran pengaduan berhasil diambil.',
            'data' => $attachments,
        ]);
    }
}
