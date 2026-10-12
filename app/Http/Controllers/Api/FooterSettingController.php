<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FooterSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FooterSettingController extends Controller
{
    public function showPublic(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Pengaturan footer berhasil diambil.',
            'data' => FooterSetting::query()->first(),
        ]);
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Pengaturan footer berhasil diambil.',
            'data' => FooterSetting::query()->first(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // Footer bersifat singleton; jangan membuat baris kedua jika data sudah ada.
        $setting = $this->save($request, FooterSetting::query()->first() ?? new FooterSetting());

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan footer berhasil dibuat.',
            'data' => $setting,
        ], 201);
    }

    public function update(Request $request, int $footer_setting_id): JsonResponse
    {
        $setting = $this->save(
            $request,
            FooterSetting::query()->findOrFail($footer_setting_id),
        );

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan footer berhasil diperbarui.',
            'data' => $setting,
        ]);
    }

    private function save(Request $request, FooterSetting $setting): FooterSetting
    {
        $this->decodeSocialMedia($request);

        $validated = $request->validate([
            'village_name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'social_media' => ['nullable', 'array'],
            'social_media.*.platform' => ['required', 'string', 'max:40'],
            'social_media.*.url' => ['required', 'url:http,https', 'max:500'],
            'address' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:5120'],
            'stamp' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:5120'],
        ]);

        $oldLogo = $setting->logo;
        $oldStamp = $setting->stamp;
        $attributes = collect($validated)->except(['logo', 'stamp'])->all();

        if ($request->hasFile('logo')) {
            $attributes['logo'] = Storage::disk('public')->url(
                $request->file('logo')->store('footer', 'public'),
            );
        }

        if ($request->hasFile('stamp')) {
            $attributes['stamp'] = Storage::disk('public')->url(
                $request->file('stamp')->store('footer', 'public'),
            );
        }

        $setting->fill($attributes)->save();

        if ($request->hasFile('logo')) {
            $this->deleteStoredFile($oldLogo);
        }
        if ($request->hasFile('stamp')) {
            $this->deleteStoredFile($oldStamp);
        }

        return $setting->fresh();
    }

    private function decodeSocialMedia(Request $request): void
    {
        $value = $request->input('social_media');
        if (!is_string($value)) {
            return;
        }

        $decoded = json_decode($value, true);
        if (!is_array($decoded)) {
            $request->merge(['social_media' => null]);
            return;
        }

        $request->merge(['social_media' => $decoded]);
    }

    private function deleteStoredFile(?string $url): void
    {
        if (!$url || !str_contains($url, '/storage/footer/')) {
            return;
        }

        $path = parse_url($url, PHP_URL_PATH);
        Storage::disk('public')->delete(str_replace('/storage/', '', (string) $path));
    }
}
