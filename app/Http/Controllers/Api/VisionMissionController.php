<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisionMission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VisionMissionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data visi dan misi berhasil diambil.',
            'data' => VisionMission::query()->with('publisher')->latest()->get(),
        ]);
    }

    public function show(int $vision_mission_id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail visi dan misi berhasil diambil.',
            'data' => VisionMission::query()->with('publisher')->findOrFail($vision_mission_id),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $visionMission = $this->save($request, new VisionMission());

        return response()->json([
            'success' => true,
            'message' => 'Visi dan misi berhasil dibuat.',
            'data' => $visionMission,
        ], 201);
    }

    public function update(Request $request, int $vision_mission_id): JsonResponse
    {
        $visionMission = $this->save(
            $request,
            VisionMission::query()->findOrFail($vision_mission_id),
        );

        return response()->json([
            'success' => true,
            'message' => 'Visi dan misi berhasil diperbarui.',
            'data' => $visionMission,
        ]);
    }

    public function destroy(int $vision_mission_id): JsonResponse
    {
        VisionMission::query()->findOrFail($vision_mission_id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Visi dan misi berhasil dihapus.',
        ]);
    }

    private function save(Request $request, VisionMission $visionMission): VisionMission
    {
        $this->decodeMissions($request);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'vision' => ['required', 'string'],
            'missions' => ['required', 'array', 'min:1'],
            'missions.*' => ['required', 'string'],
            'status' => ['nullable', Rule::in(['Draft', 'Published'])],
            'published_at' => ['nullable', 'date'],
        ]);

        $status = $validated['status'] ?? $visionMission->status ?? 'Draft';
        $visionMission->fill([
            'title' => $validated['title'],
            'vision' => $validated['vision'],
            'missions' => $validated['missions'],
            'status' => $status,
            'published_at' => $validated['published_at'] ?? $visionMission->published_at,
            'published_by' => $request->user()?->user_id ?? $visionMission->published_by,
        ]);
        if ($status === 'Published' && !$visionMission->published_at) {
            $visionMission->published_at = now();
        }
        $visionMission->save();

        return $visionMission->fresh('publisher');
    }

    private function decodeMissions(Request $request): void
    {
        $missions = $request->input('missions');
        if (!is_string($missions)) return;

        $decoded = json_decode($missions, true);
        if (!is_array($decoded)) {
            throw ValidationException::withMessages([
                'missions' => 'missions harus berupa JSON array yang valid.',
            ]);
        }
        $request->merge(['missions' => $decoded]);
    }
}
