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
use App\Models\OrganizationalStructureSetting;

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
        $this->saveSettings($payload);

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
        $this->decodeJsonField($request, 'levels');
        $this->decodeJsonField($request, 'former_officials');
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'levels' => ['required', 'array'],
            'status' => ['nullable', Rule::in(['Draft', 'Published'])],
            'published_at' => ['nullable', 'date'],
            'photos.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'signatures.*' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'former_officials' => ['nullable', 'array'],
            'former_officials.*.id' => ['nullable', 'string', 'max:64'],
            'former_officials.*.jabatan' => ['required', 'string', 'max:150'],
            'former_officials.*.nama' => ['required', 'string', 'max:150'],
            'former_officials.*.start_year' => ['required', 'integer', 'between:1900,2100'],
            'former_officials.*.end_year' => ['required', 'integer', 'between:1900,2100', 'gte:former_officials.*.start_year'],
            'former_officials.*.desc' => ['nullable', 'string', 'max:500'],
            'show_former_officials' => ['nullable', 'boolean'],
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
            '*.people.*.start_year' => ['nullable', 'integer', 'between:1900,2100'],
            '*.people.*.end_year' => ['nullable', 'integer', 'between:1900,2100'],
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
                        'description' => $person['desc'] ?? null,
                        'start_year' => $person['start_year'] ?? null,
                        'end_year' => $person['end_year'] ?? null,
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
        $settings = OrganizationalStructureSetting::query()->first();
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
                    'start_year' => $person->start_year,
                    'end_year' => $person->end_year,
                    'photo' => $person->photo,
                    'signature' => $person->signature_image,
                'former_officials' => $settings?->former_officials ?? [],
                'show_former_officials' => (bool) ($settings?->show_former_officials ?? false),
                ])->values()->all(),
            ];
        })->values()->all();

        return [
            'organizational_structure_id' => 1,
            'title' => $title ?? 'Struktur Organisasi Pemerintah Kalurahan',
            'levels' => $levels,
            'former_officials' => $settings?->former_officials ?? [],
            'show_former_officials' => (bool) ($settings?->show_former_officials ?? false),
            'status' => $status ?? 'Draft',
            'published_at' => null,
        ];
    }

    private function decodeJsonField(Request $request, string $field): void
    {
        $value = $request->input($field);
        if (!is_string($value)) return;
        $decoded = json_decode($value, true);
        if (!is_array($decoded)) {
            throw ValidationException::withMessages([$field => "{$field} harus berupa JSON array yang valid."]);
        }
        $request->merge([$field => $decoded]);
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

    private function saveSettings(array $payload): void
    {
        $settings = OrganizationalStructureSetting::query()->first() ?? new OrganizationalStructureSetting();

        if (array_key_exists('former_officials', $payload)) {
            $settings->former_officials = array_values($payload['former_officials'] ?? []);
        }
        if (array_key_exists('show_former_officials', $payload)) {
            $settings->show_former_officials = (bool) $payload['show_former_officials'];
        }

        $settings->save();
    }
}
