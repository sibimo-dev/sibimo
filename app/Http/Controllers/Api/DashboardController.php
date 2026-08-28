<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Complaint;
use App\Models\LetterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        [$periodStart, $periodEnd, $previousStart, $previousEnd] = $this->periodRange(
            $request->string('period', 'this_month')->toString(),
        );

        $currentRequests = LetterRequest::query()
            ->whereBetween('submitted_at', [$periodStart, $periodEnd]);
        $previousRequests = LetterRequest::query()
            ->whereBetween('submitted_at', [$previousStart, $previousEnd]);

        $stats = [
            $this->stat(
                'Pengajuan Masuk',
                (clone $currentRequests)->count(),
                (clone $previousRequests)->count(),
                'Surat',
                'pi pi-inbox',
                'bg-primary-500',
            ),
            $this->stat(
                'Menunggu Verifikasi',
                (clone $currentRequests)->whereIn('status', ['Pending', 'submitted'])->count(),
                (clone $previousRequests)->whereIn('status', ['Pending', 'submitted'])->count(),
                'Surat',
                'pi pi-clock',
                'bg-amber-500',
            ),
            $this->stat(
                'Terverifikasi',
                (clone $currentRequests)->whereIn('status', ['Diverifikasi', 'verified', 'Terverifikasi'])->count(),
                (clone $previousRequests)->whereIn('status', ['Diverifikasi', 'verified', 'Terverifikasi'])->count(),
                'Surat',
                'pi pi-check-circle',
                'bg-success-600',
            ),
            $this->stat(
                'Tercetak',
                (clone $currentRequests)->whereIn('status', ['Disetujui', 'authorized', 'Tercetak'])->count(),
                (clone $previousRequests)->whereIn('status', ['Disetujui', 'authorized', 'Tercetak'])->count(),
                'Surat',
                'pi pi-print',
                'bg-secondary-600',
            ),
        ];

        $submissions = LetterRequest::query()
            ->with('letterType')
            ->latest('submitted_at')
            ->take(10)
            ->get()
            ->map(fn (LetterRequest $request) => [
                'id' => $request->letter_request_id,
                'requestId' => $request->request_code,
                'citizenId' => $request->applicant_nik,
                'purpose' => $request->letterType?->letter_name ?? '-',
                'status' => $this->letterStatusLabel($request->status),
                'date' => $request->submitted_at?->toISOString(),
            ])
            ->values();

        $complaints = Complaint::query()
            ->with('citizen')
            ->latest('submitted_at')
            ->take(3)
            ->get()
            ->map(fn (Complaint $complaint) => [
                'id' => $complaint->complaint_id,
                'title' => $complaint->title,
                'reporter' => $complaint->citizen?->full_name ?? '-',
                'time' => $complaint->submitted_at?->toISOString(),
                'status' => $this->complaintStatusLabel($complaint->status),
            ])
            ->values();

        $upcomingAgenda = Agenda::query()
            ->whereDate('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->take(3)
            ->get()
            ->map(fn (Agenda $agenda) => [
                'id' => $agenda->agenda_id,
                'name' => $agenda->title,
                'date' => $agenda->event_date?->toDateString(),
                'startTime' => $agenda->start_time,
                'endTime' => $agenda->end_time,
                'location' => $agenda->location ?? '-',
            ])
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'submissions' => $submissions,
                'complaints' => $complaints,
                'upcomingAgenda' => $upcomingAgenda,
            ],
        ]);
    }

    private function stat(
        string $label,
        int $value,
        int $previousValue,
        string $unit,
        string $icon,
        string $bg,
    ): array {
        $delta = $value - $previousValue;

        return [
            'label' => $label,
            'value' => $value,
            'delta' => ($delta >= 0 ? '+' : '') . $delta . ' ' . $unit,
            'up' => $delta >= 0,
            'icon' => $icon,
            'bg' => $bg,
        ];
    }

    private function periodRange(string $period): array
    {
        $end = now();

        return match ($period) {
            'last_3_months' => [
                now()->subMonths(2)->startOfMonth(),
                $end,
                now()->subMonths(5)->startOfMonth(),
                now()->subMonths(3)->endOfMonth(),
            ],
            'this_year' => [
                now()->startOfYear(),
                $end,
                now()->subYear()->startOfYear(),
                now()->subYear()->endOfYear(),
            ],
            default => [
                now()->startOfMonth(),
                $end,
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ],
        };
    }

    private function letterStatusLabel(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'pending', 'submitted' => 'Menunggu',
            'diverifikasi', 'verified', 'terverifikasi' => 'Terverifikasi',
            'ditolak', 'rejected' => 'Ditolak',
            'disetujui', 'authorized', 'tercetak' => 'Terverifikasi',
            default => 'Terverifikasi',
        };
    }

    private function complaintStatusLabel(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'submitted', 'baru' => 'Baru',
            'in progress', 'diproses' => 'Diproses',
            'resolved', 'selesai' => 'Selesai',
            default => 'Ditolak',
        };
    }
}
