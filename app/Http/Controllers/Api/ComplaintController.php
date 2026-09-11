<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintStatusHistory;

class ComplaintController extends Controller
{
    public function index(): JsonResponse
    {
        $complaints = Complaint::query()->with(['attachments', 'statusHistories'])->latest('submitted_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data pengaduan berhasil diambil.',
            'data' => $complaints,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reporter_name' => ['nullable', 'string', 'max:100'],
            'reporter_phone' => ['nullable', 'string', 'max:20'],
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
        $complaint = Complaint::query()->with(['attachments', 'statusHistories'])->findOrFail($complaint_id);

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
            'reporter_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'reporter_phone' => ['sometimes', 'nullable', 'string', 'max:20'],
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

    public function indexPublic(): JsonResponse
    {
        $complaints = Complaint::query()
            ->select(['complaint_id', 'reporter_name', 'reporter_phone', 'category', 'title', 'description', 'location', 'latitude', 'longitude', 'status', 'submitted_at', 'resolved_at'])
            ->with('attachments')
            ->latest('submitted_at')
            ->get();
    
        return response()->json([
            'success' => true,
            'message' => 'Data pengaduan berhasil diambil.',
            'data' => $complaints,
        ]);
    }
    
    public function showPublic(int $complaint_id): JsonResponse
    {
        $complaint = Complaint::query()
            ->select(['complaint_id', 'reporter_name', 'reporter_phone', 'category', 'title', 'description', 'location', 'status', 'submitted_at', 'resolved_at'])
            ->with(['attachments', 'statusHistories'])
            ->findOrFail($complaint_id);
    
        return response()->json([
            'success' => true,
            'message' => 'Detail pengaduan berhasil diambil.',
            'data' => $complaint,
        ]);
    }

    public function storePublic(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reporter_name' => ['nullable', 'string', 'max:100'],
            'reporter_phone' => ['nullable', 'string', 'max:20'],
            'category' => ['required', Rule::in(['Infrastructure', 'Public Service', 'Environment', 'Security', 'Other'])],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $validated['status'] = 'Submitted';
        $validated['submitted_at'] = now();

        $complaint = Complaint::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan berhasil dikirim.',
            'data' => $complaint,
        ], 201);
    }

    public function storeAttachmentPublic(Request $request, int $complaint_id): JsonResponse
    {
        Complaint::findOrFail($complaint_id);
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,mp4', 'max:10240'],
        ]);
        $file = $validated['file'];
        $path = $file->store('complaints', 'public');

        $attachment = ComplaintAttachment::create([
            'complaint_id' => $complaint_id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => Storage::disk('public')->url($path),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lampiran berhasil diunggah.',
            'data' => $attachment,
        ], 201);
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
        Complaint::findOrFail($complaint_id);
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ]);
        $file = $validated['file'];
        $path = $file->store('complaints', 'public');

        $attachment = ComplaintAttachment::create([
            'complaint_id' => $complaint_id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => Storage::disk('public')->url($path),
        ]);

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
