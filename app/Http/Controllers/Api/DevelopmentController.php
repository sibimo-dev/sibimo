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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DevelopmentController extends Controller
{
    private const CATEGORIES = ['infrastruktur', 'kesehatan', 'pendidikan', 'lingkungan'];
    private const STATUSES   = ['perencanaan', 'sedang_berjalan', 'selesai'];
    private const PROGRESS   = [0, 50, 100];

    public function index(): JsonResponse
    {
        $developments = Development::query()
            ->with('creator:user_id,full_name')
            ->latest()
            ->get()
            ->map(fn (Development $d) => $this->present($d));

        return response()->json([
            'success' => true,
            'message' => 'Data pembangunan berhasil diambil.',
            'data' => $developments,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());

        $data = collect($validated)->except(['cover_image', 'progress_photos'])->all();
        $data['slug'] = Str::slug($validated['name']) . '-' . Str::lower(Str::random(5));
        $data['volume_unit'] = $data['volume_unit'] ?? 'meter';
        $data['created_by'] = $request->user()->user_id;

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('developments/main', 'public');
        }

        foreach (self::PROGRESS as $pct) {
            if ($file = $request->file("progress_photos.$pct")) {
                $data["photo_$pct"] = $file->store('developments/progress', 'public');
            }
        }

        $development = Development::create($data);
        $development->load('creator:user_id,full_name');

        return response()->json([
            'success' => true,
            'message' => 'Data pembangunan berhasil dibuat.',
            'data' => $this->present($development),
        ], 201);
    }

    public function show(int $development_id): JsonResponse
    {
        $development = Development::query()
            ->with('creator:user_id,full_name')
            ->findOrFail($development_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail pembangunan berhasil diambil.',
            'data' => $this->present($development),
        ]);
    }

    public function update(Request $request, int $development_id): JsonResponse
    {
        $development = $this->save(
            $request,
            Development::query()->findOrFail($development_id),
        );
        $development = Development::findOrFail($development_id);

        $validated = $request->validate($this->rules(partial: true));

        $data = collect($validated)->except(['cover_image', 'progress_photos'])->all();

        if ($request->hasFile('cover_image')) {
            $this->deleteFile($development->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('developments/main', 'public');
        }

        foreach (self::PROGRESS as $pct) {
            if ($file = $request->file("progress_photos.$pct")) {
                $this->deleteFile($development->{"photo_$pct"});
                $data["photo_$pct"] = $file->store('developments/progress', 'public');
            }
        }

        $development->update($data);
        $development->load('creator:user_id,full_name');

        return response()->json([
            'success' => true,
            'message' => 'Data pembangunan berhasil diperbarui.',
            'data' => $development,
            'data' => $this->present($development),
        ]);
    }

    public function destroy(int $development_id): JsonResponse
    {
        $development = Development::query()->with('progress')->findOrFail($development_id);
        $this->deleteMedia($development->cover_image);
        foreach ($development->progress as $progress) {
            $this->deleteMedia($progress->image);
        }
        $development = Development::findOrFail($development_id);

        foreach (['cover_image', 'photo_0', 'photo_50', 'photo_100'] as $column) {
            $this->deleteFile($development->$column);
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
    private function rules(bool $partial = false): array
    {
        $required = fn (array $rules) => $partial ? array_merge(['sometimes'], $rules) : $rules;

        return [
            'name'           => $required(['required', 'string', 'max:255']),
            'category'       => $required(['required', Rule::in(self::CATEGORIES)]),
            'status'         => $required(['required', Rule::in(self::STATUSES)]),
            'address'        => ['nullable', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'budget'         => ['nullable', 'numeric', 'min:0'],
            'volume'         => ['nullable', 'numeric', 'min:0'],
            'volume_unit'    => ['nullable', 'string', 'max:20'],
            'funding_source' => ['nullable', 'string', 'max:100'],
            'executor'       => ['nullable', 'string', 'max:100'],
            'year'           => $required(['required', 'integer', 'digits:4']),
            'start_date'     => $required(['required', 'date']),
            'target_date'    => ['nullable', 'date', 'after_or_equal:start_date'],
            'latitude'       => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'      => ['nullable', 'numeric', 'between:-180,180'],
            'end_latitude'   => ['nullable', 'numeric', 'between:-90,90'],
            'end_longitude'  => ['nullable', 'numeric', 'between:-180,180'],
            'cover_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'progress_photos'     => ['nullable', 'array'],
            'progress_photos.0'   => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'progress_photos.50'  => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'progress_photos.100' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function url(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    private function present(Development $d): array
    {
        // Bentuk yang dibaca frontend: progress = [{ progress_id, percentage, image }]
        $progress = collect(self::PROGRESS)
            ->filter(fn ($pct) => $d->{"photo_$pct"})
            ->map(fn ($pct) => [
                'progress_id' => $pct,
                'percentage'  => $pct,
                'image'       => $this->url($d->{"photo_$pct"}),
            ])
            ->values()
            ->all();

        return [
            'development_id' => $d->development_id,
            'name'           => $d->name,
            'slug'           => $d->slug,
            'category'       => $d->category,
            'status'         => $d->status,
            'address'        => $d->address,
            'description'    => $d->description,
            'budget'         => $d->budget,
            'volume'         => $d->volume,
            'volume_unit'    => $d->volume_unit,
            'funding_source' => $d->funding_source,
            'executor'       => $d->executor,
            'year'           => $d->year,
            'start_date'     => $d->start_date?->toDateString(),
            'target_date'    => $d->target_date?->toDateString(),
            'latitude'       => $d->latitude,
            'longitude'      => $d->longitude,
            'end_latitude'   => $d->end_latitude,
            'end_longitude'  => $d->end_longitude,
            'cover_image'    => $this->url($d->cover_image),
            'progress'       => $progress,
            'creator'        => $d->creator,
            'created_at'     => $d->created_at,
        ];
    }
}
