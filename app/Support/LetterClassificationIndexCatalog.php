<?php

namespace App\Support;

/**
 * Indeks klasifikasi arsip surat berdasarkan daftar klasifikasi kalurahan.
 *
 * Kode tipe surat tetap dipakai sebagai identitas internal layanan. Indeks
 * inilah yang dipakai sebagai bagian pertama nomor surat resmi.
 */
final class LetterClassificationIndexCatalog
{
    /** @return array<string, string> */
    public static function map(): array
    {
        return [
            // Kependudukan dan Catatan Sipil
            'SKBK' => '400.12', 'SKD' => '400.12', 'SPKTP' => '400.12', 'SPKIA' => '400.12',
            'SPPWNI' => '400.12', 'SGC' => '400.12', 'PNP' => '400.12', 'N2P' => '400.12',
            'PNL' => '400.12', 'PAK' => '400.12', 'FPK' => '400.12', 'LK' => '400.12',
            'SKAK' => '400.12', 'PPKT' => '400.12', 'LKLD' => '400.12', 'SPBNI' => '400.12',
            'SPTMDK' => '400.12', 'SPBMLP' => '400.12', 'SPTJMPSI' => '400.12',
            'SKDN' => '400.12', 'N1P' => '400.12', 'N4P' => '400.12', 'N5P' => '400.12',
            'N6P' => '400.12', 'SKWNP' => '400.12', 'SKWHP' => '400.12', 'SKNNP' => '400.12',
            'N1L' => '400.12', 'N4L' => '400.12', 'SKDPAK' => '400.12', 'SKKL' => '400.12',
            'SKKM' => '400.12', 'SPAKL' => '400.12', 'LPKL' => '400.12', 'SPTJMDK' => '400.12',
            'DKF' => '400.12', 'DKA' => '400.12', 'DKR' => '400.12', 'DKU' => '400.12',
            'DKS' => '400.12', 'DKP' => '400.12', 'DKC' => '400.12', 'DKH' => '400.12', 'FBWNI' => '400.12',
            'SPKIAF' => '400.12', 'SKBK2' => '400.12', 'SPCCP' => '400.12', 'SKKMP' => '400.12',
            'SKUMN' => '400.12', 'SPNIK' => '400.12', 'SPNIP' => '400.12', 'SKTNB' => '400.12',
            'FPDN' => '400.12', 'SKBKN' => '400.12', 'SPBML' => '400.12', 'SPOT' => '400.12',
            'SPPWF' => '400.12', 'SKDT' => '400.12', 'SPDP' => '400.12', 'SKTS' => '400.12',
            'SPTTS' => '400.12', 'SPTJP' => '400.12', 'SKWKA' => '400.12',

            // Pemerintahan umum dan pelayanan umum
            'SKUM' => '100.2', 'SKK' => '100.2', 'SKJ' => '100.2', 'SMLPI' => '100.2',
            'SPU' => '100.2', 'SKUMG' => '100.2', 'SKJG' => '100.2', 'SKJV' => '100.2',

            // Kesatuan bangsa dan politik
            'SKCK' => '200.1',

            // Sosial
            'SKTM' => '400.9',

            // Kesehatan
            'SPTKP' => '400.7',

            // Pendidikan
            'SKTMS' => '400.3', 'SBP' => '400.3',

            // Pemberdayaan masyarakat desa
            'SKU' => '400.10',

            // Pekerjaan umum dan ketenagaan
            'SRBBM' => '600', 'SPSKG' => '600',

            // Keuangan
            'SKP' => '900',

            // Kepegawaian
            'SPPD' => '800',

            // Kearsipan
            'RL' => '000.5',

            // Pertanian dan urusan pertanahan yang tersedia pada daftar indeks.
            'SKTA' => '500.6', 'SKTH' => '500.6', 'SPKLC' => '500.6', 'SKC' => '500.6',
        ];
    }

    public static function forCode(?string $code): ?string
    {
        return self::map()[$code] ?? null;
    }
}
