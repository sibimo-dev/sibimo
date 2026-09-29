<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LetterTypeSeeder extends Seeder
{
    public function run(): void
    {
        $signerId = DB::table('staff')
            ->where('is_signer', true)
            ->orderBy('staff_id')
            ->value('staff_id');

        $types = [
            // Surat keterangan
            ['SKBK', 'Surat Keterangan Belum Kawin', 'Keterangan', 'manual'],
            ['SKU', 'Surat Keterangan Usaha', 'Keterangan', 'digital'],
            ['SKUM', 'Surat Keterangan Umum', 'Keterangan', 'manual'],
            ['SKD', 'Surat Keterangan Domisili', 'Keterangan', 'digital'],
            ['SKTM', 'Surat Keterangan Tidak Mampu', 'Keterangan', 'digital'],
            ['SKP', 'Surat Keterangan Penghasilan', 'Keterangan', 'digital'],
            ['SKK', 'Surat Keterangan Keramaian', 'Keterangan', 'digital'],
            ['SKJ', 'Surat Keterangan Jalan', 'Keterangan', 'digital'],
            ['SKCK', 'Surat Keterangan Mohon SKCK', 'Keterangan', 'manual'],

            // Surat permohonan
            ['SPKTP', 'Surat Permohonan KTP', 'Permohonan', 'digital'],
            ['SPKIA', 'Surat Permohonan Penerbitan KIA', 'Permohonan', 'digital'],
            ['SRBBM', 'Surat Rekomendasi Pembelian Jenis BBM Tertentu', 'Permohonan', 'manual'],
            ['SPPWNI', 'Surat Permohonan Pindah WNI', 'Permohonan', 'digital'],
            ['SGC', 'Surat Permohonan Cerai', 'Permohonan', 'manual'],
            ['SMLPI', 'Surat Menindaklanjuti Permohonan Izin', 'Permohonan', 'digital'],
            ['SPSKG', 'Surat Penawaran Sewa Kontrak Gedung', 'Permohonan', 'manual'],
            ['PNP', 'Pendaftaran Nikah Perempuan', 'Permohonan', 'digital'],
            ['N2P', 'Permohonan Kehendak Nikah', 'Permohonan', 'digital'],
            ['PNL', 'Pendaftaran Nikah Laki-laki', 'Permohonan', 'digital'],
            ['PAK', 'Permohonan Akta Kelahiran', 'Permohonan', 'digital'],
            ['FPK', 'Formulir Pelaporan Kelahiran', 'Permohonan', 'digital'],
            ['LK', 'Laporan Kelahiran', 'Permohonan', 'digital'],
            ['SKAK', 'Surat Kuasa Permohonan Akta Kelahiran', 'Permohonan', 'manual'],
            ['PPKT', 'Persetujuan Pencatatan Kelahiran Terlambat', 'Permohonan', 'manual'],
            ['LKLD', 'Laporan Kelahiran Luar Domisili', 'Permohonan', 'digital'],

            // Surat perintah
            ['SPPD', 'SPPD (Surat Perintah Perjalanan Dinas)', 'Perintah', 'digital'],

            // Surat pernyataan
            ['SPBNI', 'Surat Pernyataan Beda Nama/Identitas', 'Pernyataan', 'digital'],
            ['SPTMDK', 'Surat Pernyataan Tidak Memiliki Dokumen Kependudukan', 'Pernyataan', 'digital'],
            ['SPBMLP', 'Surat Pernyataan Belum Menikah Lagi Perempuan', 'Pernyataan', 'digital'],
            ['SPTJMPSI', 'SPTJM Kebenaran Sebagai Pasangan Suami Istri', 'Pernyataan', 'digital'],

            // Surat pengantar
            ['SKDN', 'Surat Pengantar Duplikat Nikah', 'Pengantar', 'digital'],
            ['SPU', 'Surat Pengantar Umum', 'Pengantar', 'digital'],
            ['N1P', 'Pengantar Nikah Perempuan', 'Pengantar', 'digital'],
            ['N4P', 'Persetujuan Calon Pengantin', 'Pengantar', 'digital'],
            ['N5P', 'Surat Izin Orang Tua', 'Pengantar', 'digital'],
            ['N6P', 'Surat Keterangan Kematian', 'Pengantar', 'digital'],
            ['SKWNP', 'Surat Keterangan Wali Nikah', 'Pengantar', 'digital'],
            ['SKWHP', 'Surat Keterangan Wali Hakim', 'Pengantar', 'digital'],
            ['SPTKP', 'Surat Pengantar Tes Kesehatan Perempuan', 'Pengantar', 'digital'],
            ['SKNNP', 'Surat Keterangan Numpang Nikah Perempuan', 'Pengantar', 'digital'],
            ['N1L', 'Pengantar Nikah Laki-laki', 'Pengantar', 'digital'],
            ['N4L', 'Persetujuan Calon Pengantin Laki-laki', 'Pengantar', 'digital'],

            // Surat keterangan/pendukung lain
            ['SKDPAK', 'Surat Kuasa Dalam Pelayanan Administrasi Kependudukan', 'Keterangan', 'manual'],
            ['SBP', 'Surat Balasan Penelitian', 'Keterangan', 'manual'],
            ['SKKL', 'Surat Keterangan Kelahiran', 'Keterangan', 'digital'],
            ['SKKM', 'Surat Keterangan Kematian', 'Keterangan', 'digital'],
        ];

        foreach ($types as [$code, $name, $category, $signatureMethod]) {
            DB::table('letter_types')->updateOrInsert(
                ['code' => $code],
                [
                    'letter_name' => $name,
                    'category' => $category,
                    'description' => "Layanan {$name}.",
                    'blade_view' => null,
                    'number_prefix' => $code . '/',
                    'processing_time' => '1 hari',
                    'signature_method' => $signatureMethod,
                    'signer_id' => $signerId,
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }
    }
}
