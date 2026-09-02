<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\History;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class HistoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data sejarah berhasil diambil.',
            'data' => History::query()->with('publisher')->latest()->get(),
        ]);
    }

    public function show(int $history_id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail sejarah berhasil diambil.',
            'data' => History::query()->with('publisher')->findOrFail($history_id),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $history = $this->save($request, new History());

        return response()->json([
            'success' => true,
            'message' => 'Sejarah berhasil dibuat.',
            'data' => $history,
        ], 201);
    }

    public function update(Request $request, int $history_id): JsonResponse
    {
        $history = $this->save($request, History::query()->findOrFail($history_id));

        return response()->json([
            'success' => true,
            'message' => 'Sejarah berhasil diperbarui.',
            'data' => $history,
        ]);
    }

    public function destroy(int $history_id): JsonResponse
    {
        $history = History::query()->findOrFail($history_id);
        $this->deletePhotos($history->photos ?? []);
        $history->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sejarah berhasil dihapus.',
        ]);
    }

    private function save(Request $request, History $history): History
    {
        $this->decodeJson($request, 'points');
        $this->decodeJson($request, 'photos_data');
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'year_founded' => ['required', 'integer', 'min:1000', 'max:9999'],
            'points' => ['required', 'array', 'min:1'],
            'points.*' => ['nullable', 'string'],
            'photos_data' => ['nullable', 'array'],
            'photos_data.*' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['Draft', 'Published'])],
            'published_at' => ['nullable', 'date'],
            'photos.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $oldPhotos = $history->photos ?? [];
        $photos = $this->storeNewPhotos(
            $validated['photos_data'] ?? [],
            $request->allFiles()['photos'] ?? [],
        );
        $status = $validated['status'] ?? $history->status ?? 'Draft';

        $history->fill([
            'title' => $validated['title'],
            'year_founded' => $validated['year_founded'],
            'points' => $validated['points'],
            'photos' => $photos,
            'status' => $status,
            'published_at' => $validated['published_at'] ?? $history->published_at,
            'published_by' => $request->user()?->user_id ?? $history->published_by,
        ]);
        if ($status === 'Published' && !$history->published_at) {
            $history->published_at = now();
        }
        $history->save();

        $this->deletePhotos(array_values(array_diff($oldPhotos, $photos)));

        return $history->fresh('publisher');
    }

    private function decodeJson(Request $request, string $field): void
    {
        $value = $request->input($field);
        if (!is_string($value)) return;

        $decoded = json_decode($value, true);
        if (!is_array($decoded)) {
            throw ValidationException::withMessages([$field => "{$field} harus berupa JSON array yang valid."]);
        }
        $request->merge([$field => $decoded]);
    }

    private function storeNewPhotos(array $paths, array $files): array
    {
        return array_map(function ($path) use ($files) {
            if (!is_string($path) || !str_starts_with($path, 'upload:')) return $path;

            $token = substr($path, strlen('upload:'));
            $file = $files[$token] ?? null;
            if (!$file) {
                throw ValidationException::withMessages([
                    'photos' => "File foto untuk token {$token} tidak ditemukan.",
                ]);
            }

            return Storage::disk('public')->url($file->store('profile/history', 'public'));
        }, $paths);
    }

    private function deletePhotos(array $photos): void
    {
        foreach ($photos as $url) {
            if (!is_string($url) || !str_contains($url, '/storage/profile/history/')) continue;
            $path = parse_url($url, PHP_URL_PATH);
            Storage::disk('public')->delete(str_replace('/storage/', '', (string) $path));
        }
    }
}
