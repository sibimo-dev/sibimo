<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrganizationalStructureController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data struktur organisasi berhasil diambil.',
            'data' => [$this->structurePayload()],
        ]);
    }

    public function show(int $organizational_structure_id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail struktur organisasi berhasil diambil.',
            'data' => $this->structurePayload(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $this->validated($request);
        $this->replaceStaff($request, $payload['levels']);

        return response()->json([
            'success' => true,
            'message' => 'Struktur organisasi berhasil dibuat.',
            'data' => $this->structurePayload($payload['title'], $payload['status']),
        ], 201);
    }

    public function update(Request $request, int $organizational_structure_id): JsonResponse
    {
        $payload = $this->validated($request);
        $this->replaceStaff($request, $payload['levels']);

        return response()->json([
            'success' => true,
            'message' => 'Struktur organisasi berhasil diperbarui.',
            'data' => $this->structurePayload($payload['title'], $payload['status']),
        ]);
    }

    public function destroy(int $organizational_structure_id): JsonResponse
    {
        $this->deleteStaffPhotos();
        Staff::query()->where('is_signer', false)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Struktur organisasi berhasil dihapus.',
        ]);
    }

    private function validated(Request $request): array
    {
        $this->decodeLevels($request);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'levels' => ['required', 'array'],
            'status' => ['nullable', Rule::in(['Draft', 'Published'])],
            'published_at' => ['nullable', 'date'],
            'photos.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);
        Validator::make($validated['levels'], [
            '*.level' => ['required', 'string'],
            '*.pimpinan' => ['nullable', 'boolean'],
            '*.slider' => ['nullable', 'boolean'],
            '*.centerOnDesktop' => ['nullable', 'boolean'],
            '*.people' => ['required', 'array'],
            '*.people.*.name' => ['required', 'string'],
            '*.people.*.title' => ['required', 'string'],
            '*.people.*.desc' => ['nullable', 'string'],
            '*.people.*.photo' => ['nullable', 'string'],
        ])->validate();

        return $validated;
    }

    private function replaceStaff(Request $request, array $levels): void
    {
        DB::transaction(function () use ($request, $levels): void {
            $oldPhotos = $this->collectPhotoUrls(Staff::query()->where('is_signer', false)->pluck('photo')->all());
            $files = $request->allFiles()['photos'] ?? [];
            $newPhotos = [];
            $rows = [];

            foreach ($levels as $level) {
                foreach ($level['people'] as $person) {
                    $photo = $this->replacePhotoToken($person['photo'] ?? null, $files);
                    if ($photo) $newPhotos[] = $photo;
                    $rows[] = [
                        'name' => $person['name'],
                        'position' => $person['title'],
                        'level' => $level['level'],
                        'description' => $person['desc'] ?? null,
                        'photo' => $photo,
                        'is_signer' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            Staff::query()->where('is_signer', false)->delete();
            if ($rows) Staff::query()->insert($rows);
            $this->deletePhotos(array_values(array_diff($oldPhotos, $newPhotos)));
        });
    }

    private function structurePayload(?string $title = null, ?string $status = null): array
    {
        $staff = Staff::query()->where('is_signer', false)->orderBy('staff_id')->get();
        $levels = $staff->groupBy('level')->map(function ($people, $level) {
            return [
                'level' => $level,
                'pimpinan' => $level === 'Lurah',
                'slider' => str_contains($level, 'Dukuh') || str_contains($level, 'Staff'),
                'centerOnDesktop' => $level === 'Staff Pamong Kalurahan',
                'people' => $people->map(fn (Staff $person) => [
                    'name' => $person->name,
                    'title' => $person->position,
                    'desc' => $person->description,
                    'photo' => $person->photo,
                ])->values()->all(),
            ];
        })->values()->all();

        return [
            'organizational_structure_id' => 1,
            'title' => $title ?? 'Struktur Organisasi Pemerintah Kalurahan',
            'levels' => $levels,
            'status' => $status ?? 'Draft',
            'published_at' => null,
        ];
    }

    private function decodeLevels(Request $request): void
    {
        $levels = $request->input('levels');
        if (!is_string($levels)) return;
        $decoded = json_decode($levels, true);
        if (!is_array($decoded)) {
            throw ValidationException::withMessages(['levels' => 'levels harus berupa JSON array yang valid.']);
        }
        $request->merge(['levels' => $decoded]);
    }

    private function replacePhotoToken(?string $photo, array $files): ?string
    {
        if (!$photo || !str_starts_with($photo, 'upload:')) return $photo;
        $token = substr($photo, 7);
        $file = $files[$token] ?? null;
        if (!$file) throw ValidationException::withMessages(['photos' => "File foto untuk token {$token} tidak ditemukan."]);
        return Storage::disk('public')->url($file->store('profile/organization', 'public'));
    }

    private function collectPhotoUrls(array $photos): array
    {
        return array_values(array_filter($photos, fn ($photo) => is_string($photo) && str_contains($photo, '/storage/profile/organization/')));
    }

    private function deleteStaffPhotos(): void
    {
        $this->deletePhotos($this->collectPhotoUrls(Staff::query()->where('is_signer', false)->pluck('photo')->all()));
    }

    private function deletePhotos(array $photos): void
    {
        foreach ($photos as $url) {
            Storage::disk('public')->delete(str_replace('/storage/', '', (string) parse_url($url, PHP_URL_PATH)));
        }
    }
}
