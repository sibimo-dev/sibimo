<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Development;
use App\Models\DevelopmentProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DevelopmentController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data pembangunan berhasil diambil.',
            'data' => Development::query()->with('progress')->latest()->get(),
        ]);
    }

    public function show(int $development_id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail pembangunan berhasil diambil.',
            'data' => Development::query()->with('progress')->findOrFail($development_id),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $development = $this->save($request, new Development());

        return response()->json([
            'success' => true,
            'message' => 'Data pembangunan berhasil dibuat.',
            'data' => $development,
        ], 201);
    }

    public function update(Request $request, int $development_id): JsonResponse
    {
        $development = $this->save(
            $request,
            Development::query()->findOrFail($development_id),
        );

        return response()->json([
            'success' => true,
            'message' => 'Data pembangunan berhasil diperbarui.',
            'data' => $development,
        ]);
    }

    public function destroy(int $development_id): JsonResponse
    {
        $development = Development::query()->with('progress')->findOrFail($development_id);
        $this->deleteMedia($development->cover_image);
        foreach ($development->progress as $progress) {
            $this->deleteMedia($progress->image);
        }
        $development->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pembangunan berhasil dihapus.',
        ]);
    }

    private function save(Request $request, Development $development): Development
    {
        $this->decodeProgress($request);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'address' => ['nullable', 'string', 'max:255'],
            'category' => ['required', Rule::in(['infrastruktur', 'kesehatan', 'pendidikan', 'lingkungan', 'sosial'])],
            'status' => ['required', Rule::in(['perencanaan', 'sedang-berjalan', 'selesai'])],
            'funding_source' => ['nullable', 'string', 'max:150'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'volume' => ['nullable', 'numeric', 'min:0'],
            'volume_unit' => ['nullable', 'string', 'max:40'],
            'executor' => ['nullable', 'string', 'max:150'],
            'year' => ['nullable', 'integer', 'min:1945', 'max:2100'],
            'start_date' => ['nullable', 'date'],
            'target_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'end_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'end_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'cover_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'progress' => ['sometimes', 'array'],
            'progress.*.progress_id' => ['nullable', 'integer'],
            'progress.*.percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'progress_photos.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $oldCover = $development->cover_image;
        $oldProgress = $development->exists
            ? $development->progress()->pluck('image')->filter()->all()
            : [];

        return DB::transaction(function () use ($request, $development, $validated, $oldCover, $oldProgress): Development {
            $attributes = collect($validated)->except(['cover_image', 'progress', 'progress_photos'])->all();
            if (!$development->exists) {
                $attributes['slug'] = Str::slug($validated['name']) . '-' . Str::lower(Str::random(5));
            } elseif (!$development->slug) {
                $attributes['slug'] = Str::slug($validated['name']) . '-' . Str::lower(Str::random(5));
            }

            if ($request->hasFile('cover_image')) {
                $attributes['cover_image'] = $this->mediaUrl(
                    $request->file('cover_image')->store('developments', 'public'),
                );
            }

            $development->fill($attributes)->save();

            $newProgressImages = [];
            if (array_key_exists('progress', $validated)) {
                $keptIds = [];
                $files = $request->allFiles()['progress_photos'] ?? [];
                foreach ($validated['progress'] ?? [] as $item) {
                    $percentage = (int) $item['percentage'];
                    $progress = null;
                    if (!empty($item['progress_id'])) {
                        $progress = DevelopmentProgress::query()
                            ->where('development_id', $development->development_id)
                            ->find($item['progress_id']);
                    }
                    $progress ??= DevelopmentProgress::query()
                        ->where('development_id', $development->development_id)
                        ->where('percentage', $percentage)
                        ->first();

                    $image = $progress?->image;
                    $file = $files[$percentage] ?? $files[(string) $percentage] ?? null;
                    if ($file) {
                        $image = $this->mediaUrl($file->store('development-progress', 'public'));
                    }

                    $progress ??= new DevelopmentProgress();
                    $progress->fill([
                        'development_id' => $development->development_id,
                        'percentage' => $percentage,
                        'image' => $image,
                    ])->save();
                    $keptIds[] = $progress->progress_id;
                    if ($image) $newProgressImages[] = $image;
                }

                $removed = DevelopmentProgress::query()
                    ->where('development_id', $development->development_id)
                    ->whereNotIn('progress_id', $keptIds)
                    ->get();
                foreach ($removed as $progress) {
                    $this->deleteMedia($progress->image);
                    $progress->delete();
                }
            }

            if ($request->hasFile('cover_image')) $this->deleteMedia($oldCover);
            foreach (array_diff($oldProgress, $newProgressImages) as $image) $this->deleteMedia($image);

            return $development->fresh('progress');
        });
    }

    private function decodeProgress(Request $request): void
    {
        $value = $request->input('progress');
        if (!is_string($value)) return;

        $decoded = json_decode($value, true);
        if (!is_array($decoded)) {
            throw ValidationException::withMessages(['progress' => 'progress harus berupa JSON array yang valid.']);
        }
        $request->merge(['progress' => $decoded]);
    }

    private function mediaUrl(string $path): string
    {
        $url = Storage::disk('public')->url($path);
        return str_starts_with($url, '/')
            ? rtrim((string) config('app.url'), '/') . $url
            : $url;
    }

    private function deleteMedia(?string $url): void
    {
        if (!$url || !str_contains($url, '/storage/develop')) return;
        $path = parse_url($url, PHP_URL_PATH);
        Storage::disk('public')->delete(str_replace('/storage/', '', (string) $path));
    }
}
