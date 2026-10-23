<?php

namespace App\Http\Controllers\Api;

use App\Events\NotificationAvailable;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    public function index(): JsonResponse
    {
        // Daftar hanya membutuhkan data ringkas. Lampiran dan riwayat status
        // diambil oleh endpoint detail masing-masing agar tabel tidak memuat
        // payload relasi besar untuk setiap baris.
        $complaints = Complaint::query()
            ->select([
                'complaint_id',
                'reporter_name',
                'reporter_phone',
                'category',
                'title',
                'description',
                'location',
                'latitude',
                'longitude',
                'status',
                'submitted_at',
                'resolved_at',
            ])
            ->latest('submitted_at')
            ->get();

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
        $complaint = Complaint::query()
        ->with(['attachments', 'resolutionAttachments', 'statusHistories'])
        ->findOrFail($complaint_id);
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
            ->select(['complaint_id', 'reporter_name', 'reporter_phone', 'category', 'title', 'description', 'location', 'latitude', 'longitude', 'status', 'resolution_description', 'submitted_at', 'resolved_at'])
            ->with(['attachments', 'resolutionAttachments'])
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
            ->select(['complaint_id', 'reporter_name', 'reporter_phone', 'category', 'title', 'description', 'location', 'latitude', 'longitude', 'status', 'resolution_description', 'submitted_at', 'resolved_at'])
            ->with(['attachments', 'resolutionAttachments', 'statusHistories'])
            ->findOrFail($complaint_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail pengaduan berhasil diperbarui.',
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
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $validated['status'] = 'Submitted';
        $validated['submitted_at'] = now();

        $complaint = Complaint::create($validated);

        event(new NotificationAvailable(
            'complaint',
            (int) $complaint->complaint_id,
            'created',
            now()->toISOString(),
        ));

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
        $newStatus = $request->input('status');

        $needsProof  = $newStatus === 'Resolved' && $complaint->status !== 'Resolved';
        $needsReason = $newStatus === 'Rejected' && $complaint->status !== 'Rejected';

        $validated = $request->validate([
            'status' => ['required', Rule::in(['Submitted', 'In Progress', 'Resolved', 'Rejected'])],
            'note' => ['nullable', 'string'],
            'resolution_description' => [Rule::requiredIf($needsProof || $needsReason), 'nullable', 'string', 'max:300'],
            'resolution_files' => [Rule::requiredIf($needsProof), 'nullable', 'array', 'max:20'],
            'resolution_files.*' => ['file', 'image', 'max:5120'],
        ]);

        $isResolved = $validated['status'] === 'Resolved';

        $history = DB::transaction(function () use ($request, $complaint, $validated, $isResolved) {
            $data = [
                'status' => $validated['status'],
                'resolved_at' => $isResolved ? ($complaint->resolved_at ?? now()) : null,
            ];
            if (in_array($validated['status'], ['Resolved', 'Rejected']) && !empty($validated['resolution_description'])) {
                $data['resolution_description'] = trim($validated['resolution_description']);
            }
            $complaint->update($data);

            if ($isResolved) {
                foreach ($request->file('resolution_files', []) as $file) {
                    $path = $this->storeCompressedImage($file, 'complaints/resolutions');
                    ComplaintAttachment::create([
                        'complaint_id' => $complaint->complaint_id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => Storage::disk('public')->url($path),
                        'type' => 'resolution',
                    ]);
                }
            }

            return ComplaintStatusHistory::create([
                'complaint_id' => $complaint->complaint_id,
                'status' => $validated['status'],
                'note' => $validated['note'] ?? $validated['resolution_description'] ?? null,
                'user_id' => $request->user()->user_id,
            ]);
        });

        event(new NotificationAvailable('complaint', (int) $complaint->complaint_id, 'updated', now()->toISOString()));

        return response()->json([
            'success' => true,
            'message' => 'Status pengaduan berhasil diperbarui.',
            'data' => [
                'complaint' => $complaint->load('attachments', 'resolutionAttachments'),
                'history' => $history,
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
    private function storeCompressedImage(\Illuminate\Http\UploadedFile $file, string $dir): string
    {
        $real = $file->getRealPath();
        $info = @getimagesize($real);
        $type = $info[2] ?? null;

        $src = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($real),
            IMAGETYPE_PNG  => @imagecreatefrompng($real),
            IMAGETYPE_WEBP => @imagecreatefromwebp($real),
            IMAGETYPE_GIF  => @imagecreatefromgif($real),
            default        => false,
        };

        // Format tak dikenali GD: simpan apa adanya
        if (!$src) {
            return $file->store($dir, 'public');
        }

        // GD membuang data EXIF, jadi putar manual agar foto HP tidak miring
        if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $orientation = @exif_read_data($real)['Orientation'] ?? 1;
            $src = match ($orientation) {
                3 => imagerotate($src, 180, 0),
                6 => imagerotate($src, -90, 0),
                8 => imagerotate($src, 90, 0),
                default => $src,
            };
        }

        // Perkecil maksimal 1600px (tidak membesarkan gambar kecil)
        $w = imagesx($src);
        $h = imagesy($src);
        $scale = min(1, 1600 / max($w, $h));
        if ($scale < 1) {
            $src = imagescale($src, (int) round($w * $scale), (int) round($h * $scale));
        }

        imagepalettetotruecolor($src);
        imagesavealpha($src, true);

        $useWebp = function_exists('imagewebp');
        $path = trim($dir, '/') . '/' . Str::uuid() . ($useWebp ? '.webp' : '.jpg');

        ob_start();
        $useWebp ? imagewebp($src, null, 80) : imagejpeg($src, null, 82);
        $data = ob_get_clean();
        imagedestroy($src);

        Storage::disk('public')->put($path, $data);

        return $path;
    }
}
