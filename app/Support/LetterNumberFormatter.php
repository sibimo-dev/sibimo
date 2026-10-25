<?php

namespace App\Support;

final class LetterNumberFormatter
{
    /**
     * Preview/PDF compatibility: ubah nomor lama seperti SKTA/001/X/2026
     * menjadi nomor indeks baru tanpa mengubah nilai historis di database.
     */
    public static function forDisplay(?string $number, ?string $classificationIndex): ?string
    {
        $number = trim((string) $number);
        if ($number === '') {
            return null;
        }

        if (preg_match('/^\d+(?:\.\d+)*\/\d{1,}$/', $number)) {
            return $number;
        }

        if (! preg_match('/^[A-Za-z][A-Za-z0-9]*\/(\d{1,})(?:\/[IVXLCDM]+\/\d{4})?$/i', $number, $matches)) {
            return $number;
        }

        $index = trim((string) ($classificationIndex ?: '100.2'));
        return $index . '/' . str_pad((string) ((int) $matches[1]), 3, '0', STR_PAD_LEFT);
    }
}
