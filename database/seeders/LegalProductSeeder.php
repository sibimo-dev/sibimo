<?php

namespace Database\Seeders;

use Barryvdh\DomPDF\Facade\Pdf;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LegalProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('id_ID');
        $faker->seed(20261009);

        $userIds = DB::table('users')->pluck('user_id');

        // Bersihkan hanya data contoh dari versi seeder lama.
        DB::table('legal_products')
            ->where(function ($query) {
                $query
                    ->where('title', 'like', 'Peraturan Kalurahan Seeder %')
                    ->orWhere('title', 'like', 'SK Lurah Seeder %');
            })
            ->delete();

        $records = [
            [
                'category' => 'perkal',
                'title' => 'Peraturan Kalurahan Bimomartani tentang Anggaran Pendapatan dan Belanja Kalurahan Tahun Anggaran 2026',
                'year' => 2026,
                'status' => 'berlaku',
                'topic' => 'pengelolaan anggaran dan keuangan kalurahan',
            ],
            [
                'category' => 'perkal',
                'title' => 'Peraturan Kalurahan Bimomartani tentang Rencana Kerja Pemerintah Kalurahan Tahun 2026',
                'year' => 2026,
                'status' => 'berlaku',
                'topic' => 'perencanaan pembangunan kalurahan',
            ],
            [
                'category' => 'perkal',
                'title' => 'Peraturan Kalurahan Bimomartani tentang Pengelolaan Aset Kalurahan',
                'year' => 2025,
                'status' => 'berlaku',
                'topic' => 'penatausahaan dan pemanfaatan aset kalurahan',
            ],
            [
                'category' => 'perkal',
                'title' => 'Peraturan Kalurahan Bimomartani tentang Perlindungan dan Pemberdayaan Masyarakat',
                'year' => 2025,
                'status' => 'berlaku',
                'topic' => 'pemberdayaan dan perlindungan masyarakat',
            ],
            [
                'category' => 'perkal',
                'title' => 'Peraturan Kalurahan Bimomartani tentang Pembentukan Badan Usaha Milik Kalurahan',
                'year' => 2024,
                'status' => 'dicabut',
                'topic' => 'penguatan usaha dan perekonomian kalurahan',
            ],
            [
                'category' => 'sk-lurah',
                'title' => 'Keputusan Lurah Bimomartani tentang Penetapan Tim Pelaksana Kegiatan Tahun Anggaran 2026',
                'year' => 2026,
                'status' => 'berlaku',
                'topic' => 'pelaksanaan kegiatan pembangunan kalurahan',
            ],
            [
                'category' => 'sk-lurah',
                'title' => 'Keputusan Lurah Bimomartani tentang Penetapan Penerima Bantuan Sosial Kalurahan',
                'year' => 2026,
                'status' => 'berlaku',
                'topic' => 'penyaluran bantuan sosial kepada masyarakat',
            ],
            [
                'category' => 'sk-lurah',
                'title' => 'Keputusan Lurah Bimomartani tentang Pembentukan Tim Pengelola Informasi dan Dokumentasi',
                'year' => 2025,
                'status' => 'berlaku',
                'topic' => 'keterbukaan informasi publik kalurahan',
            ],
            [
                'category' => 'sk-lurah',
                'title' => 'Keputusan Lurah Bimomartani tentang Penetapan Kader Pemberdayaan Masyarakat',
                'year' => 2025,
                'status' => 'berlaku',
                'topic' => 'pembinaan dan pemberdayaan masyarakat',
            ],
            [
                'category' => 'sk-lurah',
                'title' => 'Keputusan Lurah Bimomartani tentang Penunjukan Pengelola Barang Milik Kalurahan',
                'year' => 2024,
                'status' => 'dicabut',
                'topic' => 'pengelolaan barang milik kalurahan',
            ],
        ];

        foreach ($records as $index => $record) {
            $no = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
            $number = $faker->numberBetween(1, 99);
            $document = "legal-products/seeder-{$no}.pdf";
            $categoryLabel = $record['category'] === 'perkal'
                ? 'Peraturan Kalurahan'
                : 'Keputusan Lurah';
            $description = sprintf(
                '%s ini menjadi dasar hukum dalam %s di Kalurahan Bimomartani. %s',
                $categoryLabel,
                $record['topic'],
                $faker->randomElement([
                    'Pelaksanaannya dilakukan dengan memperhatikan ketentuan peraturan perundang-undangan yang berlaku.',
                    'Dokumen ini menjadi pedoman bagi pemerintah kalurahan dan masyarakat dalam pelaksanaannya.',
                    'Pelaksanaan dan evaluasinya dilakukan secara tertib, transparan, dan dapat dipertanggungjawabkan.',
                ]),
            );

            $this->writePreviewPdf($document, [
                ...$record,
                'category_label' => $categoryLabel,
                'number' => "Nomor {$number} Tahun {$record['year']}",
                'description' => $description,
                'status_label' => $record['status'] === 'berlaku' ? 'Berlaku' : 'Dicabut',
            ]);

            DB::table('legal_products')->updateOrInsert(
                ['title' => $record['title']],
                [
                    'category'    => $record['category'],
                    'status'      => $record['status'],
                    'number'      => "Nomor {$number} Tahun {$record['year']}",
                    'year'        => $record['year'],
                    'description' => $description,
                    'document'    => $document,
                    'created_by'  => $userIds->first(),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
            );
        }
    }

    private function writePreviewPdf(string $document, array $product): void
    {
        $pdf = Pdf::loadView('legal-products.seed-document', compact('product'))
            ->setPaper('a4', 'portrait');

        Storage::disk('public')->put($document, $pdf->output());
    }
}
