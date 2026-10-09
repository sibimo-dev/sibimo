<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LegalProductSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('user_id');

        // Salin PDF contoh ke storage/app/public/legal-products/
        $source = database_path('seeders/files/contoh-produk-hukum.pdf');
        $hasSource = is_file($source);
        
        if (! $hasSource) {
            $this->command?->warn("PDF contoh tidak ditemukan di {$source}, kolom document diisi null.");
        }

        $categories = ['perkal', 'sk-lurah'];
        $labels     = ['perkal' => 'Peraturan Kalurahan', 'sk-lurah' => 'SK Lurah'];

        for ($i = 0; $i < 10; $i++) {
            $category = $categories[$i % 2];
            $year     = 2026 - intdiv($i, 3);
            $no       = str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
            $title    = "{$labels[$category]} Seeder {$no}";
            $document = null;
            if ($hasSource) {
                $document = "legal-products/seeder-{$no}.pdf";
                Storage::disk('public')->put($document, file_get_contents($source));
            }

            DB::table('legal_products')->updateOrInsert(
                ['title' => $title],
                [
                    'category'    => $category,
                    'status'      => $i % 5 === 4 ? 'dicabut' : 'berlaku',
                    'number'      => "Nomor {$no} Tahun {$year}",
                    'year'        => $year,
                    'description' => 'Produk hukum contoh untuk pengujian sistem SIBIMO.',
                    'document'    => $document,
                    'created_by'  => $userIds->first(),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
            );
        }
    }
}