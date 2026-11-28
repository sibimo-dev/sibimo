<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Color;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Laravel\Facades\Image;

class SignatureSpecimenNormalizer
{
    public function isSpecimen(?string $documentName): bool
    {
        return preg_match('/spesimen|tanda\s*tangan|cap\s*jempol/i', (string) $documentName) === 1;
    }

    public function fileName(UploadedFile $file): string
    {
        return $this->pngFileName($file->getClientOriginalName());
    }

    public function normalize(UploadedFile $file): string
    {
        return $this->normalizeInput($file);
    }

    public function normalizeContents(string $contents): string
    {
        return $this->normalizeInput($contents);
    }

    public function pngFileName(?string $originalName): string
    {
        return (Str::slug(pathinfo((string) $originalName, PATHINFO_FILENAME)) ?: 'spesimen-tanda-tangan') . '.png';
    }

    private function normalizeInput(mixed $input): string
    {
        try {
            $image = Image::decode($input)
                ->contain(600, 200, Color::transparent())
                ->encode(new PngEncoder());
            $bitmap = imagecreatefromstring((string) $image);

            if ($bitmap === false) {
                throw new \RuntimeException('Gambar tidak bisa dibaca GD.');
            }

            imagealphablending($bitmap, false);
            imagesavealpha($bitmap, true);

            for ($y = 0, $height = imagesy($bitmap); $y < $height; $y++) {
                for ($x = 0, $width = imagesx($bitmap); $x < $width; $x++) {
                    $rgba = imagecolorsforindex($bitmap, imagecolorat($bitmap, $x, $y));
                    $backgroundLevel = min($rgba['red'], $rgba['green'], $rgba['blue']);
                    $backgroundAlpha = $backgroundLevel >= 242
                        ? 127
                        : ($backgroundLevel > 215 ? (int) round(($backgroundLevel - 215) * (127 / 27)) : 0);
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
                'file' => 'Spesimen tanda tangan tidak dapat diproses. Unggah gambar JPG atau PNG yang valid.',
            ]);
        }
    }
}
