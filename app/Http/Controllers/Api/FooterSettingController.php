<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FooterSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Color;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Laravel\Facades\Image;

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
            'stamp' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_stamp' => ['nullable', 'boolean'],
        ]);

        $oldLogo = $setting->logo;
        $oldStamp = $setting->stamp;
        $removeLogo = $request->boolean('remove_logo') && !$request->hasFile('logo');
        $removeStamp = $request->boolean('remove_stamp') && !$request->hasFile('stamp');
        $attributes = collect($validated)->except(['logo', 'stamp', 'remove_logo', 'remove_stamp'])->all();

        if ($request->hasFile('logo')) {
            $attributes['logo'] = Storage::disk('public')->url(
                $request->file('logo')->store('footer', 'public'),
            );
        }

        if ($request->hasFile('stamp')) {
            $png = $this->normalizeStamp($request->file('stamp'));
            $path = 'footer/stamp_' . Str::uuid() . '.png';
            Storage::disk('public')->put($path, $png);
            $attributes['stamp'] = Storage::disk('public')->url($path);
        }

        if ($removeLogo) {
            $attributes['logo'] = null;
        }
        if ($removeStamp) {
            $attributes['stamp'] = null;
        }

        $setting->fill($attributes)->save();

        if ($request->hasFile('logo') || $removeLogo) {
            $this->deleteStoredFile($oldLogo);
        }
        if ($request->hasFile('stamp') || $removeStamp) {
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

    private function normalizeStamp(\Illuminate\Http\UploadedFile $file): string
    {
        try {
            $image = Image::decode($file)
                ->contain(600, 600, Color::transparent())
                ->encode(new PngEncoder());
            $bitmap = imagecreatefromstring((string) $image);

            if ($bitmap === false) {
                throw new \RuntimeException('Gambar tidak bisa dibaca GD.');
            }

            imagealphablending($bitmap, false);
            imagesavealpha($bitmap, true);

            $width = imagesx($bitmap);
            $height = imagesy($bitmap);

            for ($y = 0; $y < $height; $y++) {
                for ($x = 0; $x < $width; $x++) {
                    $rgba = imagecolorsforindex($bitmap, imagecolorat($bitmap, $x, $y));
                    $whiteLevel = min($rgba['red'], $rgba['green'], $rgba['blue']);

                    // Transparankan kertas putih/abu muda dan lunakkan tepi antialias.
                    $backgroundAlpha = $whiteLevel >= 242
                        ? 127
                        : ($whiteLevel > 215 ? (int) round(($whiteLevel - 215) * (127 / 27)) : 0);
                    $alpha = max($rgba['alpha'], $backgroundAlpha);
                    $color = (($alpha & 0x7f) << 24)
                        | (($rgba['red'] & 0xff) << 16)
                        | (($rgba['green'] & 0xff) << 8)
                        | ($rgba['blue'] & 0xff);

                    imagesetpixel($bitmap, $x, $y, $color);
                }
            }

            ob_start();
            imagepng($bitmap);
            $png = ob_get_clean();
            imagedestroy($bitmap);

            if (! is_string($png) || $png === '') {
                throw new \RuntimeException('PNG hasil normalisasi kosong.');
            }

            return $png;
        } catch (\Throwable $exception) {
            throw ValidationException::withMessages([
                'stamp' => 'Stempel tidak dapat diproses. Unggah gambar PNG, JPG, atau WEBP yang valid.',
            ]);
        }
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
