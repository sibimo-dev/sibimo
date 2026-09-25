<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LetterTypeDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $baseDocuments = [
            ['name' => 'Fotocopy Kartu Keluarga (KK)'],
            ['name' => 'Fotocopy Kartu Tanda Penduduk (KTP)'],
        ];

        $baseCodes = [
            'SKBK', 'SKU', 'SKUM', 'SKD', 'SKTM', 'SKP', 'SKK', 'SKJ', 'SKCK',
            'SPKTP', 'SPKIA', 'SRBBM', 'SPPWNI', 'SGC', 'SPPD', 'SPBNI', 'SPTMDK',
            'SKDPAK', 'SMLPI', 'SPSKG', 'SBP', 'SKDN', 'SPU', 'PNP', 'N1P', 'N2P',
            'N4P', 'N5P', 'N6P', 'SKWNP', 'SKWHP', 'SPTKP', 'SPBMLP', 'SKNNP', 'PNL',
            'N1L', 'N4L', 'PAK', 'FPK', 'LK', 'SKKL', 'SKAK', 'PPKT', 'LKLD',
            'SPTJMPSI', 'SKKM',
        ];

        $documentsByCode = array_fill_keys($baseCodes, $baseDocuments);

        $documentsByCode['SPKTP'] = array_merge($baseDocuments, [
            ['name' => 'Pass Photo (3x4)'],
        ]);

        $documentsByCode['SPKIA'] = array_merge($baseDocuments, [
            ['name' => 'Pass Photo (3x4)'],
        ]);

        $documentsByCode['SPBNI'] = [
            ['name' => 'Fotocopy SHM/Letter C'],
            ['name' => 'Fotocopy Kartu Keluarga (KK)'],
            ['name' => 'Fotocopy Kartu Tanda Penduduk (KTP)'],
            ['name' => 'Fotocopy PBB'],
            ['name' => 'Dokumen pendukung lain sesuai data yang berbeda'],
        ];

        $documentsByCode['PAK'] = [
            ['name' => 'Surat Keterangan Lahir dari Dokter/Bidan/Penolong Kelahiran'],
            ['name' => 'Surat Keterangan Kelahiran'],
            [
                'name' => 'Fotocopy Buku Nikah/Kutipan Akta Perkawinan Orang Tua',
                'description' => 'Dilegalisir',
            ],
            ['name' => 'Fotocopy Kartu Keluarga (KK) Orang Tua/Wali'],
            ['name' => 'Fotocopy KTP-el Orang Tua/Wali/Pelapor'],
            ['name' => 'Fotocopy KTP-el 2 (dua) Orang Saksi'],
            [
                'name' => 'Surat Kuasa dan Fotocopy KTP-el Penerima Kuasa',
                'is_required' => false,
            ],
            [
                'name' => 'Fotocopy Paspor bagi WNI Bukan Penduduk dan Orang Asing',
                'is_required' => false,
            ],
            [
                'name' => 'Fotocopy Surat Keterangan Tempat Tinggal (SKKT) Orang Tua bagi Pemegang ITAS',
                'is_required' => false,
            ],
            [
                'name' => 'Surat Keputusan Kepala Dinas Kependudukan dan Pencatatan Sipil',
                'is_required' => false,
            ],
            ['name' => 'SPTJM Kebenaran Data Kelahiran'],
            ['name' => 'SPTJM Kebenaran Sebagai Pasangan Suami Istri'],
        ];

        foreach ($documentsByCode as $code => $documents) {
            $this->seedDocumentsForCode($code, $documents);
        }
    }

    /**
     * Seed requirements for one existing letter type without duplicating rows.
     */
    private function seedDocumentsForCode(string $code, array $documents): void
    {
        $letterTypeId = DB::table('letter_types')
            ->where('code', $code)
            ->value('letter_type_id');

        if (!$letterTypeId) {
            throw new RuntimeException("Letter type [{$code}] must exist before its documents are seeded.");
        }

        foreach ($documents as $document) {
            DB::table('letter_type_documents')->updateOrInsert(
                [
                    'letter_type_id' => $letterTypeId,
                    'document_name' => $document['name'],
                ],
                [
                    'description' => $document['description'] ?? null,
                    'is_required' => $document['is_required'] ?? true,
                    'created_at' => now(),
                ],
            );
        }
    }
}
