<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SignerSeeder extends Seeder
{
    public function run(): void
    {
        // Signer harus memakai nama dan jabatan yang sama dengan struktur
        // organisasi pada StaffSeeder. Data ini dipisahkan di tabel staff
        // melalui flag is_signer agar tetap kompatibel dengan relasi lama.
        $signers = [
            ['name' => 'Tutik Wahyuningsih, S.Sos., M.AP', 'position' => 'Lurah Kalurahan Bimomartani', 'level' => 'Lurah', 'description' => 'Memimpin penyelenggaraan pemerintahan kalurahan.'],
            ['name' => 'Yudi Priyo Utomo, SE', 'position' => 'Carik', 'level' => 'Carik', 'description' => 'Membantu Lurah dalam tata usaha dan pelayanan administrasi.'],
            ['name' => 'Nanda Mutiara Dewi, S.Psi', 'position' => 'Kaur Danarta', 'level' => 'Kepala Urusan (Sekretariat & Keuangan)', 'description' => 'Mengelola urusan keuangan dan anggaran kalurahan.'],
            ['name' => 'Rasyifa Anom Sudaryono, Amd.Kes', 'position' => 'Kaur Tata Laksana', 'level' => 'Kepala Urusan (Sekretariat & Keuangan)', 'description' => 'Mengelola tata laksana pemerintahan dan administrasi umum.'],
            ['name' => 'Hanang Tri Nugroho, S.Kom', 'position' => 'Kaur Pangripta', 'level' => 'Kepala Urusan (Sekretariat & Keuangan)', 'description' => 'Menyusun perencanaan dan pelaporan pembangunan kalurahan.'],
            ['name' => 'Sutriyana, S.Ag', 'position' => 'Kamituwa', 'level' => 'Kepala Seksi', 'description' => 'Mengoordinasikan urusan kemasyarakatan kalurahan.'],
            ['name' => 'Yordan Ardi Tamara, S.Kom', 'position' => 'Ulu-Ulu', 'level' => 'Kepala Seksi', 'description' => 'Mengelola urusan pengairan dan pertanian kalurahan.'],
            ['name' => 'Rifai Nurmansyah, S.Pd., M.Pd', 'position' => 'Jagabaya', 'level' => 'Kepala Seksi', 'description' => 'Bertanggung jawab atas ketentraman dan ketertiban wilayah.'],
        ];

        DB::transaction(function () use ($signers): void {
            $existingIds = DB::table('staff')
                ->where('is_signer', true)
                ->orderBy('staff_id')
                ->pluck('staff_id')
                ->values();

            $signerIds = [];

            foreach ($signers as $index => $signer) {
                // Reuse signer lama berdasarkan urutan agar foreign key pada
                // request dan tipe surat tetap dapat dipertahankan.
                $staffId = $existingIds->get($index);
                $attributes = [
                    ...$signer,
                    'is_signer' => true,
                    'photo' => null,
                    'updated_at' => now(),
                ];

                if ($staffId) {
                    DB::table('staff')->where('staff_id', $staffId)->update($attributes);
                } else {
                    $staffId = DB::table('staff')->insertGetId([
                        ...$attributes,
                        'created_at' => now(),
                    ], 'staff_id');
                }

                $signerIds[] = $staffId;
            }

            $primarySignerId = $signerIds[0];
            $obsoleteIds = $existingIds->diff($signerIds)->values();

            if ($obsoleteIds->isNotEmpty()) {
                DB::table('letter_requests')
                    ->whereIn('authorized_by_signer_id', $obsoleteIds->all())
                    ->update(['authorized_by_signer_id' => $primarySignerId]);

                DB::table('letter_types')
                    ->whereIn('signer_id', $obsoleteIds->all())
                    ->update(['signer_id' => $primarySignerId]);

                DB::table('staff')
                    ->whereIn('staff_id', $obsoleteIds->all())
                    ->delete();
            }
        });
    }
}
