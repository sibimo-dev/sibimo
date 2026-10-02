<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\LetterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Return pending admin actions as a single notification feed.
     *
     * Notifications are derived from the current workflow status, so the
     * feed cannot become stale when a request or complaint is verified,
     * rejected, or resolved. The authenticated user's permissions are checked
     * per source so users cannot see unrelated items.
     */
    public function index(Request $request): JsonResponse
    {
        $permissions = $request->user()->effectivePermissionSlugs();
        $items = collect();

        if ($this->hasAnyPermission($permissions, ['pengelolaan-surat', 'verifikasi-surat'])) {
            $letters = LetterRequest::query()
                ->select([
                    'letter_request_id',
                    'letter_type_id',
                    'applicant_name',
                    'source',
                    'status',
                    'submitted_at',
                ])
                ->with('letterType:letter_type_id,letter_name')
                ->where('status', 'submitted')
                ->where(function ($query) {
                    $query->whereNull('source')
                        ->orWhere('source', 'not like', '%Manual%');
                })
                ->latest('submitted_at')
                ->limit(30)
                ->get()
                ->map(fn (LetterRequest $letter) => [
                    'key' => 'letter:' . $letter->letter_request_id,
                    'type' => 'letter',
                    'title' => $letter->letterType?->letter_name ?? 'Pengajuan surat baru',
                    'from' => trim((string) $letter->applicant_name) ?: 'Pemohon',
                    'time' => $letter->submitted_at?->toISOString(),
                    'to' => '/letter/verification/' . $letter->letter_request_id,
                ]);

            $items = $items->concat($letters);
        }

        if ($this->hasAnyPermission($permissions, ['pengaduan'])) {
            $complaints = Complaint::query()
                ->select(['complaint_id', 'title', 'reporter_name', 'status', 'submitted_at'])
                ->where('status', 'Submitted')
                ->latest('submitted_at')
                ->limit(30)
                ->get()
                ->map(fn (Complaint $complaint) => [
                    'key' => 'complaint:' . $complaint->complaint_id,
                    'type' => 'complaint',
                    'title' => trim((string) $complaint->title) ?: 'Aduan baru',
                    'from' => trim((string) $complaint->reporter_name) ?: 'Anonim',
                    'time' => $complaint->submitted_at?->toISOString(),
                    'to' => '/complaint/' . $complaint->complaint_id,
                ]);

            $items = $items->concat($complaints);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil diambil.',
            'data' => $items
                ->sortByDesc(fn (array $item) => $item['time'] ?? '')
                ->take(30)
                ->values(),
        ]);
    }

    private function hasAnyPermission(array $granted, array $required): bool
    {
        return count(array_intersect($granted, $required)) > 0;
    }
}
