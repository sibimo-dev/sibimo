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
    private const CATEGORIES = ['infrastruktur', 'kesehatan', 'pendidikan', 'lingkungan', 'sosial'];
    private const STATUSES = ['perencanaan', 'sedang-berjalan', 'sedang_berjalan', 'selesai'];
    private const PROGRESS = [0, 50, 100];

    public function index(): JsonResponse
    {
        $items = Development::query()->latest('created_at')->get()->map(fn (Development $item) => $this->present($item));

        return response()->json(['success' => true, 'message' => 'Data pembangunan berhasil diambil.', 'data' => $items]);
    }

    public function show(int $development_id): JsonResponse
    {
        $item = Development::query()->findOrFail($development_id);
        return response()->json(['success' => true, 'message' => 'Detail pembangunan berhasil diambil.', 'data' => $this->present($item)]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $item = $this->persist($request, $validated, new Development());
        return response()->json(['success' => true, 'message' => 'Data pembangunan berhasil dibuat.', 'data' => $this->present($item)], 201);
    }

    public function update(Request $request, int $development_id): JsonResponse
    {
        $item = Development::query()->findOrFail($development_id);
        $validated = $request->validate($this->rules(true));
        $item = $this->persist($request, $validated, $item);
        return response()->json(['success' => true, 'message' => 'Data pembangunan berhasil diperbarui.', 'data' => $this->present($item)]);
    }

    public function destroy(int $development_id): JsonResponse
    {
        $item = Development::query()->findOrFail($development_id);
        foreach (['cover_image', 'photo_0', 'photo_50', 'photo_100'] as $field) $this->deleteFile($item->getRawOriginal($field));
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Data pembangunan berhasil dihapus.']);
    }

    private function rules(bool $partial = false): array
    {
        $required = fn (array $rules) => $partial ? array_merge(['sometimes'], $rules) : $rules;
        return [
            'name' => $required(['required', 'string', 'max:255']), 'address' => ['nullable', 'string', 'max:255'],
            'category' => $required(['required', Rule::in(self::CATEGORIES)]), 'status' => $required(['required', Rule::in(self::STATUSES)]),
            'funding_source' => ['nullable', 'string', 'max:100'], 'budget' => ['nullable', 'numeric', 'min:0'],
            'volume' => ['nullable', 'numeric', 'min:0'], 'volume_unit' => ['nullable', 'string', 'max:20'],
            'executor' => ['nullable', 'string', 'max:100'], 'year' => $required(['required', 'integer', 'between:1945,2100']),
            'start_date' => $required(['required', 'date']), 'target_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'], 'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'], 'end_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'end_longitude' => ['nullable', 'numeric', 'between:-180,180'], 'cover_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'progress_photos.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ];
    }

    private function persist(Request $request, array $validated, Development $item): Development
    {
        $data = collect($validated)->except(['cover_image', 'progress_photos'])->all();
        $data['status'] = str_replace('-', '_', $data['status'] ?? $item->status ?? 'perencanaan');
        $data['volume_unit'] = $data['volume_unit'] ?? 'meter';
        if (!$item->exists) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::lower(Str::random(5));
            $data['created_by'] = $request->user()?->user_id;
        } elseif (($data['name'] ?? null) && !$item->slug) $data['slug'] = Str::slug($data['name']) . '-' . Str::lower(Str::random(5));
        foreach (self::PROGRESS as $percentage) {
            $file = $request->file("progress_photos.$percentage");
            if (!$file) continue;
            $this->deleteFile($item->getRawOriginal("photo_$percentage"));
            $data["photo_$percentage"] = $file->store('developments/progress', 'public');
        }
        if ($request->hasFile('cover_image')) {
            $this->deleteFile($item->getRawOriginal('cover_image'));
            $data['cover_image'] = $request->file('cover_image')->store('developments/main', 'public');
        }
        $item->fill($data)->save();
        return $item->fresh();
    }

    private function present(Development $item): array
    {
        return [
            'development_id' => $item->development_id, 'name' => $item->name, 'slug' => $item->slug, 'address' => $item->address,
            'category' => $item->category, 'status' => str_replace('_', '-', (string) $item->status), 'funding_source' => $item->funding_source,
            'budget' => $item->budget, 'volume' => $item->volume, 'volume_unit' => $item->volume_unit, 'executor' => $item->executor,
            'year' => $item->year, 'start_date' => $item->start_date?->format('Y-m-d'), 'target_date' => $item->target_date?->format('Y-m-d'),
            'description' => $item->description, 'latitude' => $item->latitude, 'longitude' => $item->longitude,
            'end_latitude' => $item->end_latitude, 'end_longitude' => $item->end_longitude,
            'cover_image' => $this->mediaUrl($item->getRawOriginal('cover_image')),
            'progress' => collect(self::PROGRESS)->map(fn (int $percentage) => ['percentage' => $percentage, 'image' => $this->mediaUrl($item->getRawOriginal("photo_$percentage"))])->values()->all(),
            'created_at' => $item->created_at,
        ];
    }

    private function mediaUrl(?string $path): ?string
    {
        if (!$path) return null;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
        $url = Storage::disk('public')->url($path);
        return str_starts_with($url, '/') ? rtrim((string) config('app.url'), '/') . $url : $url;
    }

    private function deleteFile(?string $path): void
    {
        if (!$path || str_starts_with($path, 'http')) {
            if (!$path) return;
            $path = parse_url($path, PHP_URL_PATH);
        }
        Storage::disk('public')->delete(ltrim(str_replace('/storage/', '', (string) $path), '/'));
    }
}
