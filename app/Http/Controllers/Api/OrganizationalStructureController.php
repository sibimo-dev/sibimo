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
            'signatures.*' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
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
            '*.people.*.signature' => ['nullable', 'string'],
        ])->validate();

        return $validated;
    }

    private function replaceStaff(Request $request, array $levels): void
    {
        DB::transaction(function () use ($request, $levels): void {
            $oldPhotos = $this->collectMediaUrls(Staff::query()->where('is_signer', false)->pluck('photo')->all(), 'profile/organization/');
            $oldSignatures = $this->collectMediaUrls(Staff::query()->where('is_signer', false)->pluck('signature_image')->all(), 'profile/signatures/');
            $files = $request->allFiles()['photos'] ?? [];
            $signatureFiles = $request->allFiles()['signatures'] ?? [];
            $newPhotos = [];
            $newSignatures = [];
            $rows = [];

            foreach ($levels as $level) {
                foreach ($level['people'] as $person) {
                    $photo = $this->replacePhotoToken($person['photo'] ?? null, $files);
                    $signature = $this->replaceSignatureToken($person['signature'] ?? null, $signatureFiles);
                    if ($photo) $newPhotos[] = $photo;
                    if ($signature) $newSignatures[] = $signature;
                    $rows[] = [
                        'name' => $person['name'],
                        'position' => $person['title'],
                        'level' => $level['level'],
                        'description' => $person['desc'] ?? null,
                        'photo' => $photo,
                        'signature_image' => $signature,
                        'is_signer' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            Staff::query()->where('is_signer', false)->delete();
            if ($rows) Staff::query()->insert($rows);
            foreach ($rows as $row) {
                Staff::query()
                    ->where('is_signer', true)
                    ->where('name', $row['name'])
                    ->where('position', $row['position'])
                    ->update(['signature_image' => $row['signature_image']]);
            }
            $this->deleteMedia(array_values(array_diff($oldPhotos, $newPhotos)));
            $this->deleteMedia(array_values(array_diff($oldSignatures, $newSignatures)));
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
                    'signature' => $person->signature_image,
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

    private function replaceSignatureToken(?string $signature, array $files): ?string
    {
        if (!$signature || !str_starts_with($signature, 'upload-signature:')) return $signature;
        $token = substr($signature, 17);
        $file = $files[$token] ?? null;
        if (!$file) throw ValidationException::withMessages(['signatures' => "File tanda tangan untuk token {$token} tidak ditemukan."]);
        return Storage::disk('public')->url($file->store('profile/signatures', 'public'));
    }

    private function collectMediaUrls(array $files, string $directory): array
    {
        return array_values(array_filter($files, fn ($file) => is_string($file) && str_contains($file, '/storage/' . trim($directory, '/') . '/')));
    }

    private function deleteStaffPhotos(): void
    {
        $this->deleteMedia($this->collectMediaUrls(Staff::query()->where('is_signer', false)->pluck('photo')->all(), 'profile/organization/'));
        $this->deleteMedia($this->collectMediaUrls(Staff::query()->where('is_signer', false)->pluck('signature_image')->all(), 'profile/signatures/'));
    }

    private function deleteMedia(array $photos): void
    {
        foreach ($photos as $url) {
            Storage::disk('public')->delete(str_replace('/storage/', '', (string) parse_url($url, PHP_URL_PATH)));
        }
    }
}
