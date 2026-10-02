<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LetterTypeSeeder extends Seeder
{
    public function run(): void
    {
        $defaultSignerId = DB::table('staff')
            ->where('is_signer', true)
            ->orderBy('staff_id')
            ->value('staff_id');

        $signerRows = DB::table('staff')
            ->where('is_signer', true)
            ->get(['staff_id', 'position']);

        $signerIdsByRole = [
            'lurah' => $signerRows->first(fn ($staff) => str_contains(mb_strtolower((string) $staff->position), 'lurah'))?->staff_id,
            'carik' => $signerRows->first(fn ($staff) => str_contains(mb_strtolower((string) $staff->position), 'carik'))?->staff_id,
            'kaur danarta' => $signerRows->first(fn ($staff) => str_contains(mb_strtolower((string) $staff->position), 'danarta'))?->staff_id,
            'kaur tata laksana' => $signerRows->first(fn ($staff) => str_contains(mb_strtolower((string) $staff->position), 'tata laksana'))?->staff_id,
            'kamituwa' => $signerRows->first(fn ($staff) => str_contains(mb_strtolower((string) $staff->position), 'kamituwa'))?->staff_id,
            'ulu-ulu' => $signerRows->first(fn ($staff) => str_contains(str_replace(' ', '', mb_strtolower((string) $staff->position)), 'ulu-ulu'))?->staff_id,
            'jagabaya' => $signerRows->first(fn ($staff) => str_contains(mb_strtolower((string) $staff->position), 'jagabaya'))?->staff_id,
        ];

        // Posisi penandatangan mengikuti konfigurasi template. Jika role
        // tertentu belum tersedia di database, gunakan signer utama sebagai
        // fallback agar foreign key tetap valid.
        $signerRoleByCode = [
            'SKBK' => 'lurah',
            'SKU' => 'kamituwa',
            'SKUM' => 'lurah',
            'SKD' => 'ulu-ulu',
            'SKTM' => 'kaur danarta',
            'SKP' => 'kaur tata laksana',
            'SKK' => 'jagabaya',
            'SKJ' => 'kaur tata laksana',
            'SKCK' => 'lurah',
            'SPKTP' => 'kaur tata laksana',
            'SPKIA' => 'kaur tata laksana',
            'SRBBM' => 'kaur tata laksana',
            'SPPWNI' => 'kaur tata laksana',
            'SGC' => 'carik',
            'SMLPI' => 'lurah',
            'SPSKG' => 'lurah',
            'PNP' => 'kamituwa',
            'N2P' => 'kamituwa',
            'PNL' => 'kamituwa',
            'PAK' => 'kaur tata laksana',
            'FPK' => 'kaur tata laksana',
            'LK' => 'kaur tata laksana',
            'SKAK' => 'kaur tata laksana',
            'PPKT' => 'kaur tata laksana',
            'LKLD' => 'kaur tata laksana',
            'SPPD' => 'kaur tata laksana',
            'SPBNI' => 'kaur tata laksana',
            'SPTMDK' => 'kaur tata laksana',
            'SPBMLP' => 'kamituwa',
            'SPTJMPSI' => 'ulu-ulu',
            'SKDN' => 'kamituwa',
            'SPU' => 'kaur tata laksana',
            'N1P' => 'kamituwa',
            'N4P' => 'kamituwa',
            'N5P' => 'kamituwa',
            'N6P' => 'kamituwa',
            'SKWNP' => 'kamituwa',
            'SKWHP' => 'kamituwa',
            'SPTKP' => 'kamituwa',
            'SKNNP' => 'kamituwa',
            'N1L' => 'kamituwa',
            'N4L' => 'kamituwa',
            'SKDPAK' => 'kaur tata laksana',
            'SBP' => 'kaur tata laksana',
            'SKKL' => 'kamituwa',
            'SKKM' => 'kaur tata laksana',
        ];

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

        $bladeViews = [
            'SKBK' => 'letters.unmarried-status-letter',
            'SKU' => 'letters.business-permit-letter',
            'SKUM' => 'letters.general-statement-letter',
            'SKD' => 'letters.domicile-certificate',
            'SKTM' => 'letters.sktm-general',
            'SKP' => 'letters.income-permit-letter',
            'SKK' => 'letters.event-permit-letter',
            'SKJ' => 'letters.travel-permit-letter',
            'SKCK' => 'letters.skck-referral-letter',
            'SPKTP' => 'letters.ktp-application-form',
            'SPKIA' => 'letters.population-occurrence-registration-form',
            'SRBBM' => 'letters.fuel-recommendation-letter',
            'SPPWNI' => 'letters.relocation-cover-letter',
            'SGC' => 'letters.divorce-lawsuit-letter',
            'SMLPI' => 'letters.permit-followup-letter',
            'SPSKG' => 'letters.lease-offer-letter',
            'PNP' => 'letters.marriage-women.letters.registration-form',
            'N2P' => 'letters.marriage-women.letters.n2',
            'PNL' => 'letters.marriage-women.letters.registration-form',
            'PAK' => 'letters.birth.birth-certificate-application-form',
            'FPK' => 'letters.birth.birth-report-form',
            'LK' => 'letters.birth.birth-report-statement',
            'SKAK' => 'letters.birth.birth-certificate-power-of-attorney',
            'PPKT' => 'letters.birth.late-birth-registration-approval-decree',
            'LKLD' => 'letters.birth.out-of-domicile-birth-report',
            'SPPD' => 'letters.duty-travel-order-letter',
            'SPBNI' => 'letters.identity-discrepancy-statement-letter',
            'SPTMDK' => 'letters.population-document-statement-letter',
            'SPBMLP' => 'letters.marriage-women.letters.unmarried-statement',
            'SPTJMPSI' => 'letters.birth.spousal-relationship-responsibility-statement',
            'SKDN' => 'letters.marriage-certificate-duplicate-letter',
            'SPU' => 'letters.general-cover-letter',
            'N1P' => 'letters.marriage-women.letters.n1',
            'N4P' => 'letters.marriage-women.letters.n4',
            'N5P' => 'letters.marriage-women.letters.n5',
            'N6P' => 'letters.marriage-women.letters.n6',
            'SKWNP' => 'letters.marriage-women.letters.guardian-statement',
            'SKWHP' => 'letters.marriage-women.letters.judge-guardian',
            'SPTKP' => 'letters.marriage-women.letters.health-referral',
            'SKNNP' => 'letters.marriage-women.letters.numpang-nikah',
            'N1L' => 'letters.marriage-women.letters.n1',
            'N4L' => 'letters.marriage-women.letters.n4',
            'SKDPAK' => 'letters.population-service-authorization-letter',
            'SBP' => 'letters.research-response-letter',
            'SKKL' => 'letters.birth.birth-attestation-letter',
            'SKKM' => 'letters.death.death-certificate',
        ];

        foreach ($types as [$code, $name, $category, $signatureMethod]) {
            $signerRole = $signerRoleByCode[$code] ?? 'kaur tata laksana';
            $signerId = $signerIdsByRole[$signerRole] ?? $defaultSignerId;

            DB::table('letter_types')->updateOrInsert(
                ['code' => $code],
                [
                    'letter_name' => $name,
                    'category' => $category,
                    'description' => "Layanan {$name}.",
                    'blade_view' => $bladeViews[$code] ?? null,
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
