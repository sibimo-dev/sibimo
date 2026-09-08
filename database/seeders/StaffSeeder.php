<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $photo = static fn (string $file): string => Storage::disk('public')->url("profile/organization/{$file}");

        $staff = [
            ['Tutik Wahyuningsih, S.Sos., M.AP', 'Lurah Kalurahan Bimomartani', 'Lurah', 'Memimpin penyelenggaraan pemerintahan kalurahan.', 'tutik-wahyuningsih.jpeg'],
            ['Yudi Priyo Utomo, SE', 'Carik', 'Carik', 'Membantu Lurah dalam tata usaha dan pelayanan administrasi.', 'yudi-priyo-utomo.jpeg'],
            ['Nanda Mutiara Dewi, S.Psi', 'Kaur Danarta', 'Kepala Urusan (Sekretariat & Keuangan)', 'Mengelola urusan keuangan dan anggaran kalurahan.', 'nanda-mutiara-dewi.jpeg'],
            ['Rasyifa Anom Sudaryono, Amd.Kes', 'Kaur Tata Laksana', 'Kepala Urusan (Sekretariat & Keuangan)', 'Mengelola tata laksana pemerintahan dan administrasi umum.', null],
            ['Hanang Tri Nugroho, S.Kom', 'Kaur Pangripta', 'Kepala Urusan (Sekretariat & Keuangan)', 'Menyusun perencanaan dan pelaporan pembangunan kalurahan.', 'hanang-tri-nugroho.jpeg'],
            ['Sutriyana, S.Ag', 'Kamituwa', 'Kepala Seksi', 'Mengoordinasikan urusan kemasyarakatan kalurahan.', 'sutriyana.jpeg'],
            ['Yordan Ardi Tamara, S.Kom', 'Ulu-Ulu', 'Kepala Seksi', 'Mengelola urusan pengairan dan pertanian kalurahan.', 'yordan-ardi-tamara.jpeg'],
            ['Rifai Nurmansyah, S.Pd., M.Pd', 'Jagabaya', 'Kepala Seksi', 'Bertanggung jawab atas ketentraman dan ketertiban wilayah.', 'rifai-nurmansyah.jpeg'],
            ['Jaka Widada', 'Dukuh I Krebet', 'Dukuh (Kepala Padukuhan)', 'Memimpin wilayah Padukuhan Krebet.', 'jaka-widada.jpeg'],
            ['Angga Wahyu Indra Irawan, S.Pd', 'Dukuh II Rogobangsan', 'Dukuh (Kepala Padukuhan)', 'Memimpin wilayah Padukuhan Rogobangsan.', 'angga-wahyu-indra-irawan.jpeg'],
            ['Umi Solikah', 'Dukuh III Kalibulus', 'Dukuh (Kepala Padukuhan)', 'Memimpin wilayah Padukuhan Kalibulus.', 'umi-solikah.jpeg'],
            ['Kaharudin', 'Dukuh IV Macanan', 'Dukuh (Kepala Padukuhan)', 'Memimpin wilayah Padukuhan Macanan.', 'kaharudin.jpeg'],
            ['Mucharom', 'Dukuh V Cokrogaten', 'Dukuh (Kepala Padukuhan)', 'Memimpin wilayah Padukuhan Cokrogaten.', 'mucharom.jpeg'],
            ['TH Dwi Wahyu P, Amd', 'Dukuh VI Purwobinangun', 'Dukuh (Kepala Padukuhan)', 'Memimpin wilayah Padukuhan Purwobinangun.', null],
            ['Sukirman', 'Dukuh VII Pondok Suruh', 'Dukuh (Kepala Padukuhan)', 'Memimpin wilayah Padukuhan Pondok Suruh.', null],
            ['Sunaryo', 'Dukuh VIII Balong', 'Dukuh (Kepala Padukuhan)', 'Memimpin wilayah Padukuhan Balong.', 'sunaryo.jpeg'],
            ['Suharyono', 'Dukuh IX Kragilan', 'Dukuh (Kepala Padukuhan)', 'Memimpin wilayah Padukuhan Kragilan.', 'suharyono.jpeg'],
            ['Basuki Wibowo', 'Dukuh X Banjarharjo', 'Dukuh (Kepala Padukuhan)', 'Memimpin wilayah Padukuhan Banjarharjo.', 'basuki-wibawa.jpeg'],
            ['Drs. Jazim Thoyibi', 'Dukuh XI Sorasan', 'Dukuh (Kepala Padukuhan)', 'Memimpin wilayah Padukuhan Sorasan.', 'jazim-thoyibi.jpeg'],
            ['Purnomo', 'Dukuh XII Koroulon Kidul', 'Dukuh (Kepala Padukuhan)', 'Memimpin wilayah Padukuhan Koroulon Kidul.', null],
            ['Ratna Kurnia Dewi', 'Staf Pamong Kalurahan', 'Staff Pamong Kalurahan', 'Membantu tugas administrasi dan pelayanan kalurahan.', 'ratna-kurnia-dewi.jpg'],
            ['Khoirunnisa Hidaya', 'Staf Pamong Kalurahan', 'Staff Pamong Kalurahan', 'Membantu tugas administrasi dan pelayanan kalurahan.', 'khoirunnisa-hidaya.jpg'],
            ['Mega Dwi Jayanti', 'Staf Pamong Kalurahan', 'Staff Pamong Kalurahan', 'Membantu tugas administrasi dan pelayanan kalurahan.', 'mega-dwi-jayanti.jpg'],
            ['Sigit Raharjo', 'Staf Pamong Kalurahan', 'Staff Pamong Kalurahan', 'Membantu tugas administrasi dan pelayanan kalurahan.', 'sigit-raharjo.jpeg'],
            ['Linggar Yudha Pranata', 'Staf Pamong Kalurahan', 'Staff Pamong Kalurahan', 'Membantu tugas administrasi dan pelayanan kalurahan.', 'linggar-yudha-pranata.jpg'],
            ['Riyanto', 'Staf Pamong Kalurahan', 'Staff Pamong Kalurahan', 'Membantu tugas administrasi dan pelayanan kalurahan.', 'riyanto.jpg'],
        ];

        foreach ($staff as [$name, $position, $level, $description, $filename]) {
            Staff::updateOrCreate(
                ['name' => $name, 'position' => $position, 'is_signer' => false],
                [
                    'level' => $level,
                    'description' => $description,
                    'photo' => $filename ? $photo($filename) : null,
                    'is_signer' => false,
                ],
            );
        }
    }
}
