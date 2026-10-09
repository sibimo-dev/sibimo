<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Development;
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
            'data' => $this->present($development),
        ]);
    }

    public function destroy(int $development_id): JsonResponse
    {
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