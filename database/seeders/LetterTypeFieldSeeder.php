<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LetterTypeFieldSeeder extends Seeder
{
    /** @var array<string, bool> Codes whose first definition is canonical. */
    private array $canonicalCodes = [];

    public function run(): void
    {
    // SURAT KETERANGAN
        $this->seedForCode('SKBK', [ // Surat Keterangan Belum Kawin
            ['label' => 'Tempat Lahir', 'key' => 'tempat_lahir', 'type' => 'text'],
            ['label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir', 'type' => 'date'],
            ['label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['label' => 'Agama', 'key' => 'agama', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['label' => 'Pekerjaan', 'key' => 'pekerjaan', 'type' => 'text'],
        ]);

        $this->seedForCode('SKU', [ // Surat Keterangan Usaha
            ['label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['label' => 'Tempat Lahir', 'key' => 'tempat_lahir', 'type' => 'text'],
            ['label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir', 'type' => 'date'],
            ['label' => 'Status Perkawinan', 'key' => 'status_perkawinan', 'type' => 'select', 'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']],
            ['label' => 'Pekerjaan', 'key' => 'pekerjaan', 'type' => 'text'],
            ['label' => 'Jenis Usaha', 'key' => 'jenis_usaha', 'type' => 'text'],
            ['label' => 'Alamat Usaha', 'key' => 'alamat_usaha', 'type' => 'textarea'],
            ['label' => 'Keperluan', 'key' => 'keperluan', 'type' => 'text'],
            ['label' => 'Instansi Tujuan', 'key' => 'instansi_tujuan', 'type' => 'text'],
        ]);

        $this->seedForCode('SKUM', [ // Surat Keterangan Umum
            ['label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['label' => 'Tempat Lahir', 'key' => 'tempat_lahir', 'type' => 'text'],
            ['label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir', 'type' => 'date'],
            ['label' => 'Status Perkawinan', 'key' => 'status_perkawinan', 'type' => 'select', 'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']],
            ['label' => 'Agama', 'key' => 'agama', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['label' => 'Pekerjaan', 'key' => 'pekerjaan', 'type' => 'text'],
            ['label' => 'Menerangkan Bahwa', 'key' => 'menerangkan_bahwa', 'type' => 'textarea'],
        ]);

        $this->seedForCode('SKD', [ // Surat Keterangan Domisili
            // Identitas usaha
            ['section' => 'Identitas Usaha', 'label' => 'Nama Perusahaan/Yayasan', 'key' => 'nama_perusahaan', 'type' => 'text'],
            ['section' => 'Identitas Usaha', 'label' => 'Jenis Usaha/Kegiatan', 'key' => 'jenis_usaha_kegiatan', 'type' => 'text'],
            ['section' => 'Identitas Usaha', 'label' => 'Status Bangunan', 'key' => 'status_bangunan', 'type' => 'text'],
            ['section' => 'Identitas Usaha', 'label' => 'Kegunaan Bangunan', 'key' => 'kegunaan_bangunan', 'type' => 'text'],
            ['section' => 'Identitas Usaha', 'label' => 'Nama Penanggung Jawab/Pimpinan', 'key' => 'nama_penanggung_jawab', 'type' => 'text'],
            ['section' => 'Identitas Usaha', 'label' => 'Jumlah Karyawan', 'key' => 'jumlah_karyawan', 'type' => 'number'],
            ['section' => 'Identitas Usaha', 'label' => 'Nomor Telepon/HP', 'key' => 'nomor_telepon_usaha', 'type' => 'text'],
            ['section' => 'Identitas Usaha', 'label' => 'Alamat Domisili Usaha', 'key' => 'alamat_domisili_usaha', 'type' => 'textarea'],
            // Identitas pemilik 
            ['section' => 'Identitas Pemilik', 'label' => 'Nama Pemilik', 'key' => 'nama_pemilik', 'type' => 'text'],
            ['section' => 'Identitas Pemilik', 'label' => 'NIK Pemilik', 'key' => 'nik_pemilik', 'type' => 'text'],
            ['section' => 'Identitas Pemilik', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir_pemilik', 'type' => 'text'],
            ['section' => 'Identitas Pemilik', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_pemilik', 'type' => 'date'],
            ['section' => 'Identitas Pemilik', 'label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin_pemilik', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['section' => 'Identitas Pemilik', 'label' => 'Status Perkawinan', 'key' => 'status_perkawinan', 'type' => 'select', 'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']],
            ['section' => 'Identitas Pemilik', 'label' => 'Agama', 'key' => 'agama_pemilik', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Identitas Pemilik', 'label' => 'Alamat Pemilik', 'key' => 'alamat_pemilik', 'type' => 'textarea'],
        ]);

        $this->seedForCode('SKTM', [ // Surat Keterangan Tidak Mampu
            ['label' => 'Tempat Lahir', 'key' => 'tempat_lahir', 'type' => 'text'],
            ['label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir', 'type' => 'date'],
            ['label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['label' => 'Status Perkawinan', 'key' => 'status_perkawinan', 'type' => 'select', 'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']],
            ['label' => 'Agama', 'key' => 'agama', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['label' => 'Pekerjaan', 'key' => 'pekerjaan', 'type' => 'text'],
            ['label' => 'Penghasilan', 'key' => 'penghasilan', 'type' => 'number'],
            ['label' => 'Kategori', 'key' => 'kategori', 'type' => 'text', 'is_required' => false],
            ['label' => 'Nomor KKM/KRM', 'key' => 'nomor_kkm_krm', 'type' => 'text', 'is_required' => false],
            ['label' => 'Keperluan', 'key' => 'keperluan', 'type' => 'textarea'],
            ['label' => 'Instansi/Tujuan', 'key' => 'instansi_tujuan', 'type' => 'text'],
        ]);

        $this->seedForCode('SKP', [ // Surat Keterangan Penghasilan
            ['label' => 'Tempat Lahir', 'key' => 'tempat_lahir', 'type' => 'text'],
            ['label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir', 'type' => 'date'],
            ['label' => 'Status Perkawinan', 'key' => 'status_perkawinan', 'type' => 'select', 'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']],
            ['label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['label' => 'Agama', 'key' => 'agama', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['label' => 'Pekerjaan', 'key' => 'pekerjaan', 'type' => 'text'],
            ['label' => 'Penghasilan', 'key' => 'penghasilan', 'type' => 'number'],
            ['label' => 'Kategori', 'key' => 'kategori', 'type' => 'text', 'is_required' => false],
            ['label' => 'Nomor KKM/KRM', 'key' => 'nomor_kkm_krm', 'type' => 'text', 'is_required' => false],
            ['label' => 'Dipergunakan Untuk', 'key' => 'dipergunakan_untuk', 'type' => 'textarea'],
            // Diisi jika surat untuk keperluan anak/wali (contoh: syarat sekolah/beasiswa)
            ['section' => 'Data Anak/Wali (jika ada)', 'label' => 'Nama', 'key' => 'nama_anak', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Anak/Wali (jika ada)', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir_anak', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Anak/Wali (jika ada)', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_anak', 'type' => 'date', 'is_required' => false],
            ['section' => 'Data Anak/Wali (jika ada)', 'label' => 'NIK', 'key' => 'nik_anak', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Anak/Wali (jika ada)', 'label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin_anak', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan'], 'is_required' => false],
            ['section' => 'Data Anak/Wali (jika ada)', 'label' => 'Pendidikan', 'key' => 'pendidikan_anak', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Anak/Wali (jika ada)', 'label' => 'Kelas/Semester', 'key' => 'kelas_semester_anak', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Anak/Wali (jika ada)', 'label' => 'Alamat', 'key' => 'alamat_anak', 'type' => 'textarea', 'is_required' => false],
        ]);

        $this->seedForCode('SKK', [ // Surat Keterangan Keramaian
            ['label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['label' => 'Status Perkawinan', 'key' => 'status_perkawinan', 'type' => 'select', 'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']],
            ['label' => 'Agama', 'key' => 'agama', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['label' => 'Pekerjaan', 'key' => 'pekerjaan', 'type' => 'text'],
            ['label' => 'Pergi Ke', 'key' => 'pergi_ke', 'type' => 'text'],
            ['label' => 'Keperluan', 'key' => 'keperluan', 'type' => 'textarea'],
            ['label' => 'Untuk Kegiatan/Acara', 'key' => 'untuk_kegiatan_acara', 'type' => 'text'],
            ['label' => 'Pada Hari/Tanggal', 'key' => 'pada_hari_tanggal', 'type' => 'date'],
            ['label' => 'Jam', 'key' => 'jam', 'type' => 'text'],
            ['label' => 'Tempat', 'key' => 'tempat', 'type' => 'text'],
            ['label' => 'Peserta', 'key' => 'peserta', 'type' => 'number'],
            ['label' => 'Tujuan', 'key' => 'tujuan', 'type' => 'textarea'],
            ['label' => 'Penanggung Jawab', 'key' => 'penanggung_jawab', 'type' => 'text'],
        ]);

        $this->seedForCode('SKJ', [ // Surat Keterangan Jalan 
            ['label' => 'Tempat Lahir', 'key' => 'tempat_lahir', 'type' => 'text'],
            ['label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir', 'type' => 'date'],
            ['label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['label' => 'Status Perkawinan', 'key' => 'status_perkawinan', 'type' => 'select', 'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']],
            ['label' => 'Agama', 'key' => 'agama', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['label' => 'Pekerjaan', 'key' => 'pekerjaan', 'type' => 'text'],
            ['label' => 'Pergi Ke', 'key' => 'pergi_ke', 'type' => 'text'],
            ['label' => 'Maksud dan Tujuan', 'key' => 'maksud_dan_tujuan', 'type' => 'textarea'],
        ]);

        $this->seedForCode('SKCK', [ // Surat Keterangan Mohon SKCK
            ['label' => 'Tempat Lahir', 'key' => 'tempat_lahir', 'type' => 'text'],
            ['label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir', 'type' => 'date'],
            ['label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['label' => 'Status Perkawinan', 'key' => 'status_perkawinan', 'type' => 'select', 'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']],
            ['label' => 'Agama', 'key' => 'agama', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['label' => 'Pekerjaan', 'key' => 'pekerjaan', 'type' => 'text'],
            ['label' => 'Pergi Ke', 'key' => 'pergi_ke', 'type' => 'text'],
            ['label' => 'Keperluan', 'key' => 'keperluan', 'type' => 'textarea'],
        ]);

    // SURAT PERMOHONAN
        $this->seedForCode('SPKTP', [ // Surat permohonan KTP
            ['label' => 'Pemerintah Provinsi', 'key' => 'pemerintah_provinsi', 'type' => 'text'],
            ['label' => 'Pemerintah Kabupaten', 'key' => 'pemerintah_kabupaten', 'type' => 'text'],
            ['label' => 'Kecamatan', 'key' => 'kecamatan', 'type' => 'text'],
            ['label' => 'Desa', 'key' => 'desa', 'type' => 'text'],
            ['label' => 'Jenis Permohonan KTP', 'key' => 'jenis_permohonan_ktp', 'type' => 'select', 'options' => ['Baru', 'Perpanjangan', 'Penggantian']],
            ['label' => 'Nomor Kartu Keluarga', 'key' => 'nomor_kk', 'type' => 'text'],
            ['label' => 'RT', 'key' => 'rt', 'type' => 'text'],
            ['label' => 'RW', 'key' => 'rw', 'type' => 'text'],
            ['label' => 'Spesimen Tanda Tangan', 'key' => 'spesimen_tanda_tangan', 'type' => 'select', 'options' => ['Cap Jempol', 'Tanda Tangan']],
        ]);
            
        $this->seedForCode('SPKIA', [ // Surat permohonan penerbitan KIA
            // Data Anak
            ['section' => 'Data Anak', 'label' => 'NIK Anak', 'key' => 'nik_anak', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Nama Anak', 'key' => 'nama_anak', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir_anak', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_anak', 'type' => 'date'],
            ['section' => 'Data Anak', 'label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin_anak', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['section' => 'Data Anak', 'label' => 'Golongan Darah', 'key' => 'golongan_darah_anak', 'type' => 'select', 'options' => ['A', 'B', 'AB', 'O', 'Tidak Tahu'], 'is_required' => false],
            ['section' => 'Data Anak', 'label' => 'Nomor Kartu Keluarga', 'key' => 'nomor_kk', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Nama Kepala Keluarga', 'key' => 'nama_kepala_keluarga', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Nomor Akta Kelahiran', 'key' => 'nomor_akta_kelahiran', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Anak', 'label' => 'Agama', 'key' => 'agama_anak', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Anak', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan', 'type' => 'text'],
            // Alamat
            ['section' => 'Alamat', 'label' => 'Alamat', 'key' => 'alamat_anak', 'type' => 'textarea'],
            ['section' => 'Alamat', 'label' => 'RT', 'key' => 'rt', 'type' => 'text'],
            ['section' => 'Alamat', 'label' => 'RW', 'key' => 'rw', 'type' => 'text'],
            ['section' => 'Alamat', 'label' => 'Kelurahan', 'key' => 'kelurahan', 'type' => 'text'],
            ['section' => 'Alamat', 'label' => 'Kecamatan', 'key' => 'kecamatan', 'type' => 'text'], 
            // Tanda Bukti Penerimaan
            ['section' => 'Tanda Bukti Penerimaan', 'label' => 'Tanggal Pengambilan', 'key' => 'tgl_pengambilan', 'type' => 'date', 'is_required' => false],
        ]);

        $this->seedForCode('SRBBM', [ // Surat Rekomendasi Pembelian Jenis BBM Tertentu
            ['label' => 'Alamat Usaha', 'key' => 'alamat_usaha', 'type' => 'textarea'],
            ['label' => 'Konsumen (Jenis BBM)', 'key' => 'konsumen_jenis_bbm', 'type' => 'text'],
            ['label' => 'Jenis Usaha Kegiatan', 'key' => 'jenis_usaha_kegiatan', 'type' => 'text'],
            ['section' => 'Data Penyaluran BBM', 'label' => 'Pengecer Solar', 'key' => 'pengecer_solar', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Penyaluran BBM', 'label' => 'BBM Jenis Tertentu', 'key' => 'bbm_jenis_tertentu', 'type' => 'text'],
            ['section' => 'Data Penyaluran BBM', 'label' => 'Konsumsi (Liter/Jam, Harian/Mingguan/Bulanan)', 'key' => 'konsumsi_bbm', 'type' => 'textarea'],
            ['section' => 'Data Penyaluran BBM', 'label' => 'Jumlah', 'key' => 'jumlah', 'type' => 'number'],
            ['section' => 'Data Penyaluran BBM', 'label' => 'Alokasi Volume (Liter per Hari/Minggu/Bulan)', 'key' => 'alokasi_volume', 'type' => 'text'],
            ['section' => 'Data Penyaluran BBM', 'label' => 'Tempat Pengambilan', 'key' => 'tempat_pengambilan', 'type' => 'text'],
            ['section' => 'Data Penyaluran BBM', 'label' => 'Nomor Lembaga Penyalur', 'key' => 'nomor_lembaga_penyalur', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Penyaluran BBM', 'label' => 'Lokasi', 'key' => 'lokasi', 'type' => 'text'],
        ]);

        $this->seedForCode('SPPWNI', [ // Surat Permohonan Pindah WNI 
            // Data Daerah Asal
            ['section' => 'Data Daerah Asal', 'label' => 'Nomor Kartu Keluarga', 'key' => 'nomor_kk', 'type' => 'text'],
            ['section' => 'Data Daerah Asal', 'label' => 'Nama Kepala Keluarga', 'key' => 'nama_kepala_keluarga', 'type' => 'text'],
            ['section' => 'Data Daerah Asal', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir', 'type' => 'text'],
            ['section' => 'Data Daerah Asal', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir', 'type' => 'date'],
            ['section' => 'Data Daerah Asal', 'label' => 'Alamat Asal', 'key' => 'alamat_asal', 'type' => 'textarea'],
            ['section' => 'Data Daerah Asal', 'label' => 'RT Asal', 'key' => 'rt_asal', 'type' => 'text'],
            ['section' => 'Data Daerah Asal', 'label' => 'RW Asal', 'key' => 'rw_asal', 'type' => 'text'],
            ['section' => 'Data Daerah Asal', 'label' => 'Dusun/Dukuh/Kampung Asal', 'key' => 'dusun_asal', 'type' => 'text'],
            ['section' => 'Data Daerah Asal', 'label' => 'Desa/Kelurahan Asal', 'key' => 'desa_asal', 'type' => 'text'],
            ['section' => 'Data Daerah Asal', 'label' => 'Kecamatan Asal', 'key' => 'kecamatan_asal', 'type' => 'text'],
            ['section' => 'Data Daerah Asal', 'label' => 'Kabupaten/Kota Asal', 'key' => 'kabupaten_asal', 'type' => 'text'],
            ['section' => 'Data Daerah Asal', 'label' => 'Provinsi Asal', 'key' => 'provinsi_asal', 'type' => 'text'],
            ['section' => 'Data Daerah Asal', 'label' => 'Kode Pos Asal', 'key' => 'kode_pos_asal', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Daerah Asal', 'label' => 'Telepon Asal', 'key' => 'telepon_asal', 'type' => 'text', 'is_required' => false],
            // Data Kepindahan
            ['section' => 'Data Kepindahan', 'label' => 'Alasan Pindah', 'key' => 'alasan_pindah', 'type' => 'select', 'options' => ['Pekerjaan', 'Pendidikan', 'Keamanan', 'Kesehatan', 'Perumahan', 'Keluarga', 'Lainnya']],
            ['section' => 'Data Kepindahan', 'label' => 'Alasan Pindah (Lainnya, sebutkan)', 'key' => 'alasan_pindah_lainnya', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Kepindahan', 'label' => 'Alamat Tujuan Pindah', 'key' => 'alamat_tujuan', 'type' => 'textarea'],
            ['section' => 'Data Kepindahan', 'label' => 'RT Tujuan', 'key' => 'rt_tujuan', 'type' => 'text'],
            ['section' => 'Data Kepindahan', 'label' => 'RW Tujuan', 'key' => 'rw_tujuan', 'type' => 'text'],
            ['section' => 'Data Kepindahan', 'label' => 'Dusun/Dukuh/Kampung Tujuan', 'key' => 'dusun_tujuan', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Kepindahan', 'label' => 'Desa/Kelurahan Tujuan', 'key' => 'desa_tujuan', 'type' => 'text'],
            ['section' => 'Data Kepindahan', 'label' => 'Kecamatan Tujuan', 'key' => 'kecamatan_tujuan', 'type' => 'text'],
            ['section' => 'Data Kepindahan', 'label' => 'Kabupaten/Kota Tujuan', 'key' => 'kabupaten_tujuan', 'type' => 'text'],
            ['section' => 'Data Kepindahan', 'label' => 'Provinsi Tujuan', 'key' => 'provinsi_tujuan', 'type' => 'text'],
            ['section' => 'Data Kepindahan', 'label' => 'Kode Pos Tujuan', 'key' => 'kode_pos_tujuan', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Kepindahan', 'label' => 'Telepon Tujuan', 'key' => 'telepon_tujuan', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Kepindahan', 'label' => 'Jenis Kepindahan', 'key' => 'jenis_kepindahan', 'type' => 'select', 'options' => ['Kepala Keluarga', 'Kepala Keluarga dan Seluruh Anggota Keluarga', 'Kepala Keluarga dan sebagian Anggota Keluarga', 'Anggota Keluarga']],
            ['section' => 'Data Kepindahan', 'label' => 'Status KK bagi yang Tidak Pindah', 'key' => 'status_kk_tidak_pindah', 'type' => 'select', 'options' => ['Numpang KK', 'Membuat KK Baru', 'Nomor KK Tetap']],
            ['section' => 'Data Kepindahan', 'label' => 'Status Nomor KK bagi yang Pindah', 'key' => 'status_kk_pindah', 'type' => 'select', 'options' => ['Numpang KK', 'Membuat KK Baru', 'Nomor KK Tetap']],
            // Keluarga yang Pindah (maks 7 orang sesuai form fisik; masih pola statis, belum "repeater")
            ['section' => 'Keluarga yang Pindah', 'label' => 'NIK 1', 'key' => 'nik_pindah_1', 'type' => 'text', 'is_required' => false],
            ['section' => 'Keluarga yang Pindah', 'label' => 'Nama 1', 'key' => 'nama_pindah_1', 'type' => 'text', 'is_required' => false],
            ['section' => 'Keluarga yang Pindah', 'label' => 'Masa Berlaku KTP s/d 1', 'key' => 'masa_berlaku_ktp_1', 'type' => 'text', 'is_required' => false],
            ['section' => 'Keluarga yang Pindah', 'label' => 'SHDK 1', 'key' => 'shdk_1', 'type' => 'text', 'is_required' => false],
        
            ['section' => 'Keluarga yang Pindah', 'label' => 'NIK 2', 'key' => 'nik_pindah_2', 'type' => 'text', 'is_required' => false],
            ['section' => 'Keluarga yang Pindah', 'label' => 'Nama 2', 'key' => 'nama_pindah_2', 'type' => 'text', 'is_required' => false],
            ['section' => 'Keluarga yang Pindah', 'label' => 'Masa Berlaku KTP s/d 2', 'key' => 'masa_berlaku_ktp_2', 'type' => 'text', 'is_required' => false],
            ['section' => 'Keluarga yang Pindah', 'label' => 'SHDK 2', 'key' => 'shdk_2', 'type' => 'text', 'is_required' => false],
        
            ['section' => 'Keluarga yang Pindah', 'label' => 'NIK 3', 'key' => 'nik_pindah_3', 'type' => 'text', 'is_required' => false],
            ['section' => 'Keluarga yang Pindah', 'label' => 'Nama 3', 'key' => 'nama_pindah_3', 'type' => 'text', 'is_required' => false],
            ['section' => 'Keluarga yang Pindah', 'label' => 'Masa Berlaku KTP s/d 3', 'key' => 'masa_berlaku_ktp_3', 'type' => 'text', 'is_required' => false],
            ['section' => 'Keluarga yang Pindah', 'label' => 'SHDK 3', 'key' => 'shdk_3', 'type' => 'text', 'is_required' => false],
        
            ['section' => 'Keluarga yang Pindah', 'label' => 'NIK 4', 'key' => 'nik_pindah_4', 'type' => 'text', 'is_required' => false],
            ['section' => 'Keluarga yang Pindah', 'label' => 'Nama 4', 'key' => 'nama_pindah_4', 'type' => 'text', 'is_required' => false],
            ['section' => 'Keluarga yang Pindah', 'label' => 'Masa Berlaku KTP s/d 4', 'key' => 'masa_berlaku_ktp_4', 'type' => 'text', 'is_required' => false],
            ['section' => 'Keluarga yang Pindah', 'label' => 'SHDK 4', 'key' => 'shdk_4', 'type' => 'text', 'is_required' => false],
        
            ['section' => 'Keluarga yang Pindah', 'label' => 'NIK 5', 'key' => 'nik_pindah_5', 'type' => 'text', 'is_required' => false],
            ['section' => 'Keluarga yang Pindah', 'label' => 'Nama 5', 'key' => 'nama_pindah_5', 'type' => 'text', 'is_required' => false],
            ['section' => 'Keluarga yang Pindah', 'label' => 'Masa Berlaku KTP s/d 5', 'key' => 'masa_berlaku_ktp_5', 'type' => 'text', 'is_required' => false],
            ['section' => 'Keluarga yang Pindah', 'label' => 'SHDK 5', 'key' => 'shdk_5', 'type' => 'text', 'is_required' => false],
        ]);

        $this->seedForCode('SGC', [ // Surat permohonan Cerai
            // Data (penggugat)
            ['section' => 'Data Penggugat', 'label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin_penggugat', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['section' => 'Data Penggugat', 'label' => 'Nama', 'key' => 'nama_penggugat', 'type' => 'text'],
            ['section' => 'Data Penggugat', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir_penggugat', 'type' => 'text'],
            ['section' => 'Data Penggugat', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_penggugat', 'type' => 'date'],
            ['section' => 'Data Penggugat', 'label' => 'Agama', 'key' => 'agama_penggugat', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Penggugat', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_penggugat', 'type' => 'text'],
            ['section' => 'Data Penggugat', 'label' => 'Alamat', 'key' => 'alamat_penggugat', 'type' => 'textarea'],
            // Data suami/istri (tergugat)
            ['section' => 'Data Tergugat', 'label' => 'Nama', 'key' => 'nama_tergugat', 'type' => 'text'],
            ['section' => 'Data Tergugat', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir_tergugat', 'type' => 'text'],
            ['section' => 'Data Tergugat', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_tergugat', 'type' => 'date'],
            ['section' => 'Data Tergugat', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_tergugat', 'type' => 'text'],
            ['section' => 'Data Tergugat', 'label' => 'Alamat', 'key' => 'alamat_tergugat', 'type' => 'textarea'],
            // Data surat nikah
            ['section' => 'Data Pernikahan', 'label' => 'Nomor Surat Nikah', 'key' => 'nomor_surat_nikah', 'type' => 'text'],
            ['section' => 'Data Pernikahan', 'label' => 'Kantor Pencatat Nikah', 'key' => 'kantor_pencatat_nikah', 'type' => 'text'],
            ['section' => 'Data Pernikahan', 'label' => 'Tanggal Nikah', 'key' => 'tanggal_nikah', 'type' => 'date'],
            // Alasan gugatan 
            ['section' => 'Alasan Gugatan', 'label' => 'Alasan Mengajukan Gugat Cerai', 'key' => 'alasan_gugat_cerai', 'type' => 'textarea'],
            // Saksi 1
            ['section' => 'Saksi 1', 'label' => 'Nama', 'key' => 'nama_saksi_1', 'type' => 'text'],
            ['section' => 'Saksi 1', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir_saksi_1', 'type' => 'text'],
            ['section' => 'Saksi 1', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_saksi_1', 'type' => 'date'],
            ['section' => 'Saksi 1', 'label' => 'Agama', 'key' => 'agama_saksi_1', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Saksi 1', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_saksi_1', 'type' => 'text'],
            ['section' => 'Saksi 1', 'label' => 'Alamat', 'key' => 'alamat_saksi_1', 'type' => 'textarea'],
            // Saksi 2
            ['section' => 'Saksi 2', 'label' => 'Nama', 'key' => 'nama_saksi_2', 'type' => 'text'],
            ['section' => 'Saksi 2', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir_saksi_2', 'type' => 'text'],
            ['section' => 'Saksi 2', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_saksi_2', 'type' => 'date'],
            ['section' => 'Saksi 2', 'label' => 'Agama', 'key' => 'agama_saksi_2', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Saksi 2', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_saksi_2', 'type' => 'text'],
            ['section' => 'Saksi 2', 'label' => 'Alamat', 'key' => 'alamat_saksi_2', 'type' => 'textarea'],
        ]);

    // SURAT PERINTAH
        $this->seedForCode('SPPD', [ // Surat Perintah Perjalanan Dinas
            ['label' => 'Pejabat yang Berwenang Memberi Perintah', 'key' => 'pejabat_berwenang', 'type' => 'text'],
            ['label' => 'Nama Pegawai yang Diperintah', 'key' => 'nama_pegawai', 'type' => 'text'],
            ['label' => 'Pangkat/Golongan', 'key' => 'pangkat_golongan', 'type' => 'text'],
            ['label' => 'Jabatan', 'key' => 'jabatan', 'type' => 'text'],
            ['label' => 'Maksud Perjalanan Dinas', 'key' => 'maksud_perjalanan', 'type' => 'textarea'],
            ['label' => 'Alat Angkutan yang Digunakan', 'key' => 'alat_angkutan', 'type' => 'text'],
            ['label' => 'Tempat Berangkat', 'key' => 'tempat_berangkat', 'type' => 'text'],
            ['label' => 'Tempat Tujuan', 'key' => 'tempat_tujuan', 'type' => 'text'],
            ['label' => 'Tanggal Berangkat', 'key' => 'tanggal_berangkat', 'type' => 'date'],
            ['label' => 'Tanggal Harus Kembali', 'key' => 'tanggal_kembali', 'type' => 'date'],
            ['label' => 'Pengikut', 'key' => 'pengikut', 'type' => 'text'],
            ['label' => 'Pembebanan Anggaran (Instansi)', 'key' => 'anggaran_instansi', 'type' => 'text'],
            ['label' => 'Pembebanan Anggaran (Mata Anggaran)', 'key' => 'anggaran_mata_anggaran', 'type' => 'text'],
            ['label' => 'Keterangan Lain-lain', 'key' => 'keterangan_lain', 'type' => 'textarea'],
        ]);

    // SURAT PERNYATAAN
        $this->seedForCode('SPBNI', [ // Surat Pernyataan Beda Nama/Identitas
            ['section' => 'Perbedaan Nama/NIK/Alamat', 'label' => 'Perbedaan pada Nama', 'key' => 'perbedaan_nama', 'type' => 'text', 'is_required' => false],
            ['section' => 'Perbedaan Nama/NIK/Alamat', 'label' => 'Perbedaan pada Alamat', 'key' => 'perbedaan_alamat', 'type' => 'text', 'is_required' => false],
            ['section' => 'Perbedaan Nama/NIK/Alamat', 'label' => 'Perbedaan pada NIK', 'key' => 'perbedaan_nik', 'type' => 'text', 'is_required' => false],
        ]);

        $this->seedForCode('SPTMDK', [ // Surat Pernyataan Tidak Memiliki Dokumen Kependudukan
            ['label' => 'Tempat Lahir', 'key' => 'tempat_lahir', 'type' => 'text'],
            ['label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir', 'type' => 'date'],
            ['label' => 'Nama Ibu', 'key' => 'nama_ibu', 'type' => 'text'],
            ['label' => 'Nama Ayah', 'key' => 'nama_ayah', 'type' => 'text'],
        ]);

        $this->seedForCode('SKDPAK', [ // Surat Kuasa Dalam Pelayanan Administrasi Kependudukan
            ['section' => 'Pemberi Kuasa', 'label' => 'Nama', 'key' => 'nama_pemberi_kuasa', 'type' => 'text'],
            ['section' => 'Pemberi Kuasa', 'label' => 'NIK', 'key' => 'nik_pemberi_kuasa', 'type' => 'text'],
            ['section' => 'Pemberi Kuasa', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir_pemberi_kuasa', 'type' => 'text'],
            ['section' => 'Pemberi Kuasa', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_pemberi_kuasa', 'type' => 'date'],
            ['section' => 'Pemberi Kuasa', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_pemberi_kuasa', 'type' => 'text'],
            ['section' => 'Pemberi Kuasa', 'label' => 'Alamat', 'key' => 'alamat_pemberi_kuasa', 'type' => 'textarea'],
        
            ['section' => 'Penerima Kuasa', 'label' => 'Nama', 'key' => 'nama_penerima_kuasa', 'type' => 'text'],
            ['section' => 'Penerima Kuasa', 'label' => 'NIK', 'key' => 'nik_penerima_kuasa', 'type' => 'text'],
            ['section' => 'Penerima Kuasa', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir_penerima_kuasa', 'type' => 'text'],
            ['section' => 'Penerima Kuasa', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_penerima_kuasa', 'type' => 'date'],
            ['section' => 'Penerima Kuasa', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_penerima_kuasa', 'type' => 'text'],
            ['section' => 'Penerima Kuasa', 'label' => 'Alamat', 'key' => 'alamat_penerima_kuasa', 'type' => 'textarea'],
            ['section' => 'Penerima Kuasa', 'label' => 'RT', 'key' => 'rt_penerima_kuasa', 'type' => 'text'],
            ['section' => 'Penerima Kuasa', 'label' => 'RW', 'key' => 'rw_penerima_kuasa', 'type' => 'text'],
        
            ['label' => 'Alasan/Kondisi Pemberian Kuasa', 'key' => 'alasan_kuasa', 'type' => 'textarea'],
        ]);

    // SURAT BALASAN 
        $this->seedForCode('SMLPI', [ // Surat Menindaklanjuti Permohonan Izin
            ['label' => 'Nama Organisasi/Kegiatan Pemohon', 'key' => 'nama_organisasi', 'type' => 'text'],
            ['label' => 'Pangkalan/Instansi Pemohon', 'key' => 'pangkalan_instansi', 'type' => 'text', 'is_required' => false],
            ['label' => 'Ditujukan Kepada (Nama/Jabatan)', 'key' => 'ditujukan_kepada', 'type' => 'text'],
            ['label' => 'Lokasi Tujuan Surat', 'key' => 'lokasi_tujuan_surat', 'type' => 'text', 'is_required' => false],
            ['label' => 'Nomor Surat yang Ditindaklanjuti', 'key' => 'nomor_surat_asal', 'type' => 'text'],
            ['label' => 'Perihal Surat yang Ditindaklanjuti', 'key' => 'perihal_surat_asal', 'type' => 'text'],
            ['label' => 'Hari', 'key' => 'hari_kegiatan', 'type' => 'text'],
            ['label' => 'Tanggal Kegiatan', 'key' => 'tanggal_kegiatan', 'type' => 'text'],
            ['label' => 'Waktu', 'key' => 'waktu_kegiatan', 'type' => 'text'],
            ['label' => 'Acara/Kegiatan', 'key' => 'nama_acara', 'type' => 'textarea'],
            ['label' => 'Tempat Kegiatan', 'key' => 'tempat_kegiatan', 'type' => 'text'],
            ['label' => 'Keputusan', 'key' => 'keputusan', 'type' => 'select', 'options' => ['Mengizinkan', 'Tidak Mengizinkan']],
            ['label' => 'Tembusan', 'key' => 'tembusan', 'type' => 'textarea', 'is_required' => false],
        ]);

        $this->seedForCode('SPSKG', [ // Surat Penawaran Sewa Kontrak Gedung
            ['label' => 'Hal/Perihal', 'key' => 'perihal', 'type' => 'text'],
            ['label' => 'Ditujukan Kepada (Nama/Jabatan)', 'key' => 'ditujukan_kepada', 'type' => 'text'],
            ['label' => 'Lokasi Tujuan Surat', 'key' => 'lokasi_tujuan_surat', 'type' => 'text', 'is_required' => false],
            ['label' => 'Nama Gedung/Aset yang Disewakan', 'key' => 'nama_gedung', 'type' => 'text'],
            ['label' => 'Alamat Gedung/Aset', 'key' => 'alamat_gedung', 'type' => 'textarea'],
            ['label' => 'Tanggal Berakhir Masa Sewa Sebelumnya', 'key' => 'tanggal_berakhir_sewa_lama', 'type' => 'date', 'is_required' => false],
            ['label' => 'Harga Sewa per Tahun (Rp)', 'key' => 'harga_sewa_per_tahun', 'type' => 'number'],
            ['label' => 'Lama Sewa (Tahun)', 'key' => 'lama_sewa_tahun', 'type' => 'number'],
        ]);

        $this->seedForCode('SBP', [ // Surat Balasan Penelitian
            ['label' => 'Ditujukan Kepada (Universitas/Instansi)', 'key' => 'ditujukan_kepada', 'type' => 'text'],
            ['label' => 'Nomor Surat yang Ditindaklanjuti', 'key' => 'nomor_surat_asal', 'type' => 'text'],
            ['label' => 'Nama Mahasiswa', 'key' => 'nama_mahasiswa', 'type' => 'text'],
            ['label' => 'NIM', 'key' => 'nim', 'type' => 'text'],
            ['label' => 'Program Studi', 'key' => 'program_studi', 'type' => 'text'],
            ['label' => 'Fakultas', 'key' => 'fakultas', 'type' => 'text'],
        ]);

    // SURAT PENGANTAR
        $this->seedForCode('SKDN', [ // Surat Pengantar Duplikat Nikah
            ['label' => 'Tempat Lahir', 'key' => 'tempat_lahir', 'type' => 'text'],
            ['label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir', 'type' => 'date'],
            ['label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['label' => 'Agama', 'key' => 'agama', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['label' => 'Status Perkawinan', 'key' => 'status_perkawinan', 'type' => 'select', 'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']],
            ['label' => 'Pekerjaan', 'key' => 'pekerjaan', 'type' => 'text'],
            ['label' => 'Tujuan', 'key' => 'tujuan', 'type' => 'text'],
            ['label' => 'Keperluan', 'key' => 'keperluan', 'type' => 'textarea'],
        ]);

        $this->seedForCode('SPU', [ // Surat Pengantar Umum
            ['label' => 'Tempat Lahir', 'key' => 'tempat_lahir', 'type' => 'text'],
            ['label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir', 'type' => 'date'],
            ['label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['label' => 'Status Perkawinan', 'key' => 'status_perkawinan', 'type' => 'select', 'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']],
            ['label' => 'Agama', 'key' => 'agama', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['label' => 'Pekerjaan', 'key' => 'pekerjaan', 'type' => 'text'],
            ['label' => 'Pergi Ke', 'key' => 'pergi_ke', 'type' => 'text'],
            ['label' => 'Keperluan', 'key' => 'keperluan', 'type' => 'textarea'],
        ]);

    // PERNIKAHAN PEREMPUAN
        $this->seedForCode('PNP', [ // Pendaftaran Nikah Perempuan
            // Data Akad Nikah
            ['section' => 'Data Akad Nikah', 'label' => 'Hari Akad', 'key' => 'hari_akad', 'type' => 'text'],
            ['section' => 'Data Akad Nikah', 'label' => 'Tanggal Akad', 'key' => 'tanggal_akad', 'type' => 'date'],
            ['section' => 'Data Akad Nikah', 'label' => 'Jam Akad', 'key' => 'jam_akad', 'type' => 'text'],
            ['section' => 'Data Akad Nikah', 'label' => 'Tempat Akad', 'key' => 'tempat_akad', 'type' => 'text'],
            // Data Calon Pengantin Putri
            ['section' => 'Data Catin Putri', 'label' => 'Nama', 'key' => 'nama_catin_putri', 'type' => 'text'],
            ['section' => 'Data Catin Putri', 'label' => 'NIK', 'key' => 'nik_catin_putri', 'type' => 'text'],
            ['section' => 'Data Catin Putri', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir_catin_putri', 'type' => 'text'],
            ['section' => 'Data Catin Putri', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_catin_putri', 'type' => 'date'],
            ['section' => 'Data Catin Putri', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_catin_putri', 'type' => 'text'],
            ['section' => 'Data Catin Putri', 'label' => 'Agama', 'key' => 'agama_catin_putri', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Catin Putri', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_catin_putri', 'type' => 'text'],
            ['section' => 'Data Catin Putri', 'label' => 'Pendidikan Terakhir', 'key' => 'pendidikan_catin_putri', 'type' => 'text'],
            ['section' => 'Data Catin Putri', 'label' => 'Alamat', 'key' => 'alamat_catin_putri', 'type' => 'textarea'],
            ['section' => 'Data Catin Putri', 'label' => 'Status', 'key' => 'status_catin_putri', 'type' => 'select', 'options' => ['Perawan', 'Janda']],
            // Data Suami Terdahulu (hanya diisi jika status Catin Putri = Janda)
            ['section' => 'Data Suami Terdahulu (jika Janda)', 'label' => 'Nama Suami Terdahulu', 'key' => 'nama_suami_terdahulu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Suami Terdahulu (jika Janda)', 'label' => 'Bin/Nama Orang Tua Suami Terdahulu', 'key' => 'bin_suami_terdahulu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Suami Terdahulu (jika Janda)', 'label' => 'NIK Suami Terdahulu', 'key' => 'nik_suami_terdahulu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Suami Terdahulu (jika Janda)', 'label' => 'Tempat/Tanggal Lahir Suami Terdahulu', 'key' => 'ttl_suami_terdahulu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Suami Terdahulu (jika Janda)', 'label' => 'Kewarganegaraan Suami Terdahulu', 'key' => 'kewarganegaraan_suami_terdahulu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Suami Terdahulu (jika Janda)', 'label' => 'Agama Suami Terdahulu', 'key' => 'agama_suami_terdahulu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Suami Terdahulu (jika Janda)', 'label' => 'Pekerjaan Suami Terdahulu', 'key' => 'pekerjaan_suami_terdahulu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Suami Terdahulu (jika Janda)', 'label' => 'Alamat Suami Terdahulu', 'key' => 'alamat_suami_terdahulu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Suami Terdahulu (jika Janda)', 'label' => 'Meninggal Dunia Pada', 'key' => 'suami_terdahulu_meninggal_pada', 'type' => 'date', 'is_required' => false],
            ['section' => 'Data Suami Terdahulu (jika Janda)', 'label' => 'Meninggal Di', 'key' => 'suami_terdahulu_meninggal_di', 'type' => 'text', 'is_required' => false],
            // Data Ayah Catin Putri
            ['section' => 'Data Ayah Catin Putri', 'label' => 'Nama', 'key' => 'nama_ayah_putri', 'type' => 'text'],
            ['section' => 'Data Ayah Catin Putri', 'label' => 'Bin', 'key' => 'bin_ayah_putri', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ayah Catin Putri', 'label' => 'NIK', 'key' => 'nik_ayah_putri', 'type' => 'text'],
            ['section' => 'Data Ayah Catin Putri', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_ayah_putri', 'type' => 'text'],
            ['section' => 'Data Ayah Catin Putri', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ayah_putri', 'type' => 'text'],
            ['section' => 'Data Ayah Catin Putri', 'label' => 'Agama', 'key' => 'agama_ayah_putri', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Ayah Catin Putri', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ayah_putri', 'type' => 'text'],
            ['section' => 'Data Ayah Catin Putri', 'label' => 'Alamat', 'key' => 'alamat_ayah_putri', 'type' => 'textarea'],
            // Data Ibu Catin Putri
            ['section' => 'Data Ibu Catin Putri', 'label' => 'Nama', 'key' => 'nama_ibu_putri', 'type' => 'text'],
            ['section' => 'Data Ibu Catin Putri', 'label' => 'Binti', 'key' => 'binti_ibu_putri', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ibu Catin Putri', 'label' => 'NIK', 'key' => 'nik_ibu_putri', 'type' => 'text'],
            ['section' => 'Data Ibu Catin Putri', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_ibu_putri', 'type' => 'text'],
            ['section' => 'Data Ibu Catin Putri', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ibu_putri', 'type' => 'text'],
            ['section' => 'Data Ibu Catin Putri', 'label' => 'Agama', 'key' => 'agama_ibu_putri', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Ibu Catin Putri', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ibu_putri', 'type' => 'text'],
            ['section' => 'Data Ibu Catin Putri', 'label' => 'Alamat', 'key' => 'alamat_ibu_putri', 'type' => 'textarea'],
            // Data Wali Nikah (hanya diisi jika wali nasab bukan ayah kandung)
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Nama', 'key' => 'nama_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Bin', 'key' => 'bin_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'NIK', 'key' => 'nik_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Agama', 'key' => 'agama_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Alamat', 'key' => 'alamat_wali', 'type' => 'textarea', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Hubungan Wali', 'key' => 'hubungan_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Sebab Wali Bukan Ayah Kandung', 'key' => 'sebab_wali_bukan_ayah', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Jika Wali Hakim, Sebab Wali Hakim', 'key' => 'sebab_wali_hakim', 'type' => 'text', 'is_required' => false],
            // Data Calon Pengantin Putra
            ['section' => 'Data Catin Putra', 'label' => 'Nama', 'key' => 'nama_catin_putra', 'type' => 'text'],
            ['section' => 'Data Catin Putra', 'label' => 'Bin', 'key' => 'bin_catin_putra', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Catin Putra', 'label' => 'NIK', 'key' => 'nik_catin_putra', 'type' => 'text'],
            ['section' => 'Data Catin Putra', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir_catin_putra', 'type' => 'text'],
            ['section' => 'Data Catin Putra', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_catin_putra', 'type' => 'date'],
            ['section' => 'Data Catin Putra', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_catin_putra', 'type' => 'text'],
            ['section' => 'Data Catin Putra', 'label' => 'Agama', 'key' => 'agama_catin_putra', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Catin Putra', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_catin_putra', 'type' => 'text'],
            ['section' => 'Data Catin Putra', 'label' => 'Alamat', 'key' => 'alamat_catin_putra', 'type' => 'textarea'],
        ]);

        $this->seedForCode('N1P', [ // Pengantar Nikah Perempuan
            // Data Diri Catin
            ['section' => 'Data Catin', 'label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['section' => 'Data Catin', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir', 'type' => 'text'],
            ['section' => 'Data Catin', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir', 'type' => 'date'],
            ['section' => 'Data Catin', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan', 'type' => 'text'],
            ['section' => 'Data Catin', 'label' => 'Agama', 'key' => 'agama', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Catin', 'label' => 'Pekerjaan', 'key' => 'pekerjaan', 'type' => 'text'],
            ['section' => 'Data Catin', 'label' => 'Pendidikan Terakhir', 'key' => 'pendidikan_terakhir', 'type' => 'text'],
            // Status Pernikahan
            ['section' => 'Status Pernikahan', 'label' => 'Status', 'key' => 'status_pernikahan', 'type' => 'select', 'options' => ['Jejaka', 'Duda', 'Beristri Lagi', 'Perawan', 'Janda']],
            ['section' => 'Status Pernikahan', 'label' => 'Nama Isteri/Suami Terdahulu', 'key' => 'nama_pasangan_terdahulu', 'type' => 'text', 'is_required' => false],
            // Data Ayah
            ['section' => 'Data Ayah', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'NIK', 'key' => 'nik_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Agama', 'key' => 'agama_ayah', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Ayah', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Alamat', 'key' => 'alamat_ayah', 'type' => 'textarea'],
            // Data Ibu
            ['section' => 'Data Ibu', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'NIK', 'key' => 'nik_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Agama', 'key' => 'agama_ibu', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Ibu', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Alamat', 'key' => 'alamat_ibu', 'type' => 'textarea'],
        ]);

        $this->seedForCode('N2P', [ // Permohonan Kehendak Nikah
            ['label' => 'Ditujukan Kepada (Kepala KUA Kecamatan)', 'key' => 'kepala_kua_tujuan', 'type' => 'text'],
            ['label' => 'Nama Calon Suami', 'key' => 'nama_calon_suami', 'type' => 'text'],
            ['label' => 'Nama Calon Istri', 'key' => 'nama_calon_istri', 'type' => 'text'],
            ['label' => 'Hari/Tanggal Akad', 'key' => 'hari_tanggal_akad', 'type' => 'text'],
            ['label' => 'Jam Akad', 'key' => 'jam_akad', 'type' => 'text'],
            ['label' => 'Tempat Akad Nikah', 'key' => 'tempat_akad', 'type' => 'text'],
            ['label' => 'Daftar Surat/Berkas yang Dilampirkan (otomatis dari upload)', 'key' => 'daftar_berkas_terlampir', 'type' => 'textarea', 'is_required' => false],
        ]);

        $this->seedForCode('N4P', [ //Persetujuan Calon Pengantin
            // Calon Suami
            ['section' => 'Calon Suami', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_calon_suami', 'type' => 'text'],
            ['section' => 'Calon Suami', 'label' => 'Bin', 'key' => 'bin_calon_suami', 'type' => 'text', 'is_required' => false],
            ['section' => 'Calon Suami', 'label' => 'NIK', 'key' => 'nik_calon_suami', 'type' => 'text'],
            ['section' => 'Calon Suami', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_calon_suami', 'type' => 'text'],
            ['section' => 'Calon Suami', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_calon_suami', 'type' => 'text'],
            ['section' => 'Calon Suami', 'label' => 'Agama', 'key' => 'agama_calon_suami', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Calon Suami', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_calon_suami', 'type' => 'text'],
            ['section' => 'Calon Suami', 'label' => 'Alamat', 'key' => 'alamat_calon_suami', 'type' => 'textarea'],
            // Calon Istri
            ['section' => 'Calon Istri', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_calon_istri', 'type' => 'text'],
            ['section' => 'Calon Istri', 'label' => 'Binti', 'key' => 'binti_calon_istri', 'type' => 'text', 'is_required' => false],
            ['section' => 'Calon Istri', 'label' => 'NIK', 'key' => 'nik_calon_istri', 'type' => 'text'],
            ['section' => 'Calon Istri', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_calon_istri', 'type' => 'text'],
            ['section' => 'Calon Istri', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_calon_istri', 'type' => 'text'],
            ['section' => 'Calon Istri', 'label' => 'Agama', 'key' => 'agama_calon_istri', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Calon Istri', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_calon_istri', 'type' => 'text'],
            ['section' => 'Calon Istri', 'label' => 'Alamat', 'key' => 'alamat_calon_istri', 'type' => 'textarea'],
        ]);

        $this->seedForCode('N5P', [ // Surat Izin Orang Tua
            // Data Ayah
            ['section' => 'Data Ayah', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Bin', 'key' => 'bin_ayah', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ayah', 'label' => 'NIK', 'key' => 'nik_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Agama', 'key' => 'agama_ayah', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Ayah', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Alamat', 'key' => 'alamat_ayah', 'type' => 'textarea'],
            // Data Ibu
            ['section' => 'Data Ibu', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Binti', 'key' => 'binti_ibu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ibu', 'label' => 'NIK', 'key' => 'nik_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Agama', 'key' => 'agama_ibu', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Ibu', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Alamat', 'key' => 'alamat_ibu', 'type' => 'textarea'],
            // Data Anak yang Diberi Izin
            ['section' => 'Data Anak', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_anak', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Bin/Binti', 'key' => 'bin_binti_anak', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Anak', 'label' => 'NIK', 'key' => 'nik_anak', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_anak', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_anak', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Agama', 'key' => 'agama_anak', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Anak', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_anak', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Alamat', 'key' => 'alamat_anak', 'type' => 'textarea'],
            // Data Calon Pasangan
            ['section' => 'Data Calon Pasangan', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_calon_pasangan', 'type' => 'text'],
            ['section' => 'Data Calon Pasangan', 'label' => 'Bin/Binti', 'key' => 'bin_binti_calon_pasangan', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Calon Pasangan', 'label' => 'NIK', 'key' => 'nik_calon_pasangan', 'type' => 'text'],
            ['section' => 'Data Calon Pasangan', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_calon_pasangan', 'type' => 'text'],
            ['section' => 'Data Calon Pasangan', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_calon_pasangan', 'type' => 'text'],
            ['section' => 'Data Calon Pasangan', 'label' => 'Agama', 'key' => 'agama_calon_pasangan', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Calon Pasangan', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_calon_pasangan', 'type' => 'text'],
            ['section' => 'Data Calon Pasangan', 'label' => 'Alamat', 'key' => 'alamat_calon_pasangan', 'type' => 'textarea'],
        ]);

        $this->seedForCode('N6P', [ // Surat Keterangan Kematian
            // Data Almarhum/Almarhumah
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_almarhum', 'type' => 'text'],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Bin/Binti', 'key' => 'bin_binti_almarhum', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'NIK', 'key' => 'nik_almarhum', 'type' => 'text'],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_almarhum', 'type' => 'text'],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_almarhum', 'type' => 'text'],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Agama', 'key' => 'agama_almarhum', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_almarhum', 'type' => 'text'],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Alamat', 'key' => 'alamat_almarhum', 'type' => 'textarea'],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Tanggal Meninggal Dunia', 'key' => 'tanggal_meninggal', 'type' => 'date'],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Tempat Meninggal', 'key' => 'tempat_meninggal', 'type' => 'text'],
            // Data Pasangan yang Ditinggalkan
            ['section' => 'Data Pasangan yang Ditinggalkan', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_pasangan', 'type' => 'text'],
            ['section' => 'Data Pasangan yang Ditinggalkan', 'label' => 'Bin/Binti', 'key' => 'bin_binti_pasangan', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Pasangan yang Ditinggalkan', 'label' => 'NIK', 'key' => 'nik_pasangan', 'type' => 'text'],
            ['section' => 'Data Pasangan yang Ditinggalkan', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_pasangan', 'type' => 'text'],
            ['section' => 'Data Pasangan yang Ditinggalkan', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_pasangan', 'type' => 'text'],
            ['section' => 'Data Pasangan yang Ditinggalkan', 'label' => 'Agama', 'key' => 'agama_pasangan', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Pasangan yang Ditinggalkan', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_pasangan', 'type' => 'text'],
            ['section' => 'Data Pasangan yang Ditinggalkan', 'label' => 'Alamat', 'key' => 'alamat_pasangan', 'type' => 'textarea'],
        ]);

        $this->seedForCode('SKWNP', [ // Surat Keterangan Wali Nikah 
            // Data Wali
            ['section' => 'Data Wali', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_wali', 'type' => 'text'],
            ['section' => 'Data Wali', 'label' => 'Bin', 'key' => 'bin_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali', 'label' => 'NIK', 'key' => 'nik_wali', 'type' => 'text'],
            ['section' => 'Data Wali', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_wali', 'type' => 'text'],
            ['section' => 'Data Wali', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_wali', 'type' => 'text'],
            ['section' => 'Data Wali', 'label' => 'Agama', 'key' => 'agama_wali', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Wali', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_wali', 'type' => 'text'],
            ['section' => 'Data Wali', 'label' => 'Alamat', 'key' => 'alamat_wali', 'type' => 'textarea'],
            // Data Calon Mempelai Wanita
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Binti', 'key' => 'binti_mempelai_wanita', 'type' => 'text', 'is_required' => false],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'NIK', 'key' => 'nik_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Agama', 'key' => 'agama_mempelai_wanita', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Alamat', 'key' => 'alamat_mempelai_wanita', 'type' => 'textarea'],
            // Data Calon Mempelai Pria
            ['section' => 'Calon Mempelai Pria', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Pria', 'label' => 'Bin', 'key' => 'bin_mempelai_pria', 'type' => 'text', 'is_required' => false],
            ['section' => 'Calon Mempelai Pria', 'label' => 'NIK', 'key' => 'nik_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Pria', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Pria', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Pria', 'label' => 'Agama', 'key' => 'agama_mempelai_pria', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Calon Mempelai Pria', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Pria', 'label' => 'Alamat', 'key' => 'alamat_mempelai_pria', 'type' => 'textarea'],
        
            ['label' => 'Hubungan Wali Nasab dengan Calon Mempelai Wanita', 'key' => 'hubungan_wali_nasab', 'type' => 'text'],
        ]);

        $this->seedForCode('SKWHP', [ // Surat Keterangan Wali Hakim 
            // Calon Mempelai Wanita
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Binti', 'key' => 'binti_mempelai_wanita', 'type' => 'text', 'is_required' => false],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'NIK', 'key' => 'nik_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Agama', 'key' => 'agama_mempelai_wanita', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Alamat', 'key' => 'alamat_mempelai_wanita', 'type' => 'textarea'],
            // Calon Mempelai Laki-laki
            ['section' => 'Calon Mempelai Laki-laki', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Laki-laki', 'label' => 'Bin', 'key' => 'bin_mempelai_pria', 'type' => 'text', 'is_required' => false],
            ['section' => 'Calon Mempelai Laki-laki', 'label' => 'NIK', 'key' => 'nik_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Laki-laki', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Laki-laki', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Laki-laki', 'label' => 'Agama', 'key' => 'agama_mempelai_pria', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Calon Mempelai Laki-laki', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Laki-laki', 'label' => 'Alamat', 'key' => 'alamat_mempelai_pria', 'type' => 'textarea'],
        
            ['label' => 'Sebab Dilangsungkan dengan Wali Hakim', 'key' => 'sebab_wali_hakim', 'type' => 'textarea'],
        ]);

        $this->seedForCode('SPTKP', [ // Surat  Pengantar Tes  Kesehatan Perempuan
            ['label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['label' => 'Tempat Lahir', 'key' => 'tempat_lahir', 'type' => 'text'],
            ['label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir', 'type' => 'date'],
            ['label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan', 'type' => 'text'],
            ['label' => 'Agama', 'key' => 'agama', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['label' => 'Pendidikan', 'key' => 'pendidikan', 'type' => 'text'],
            ['label' => 'Pekerjaan', 'key' => 'pekerjaan', 'type' => 'text'],
            ['label' => 'Status Perkawinan', 'key' => 'status_perkawinan', 'type' => 'select', 'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']],
            ['label' => 'Kelakuan', 'key' => 'kelakuan', 'type' => 'text'],
            ['label' => 'Tujuan Ke', 'key' => 'tujuan_ke', 'type' => 'text'],
            ['label' => 'Keperluan', 'key' => 'keperluan', 'type' => 'text'],
            ['label' => 'Keterangan Lain-lain', 'key' => 'keterangan_lain', 'type' => 'textarea', 'is_required' => false],
            ['label' => 'Surat Berlaku Mulai Tanggal', 'key' => 'berlaku_mulai', 'type' => 'date'],
            ['label' => 'Surat Berlaku Sampai Tanggal', 'key' => 'berlaku_sampai', 'type' => 'date'],
        ]);

        $this->seedForCode('SPBMLP', [ // Surat Pernyataan Belum Menikah Lagi Perempuan 
            ['label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['label' => 'Tempat Lahir', 'key' => 'tempat_lahir', 'type' => 'text'],
            ['label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir', 'type' => 'date'],
            ['label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan', 'type' => 'text'],
            ['label' => 'Agama', 'key' => 'agama', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['label' => 'Pekerjaan', 'key' => 'pekerjaan', 'type' => 'text'],
            ['label' => 'Pendidikan Terakhir', 'key' => 'pendidikan_terakhir', 'type' => 'text'],
            ['label' => 'Status Perkawinan', 'key' => 'status_perkawinan', 'type' => 'select', 'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']],
        ]);

        $this->seedForCode('SKNNP', [ // Surat Keterangan Numpang Nikah Perempuan
            // Warga Kami (yang mengajukan)
            ['section' => 'Data Warga', 'label' => 'Bin/Binti', 'key' => 'bin_binti_warga', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Warga', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir_warga', 'type' => 'text'],
            ['section' => 'Data Warga', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_warga', 'type' => 'date'],
            ['section' => 'Data Warga', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_warga', 'type' => 'text'],
            ['section' => 'Data Warga', 'label' => 'Agama', 'key' => 'agama_warga', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Warga', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_warga', 'type' => 'text'],
            ['section' => 'Data Warga', 'label' => 'Pendidikan Terakhir', 'key' => 'pendidikan_warga', 'type' => 'text'],
            // Calon Pasangan
            ['section' => 'Calon Pasangan', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_calon_pasangan', 'type' => 'text'],
            ['section' => 'Calon Pasangan', 'label' => 'Bin/Binti', 'key' => 'bin_binti_calon_pasangan', 'type' => 'text', 'is_required' => false],
            ['section' => 'Calon Pasangan', 'label' => 'NIK', 'key' => 'nik_calon_pasangan', 'type' => 'text'],
            ['section' => 'Calon Pasangan', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_calon_pasangan', 'type' => 'text'],
            ['section' => 'Calon Pasangan', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_calon_pasangan', 'type' => 'text'],
            ['section' => 'Calon Pasangan', 'label' => 'Agama', 'key' => 'agama_calon_pasangan', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Calon Pasangan', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_calon_pasangan', 'type' => 'text'],
            ['section' => 'Calon Pasangan', 'label' => 'Alamat', 'key' => 'alamat_calon_pasangan', 'type' => 'textarea'],
        ]);

    // PERNIKAHAN LAKI LAKI  
        $this->seedForCode('PNL', [ // Pendaftaran Nikah Laki-laki
            // Data Akad Nikah
            ['section' => 'Data Akad Nikah', 'label' => 'Hari Akad', 'key' => 'hari_akad', 'type' => 'text'],
            ['section' => 'Data Akad Nikah', 'label' => 'Tanggal Akad', 'key' => 'tanggal_akad', 'type' => 'date'],
            ['section' => 'Data Akad Nikah', 'label' => 'Jam Akad', 'key' => 'jam_akad', 'type' => 'text'],
            ['section' => 'Data Akad Nikah', 'label' => 'Tempat Akad', 'key' => 'tempat_akad', 'type' => 'text'],
            // Data Calon Pengantin Laki-Laki
            ['section' => 'Data Catin Putra', 'label' => 'Nama', 'key' => 'nama_catin_putra', 'type' => 'text'],
            ['section' => 'Data Catin Putra', 'label' => 'NIK', 'key' => 'nik_catin_putra', 'type' => 'text'],
            ['section' => 'Data Catin Putra', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir_catin_Putra', 'type' => 'text'],
            ['section' => 'Data Catin Putra', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_catin_Putra', 'type' => 'date'],
            ['section' => 'Data Catin Putra', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_catin_Putra', 'type' => 'text'],
            ['section' => 'Data Catin Putra', 'label' => 'Agama', 'key' => 'agama_catin_putra', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Catin Putra', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_catin_putra', 'type' => 'text'],
            ['section' => 'Data Catin Putra', 'label' => 'Pendidikan Terakhir', 'key' => 'pendidikan_catin_putra', 'type' => 'text'],
            ['section' => 'Data Catin Putra', 'label' => 'Alamat', 'key' => 'alamat_catin_putra', 'type' => 'textarea'],
            ['section' => 'Data Catin Putra', 'label' => 'Status', 'key' => 'status_catin_putra', 'type' => 'select', 'options' => ['Perjaka', 'Duda']],
            // Data Istri Terdahulu (hanya diisi jika status Catin Putra = Duda)
            ['section' => 'Data Istri Terdahulu (jika Duda)', 'label' => 'Nama Istri Terdahulu', 'key' => 'nama_istri_terdahulu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Istri Terdahulu (jika Duda)', 'label' => 'Bin/Nama Orang Tua Istri Terdahulu', 'key' => 'bin_istri_terdahulu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Istri Terdahulu (jika Duda)', 'label' => 'NIK Istri Terdahulu', 'key' => 'nik_istri_terdahulu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Istri Terdahulu (jika Duda)', 'label' => 'Tempat/Tanggal Lahir Istri Terdahulu', 'key' => 'ttl_istri_terdahulu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Istri Terdahulu (jika Duda)', 'label' => 'Kewarganegaraan Istri Terdahulu', 'key' => 'kewarganegaraan_istri_terdahulu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Istri Terdahulu (jika Duda)', 'label' => 'Agama Istri Terdahulu', 'key' => 'agama_istri_terdahulu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Istri Terdahulu (jika Duda)', 'label' => 'Pekerjaan Istri Terdahulu', 'key' => 'pekerjaan_istri_terdahulu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Istri Terdahulu (jika Duda)', 'label' => 'Alamat Istri Terdahulu', 'key' => 'alamat_istri_terdahulu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Istri Terdahulu (jika Duda)', 'label' => 'Meninggal Dunia Pada', 'key' => 'Istri_terdahulu_meninggal_pada', 'type' => 'date', 'is_required' => false],
            ['section' => 'Data Istri Terdahulu (jika Duda)', 'label' => 'Meninggal Di', 'key' => 'istri_terdahulu_meninggal_di', 'type' => 'text', 'is_required' => false],
            // Data Ayah Catin laki-laki 
            ['section' => 'Data Ayah Catin Putra', 'label' => 'Nama', 'key' => 'nama_ayah_putra', 'type' => 'text'],
            ['section' => 'Data Ayah Catin Putra', 'label' => 'Bin', 'key' => 'bin_ayah_putra', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ayah Catin Putra', 'label' => 'NIK', 'key' => 'nik_ayah_putra', 'type' => 'text'],
            ['section' => 'Data Ayah Catin Putra', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_ayah_putra', 'type' => 'text'],
            ['section' => 'Data Ayah Catin Putra', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ayah_putra', 'type' => 'text'],
            ['section' => 'Data Ayah Catin Putra', 'label' => 'Agama', 'key' => 'agama_ayah_putra', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Ayah Catin Putra', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ayah_putra', 'type' => 'text'],
            ['section' => 'Data Ayah Catin Putra', 'label' => 'Alamat', 'key' => 'alamat_ayah_putra', 'type' => 'textarea'],
            // Data Ibu Catin Putra
            ['section' => 'Data Ibu Catin Putra', 'label' => 'Nama', 'key' => 'nama_ibu_putra', 'type' => 'text'],
            ['section' => 'Data Ibu Catin Putra', 'label' => 'Binti', 'key' => 'binti_ibu_putra', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ibu Catin Putra', 'label' => 'NIK', 'key' => 'nik_ibu_putra', 'type' => 'text'],
            ['section' => 'Data Ibu Catin Putra', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_ibu_putra', 'type' => 'text'],
            ['section' => 'Data Ibu Catin Putra', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ibu_putra', 'type' => 'text'],
            ['section' => 'Data Ibu Catin Putra', 'label' => 'Agama', 'key' => 'agama_ibu_putra', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Ibu Catin Putra', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ibu_putra', 'type' => 'text'],
            ['section' => 'Data Ibu Catin Putra', 'label' => 'Alamat', 'key' => 'alamat_ibu_putra', 'type' => 'textarea'],
            // Data Wali Nikah (hanya diisi jika wali nasab bukan ayah kandung)
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Nama', 'key' => 'nama_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Bin', 'key' => 'bin_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'NIK', 'key' => 'nik_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Agama', 'key' => 'agama_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Alamat', 'key' => 'alamat_wali', 'type' => 'textarea', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Hubungan Wali', 'key' => 'hubungan_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Sebab Wali Bukan Ayah Kandung', 'key' => 'sebab_wali_bukan_ayah', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali Nikah (jika bukan ayah kandung)', 'label' => 'Jika Wali Hakim, Sebab Wali Hakim', 'key' => 'sebab_wali_hakim', 'type' => 'text', 'is_required' => false],
            // Data Calon Pengantin Putri
            ['section' => 'Data Catin Putri', 'label' => 'Nama', 'key' => 'nama_catin_putri', 'type' => 'text'],
            ['section' => 'Data Catin Putri', 'label' => 'Bin', 'key' => 'bin_catin_putri', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Catin Putri', 'label' => 'NIK', 'key' => 'nik_catin_putri', 'type' => 'text'],
            ['section' => 'Data Catin Putri', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir_catin_putri', 'type' => 'text'],
            ['section' => 'Data Catin Putri', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_catin_putri', 'type' => 'date'],
            ['section' => 'Data Catin Putri', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_catin_putri', 'type' => 'text'],
            ['section' => 'Data Catin Putri', 'label' => 'Agama', 'key' => 'agama_catin_putri', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Catin Putri', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_catin_putri', 'type' => 'text'],
            ['section' => 'Data Catin Putri', 'label' => 'Alamat', 'key' => 'alamat_catin_putri', 'type' => 'textarea'],
        ]);

        $this->seedForCode('N1L', [ // Pengantar Nikah Laki-laki
            // Data Diri Catin
            ['section' => 'Data Catin', 'label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['section' => 'Data Catin', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir', 'type' => 'text'],
            ['section' => 'Data Catin', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir', 'type' => 'date'],
            ['section' => 'Data Catin', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan', 'type' => 'text'],
            ['section' => 'Data Catin', 'label' => 'Agama', 'key' => 'agama', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Catin', 'label' => 'Pekerjaan', 'key' => 'pekerjaan', 'type' => 'text'],
            ['section' => 'Data Catin', 'label' => 'Pendidikan Terakhir', 'key' => 'pendidikan_terakhir', 'type' => 'text'],
            // Status Pernikahan
            ['section' => 'Status Pernikahan', 'label' => 'Status', 'key' => 'status_perkawinan', 'type' => 'select', 'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']],
            ['section' => 'Status Pernikahan', 'label' => 'Nama Isteri/Suami Terdahulu', 'key' => 'nama_pasangan_terdahulu', 'type' => 'text', 'is_required' => false],
            // Data Ayah
            ['section' => 'Data Ayah', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'NIK', 'key' => 'nik_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Agama', 'key' => 'agama_ayah', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Ayah', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Alamat', 'key' => 'alamat_ayah', 'type' => 'textarea'],
            // Data Ibu
            ['section' => 'Data Ibu', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'NIK', 'key' => 'nik_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Agama', 'key' => 'agama_ibu', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Ibu', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Alamat', 'key' => 'alamat_ibu', 'type' => 'textarea'],
        ]);

        $this->seedForCode('N4L', [ //Persetujuan Calon Pengantin Laki-laki
            // Calon Suami
            ['section' => 'Calon Suami', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_calon_suami', 'type' => 'text'],
            ['section' => 'Calon Suami', 'label' => 'Bin', 'key' => 'bin_calon_suami', 'type' => 'text', 'is_required' => false],
            ['section' => 'Calon Suami', 'label' => 'NIK', 'key' => 'nik_calon_suami', 'type' => 'text'],
            ['section' => 'Calon Suami', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_calon_suami', 'type' => 'text'],
            ['section' => 'Calon Suami', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_calon_suami', 'type' => 'text'],
            ['section' => 'Calon Suami', 'label' => 'Agama', 'key' => 'agama_calon_suami', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Calon Suami', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_calon_suami', 'type' => 'text'],
            ['section' => 'Calon Suami', 'label' => 'Alamat', 'key' => 'alamat_calon_suami', 'type' => 'textarea'],
            // Calon Istri
            ['section' => 'Calon Istri', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_calon_istri', 'type' => 'text'],
            ['section' => 'Calon Istri', 'label' => 'Binti', 'key' => 'binti_calon_istri', 'type' => 'text', 'is_required' => false],
            ['section' => 'Calon Istri', 'label' => 'NIK', 'key' => 'nik_calon_istri', 'type' => 'text'],
            ['section' => 'Calon Istri', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_calon_istri', 'type' => 'text'],
            ['section' => 'Calon Istri', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_calon_istri', 'type' => 'text'],
            ['section' => 'Calon Istri', 'label' => 'Agama', 'key' => 'agama_calon_istri', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Calon Istri', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_calon_istri', 'type' => 'text'],
            ['section' => 'Calon Istri', 'label' => 'Alamat', 'key' => 'alamat_calon_istri', 'type' => 'textarea'],
        ]);

        $this->seedForCode('N5P', [ // Surat Izin Orang Tua
            // Data Ayah
            ['section' => 'Data Ayah', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Bin', 'key' => 'bin_ayah', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ayah', 'label' => 'NIK', 'key' => 'nik_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Agama', 'key' => 'agama_ayah', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Ayah', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Alamat', 'key' => 'alamat_ayah', 'type' => 'textarea'],
            // Data Ibu
            ['section' => 'Data Ibu', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Binti', 'key' => 'binti_ibu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ibu', 'label' => 'NIK', 'key' => 'nik_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Agama', 'key' => 'agama_ibu', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Ibu', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Alamat', 'key' => 'alamat_ibu', 'type' => 'textarea'],
            // Data Anak yang Diberi Izin
            ['section' => 'Data Anak', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_anak', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Bin/Binti', 'key' => 'bin_binti_anak', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Anak', 'label' => 'NIK', 'key' => 'nik_anak', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_anak', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_anak', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Agama', 'key' => 'agama_anak', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Anak', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_anak', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Alamat', 'key' => 'alamat_anak', 'type' => 'textarea'],
            // Data Calon Pasangan
            ['section' => 'Data Calon Pasangan', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_calon_pasangan', 'type' => 'text'],
            ['section' => 'Data Calon Pasangan', 'label' => 'Bin/Binti', 'key' => 'bin_binti_calon_pasangan', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Calon Pasangan', 'label' => 'NIK', 'key' => 'nik_calon_pasangan', 'type' => 'text'],
            ['section' => 'Data Calon Pasangan', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_calon_pasangan', 'type' => 'text'],
            ['section' => 'Data Calon Pasangan', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_calon_pasangan', 'type' => 'text'],
            ['section' => 'Data Calon Pasangan', 'label' => 'Agama', 'key' => 'agama_calon_pasangan', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Calon Pasangan', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_calon_pasangan', 'type' => 'text'],
            ['section' => 'Data Calon Pasangan', 'label' => 'Alamat', 'key' => 'alamat_calon_pasangan', 'type' => 'textarea'],
        ]);

        $this->seedForCode('N6P', [ // Surat Keterangan Kematian
            // Data Almarhum/Almarhumah
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_almarhum', 'type' => 'text'],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Bin/Binti', 'key' => 'bin_binti_almarhum', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'NIK', 'key' => 'nik_almarhum', 'type' => 'text'],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_almarhum', 'type' => 'text'],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_almarhum', 'type' => 'text'],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Agama', 'key' => 'agama_almarhum', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_almarhum', 'type' => 'text'],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Alamat', 'key' => 'alamat_almarhum', 'type' => 'textarea'],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Tanggal Meninggal Dunia', 'key' => 'tanggal_meninggal', 'type' => 'date'],
            ['section' => 'Data Almarhum/Almarhumah', 'label' => 'Tempat Meninggal', 'key' => 'tempat_meninggal', 'type' => 'text'],
            // Data Pasangan yang Ditinggalkan
            ['section' => 'Data Pasangan yang Ditinggalkan', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_pasangan', 'type' => 'text'],
            ['section' => 'Data Pasangan yang Ditinggalkan', 'label' => 'Bin/Binti', 'key' => 'bin_binti_pasangan', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Pasangan yang Ditinggalkan', 'label' => 'NIK', 'key' => 'nik_pasangan', 'type' => 'text'],
            ['section' => 'Data Pasangan yang Ditinggalkan', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_pasangan', 'type' => 'text'],
            ['section' => 'Data Pasangan yang Ditinggalkan', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_pasangan', 'type' => 'text'],
            ['section' => 'Data Pasangan yang Ditinggalkan', 'label' => 'Agama', 'key' => 'agama_pasangan', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Pasangan yang Ditinggalkan', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_pasangan', 'type' => 'text'],
            ['section' => 'Data Pasangan yang Ditinggalkan', 'label' => 'Alamat', 'key' => 'alamat_pasangan', 'type' => 'textarea'],
        ]);

        $this->seedForCode('SKWNP', [ // Surat Keterangan Wali Nikah 
            // Data Wali
            ['section' => 'Data Wali', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_wali', 'type' => 'text'],
            ['section' => 'Data Wali', 'label' => 'Bin', 'key' => 'bin_wali', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Wali', 'label' => 'NIK', 'key' => 'nik_wali', 'type' => 'text'],
            ['section' => 'Data Wali', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_wali', 'type' => 'text'],
            ['section' => 'Data Wali', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_wali', 'type' => 'text'],
            ['section' => 'Data Wali', 'label' => 'Agama', 'key' => 'agama_wali', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Wali', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_wali', 'type' => 'text'],
            ['section' => 'Data Wali', 'label' => 'Alamat', 'key' => 'alamat_wali', 'type' => 'textarea'],
            // Data Calon Mempelai Wanita
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Binti', 'key' => 'binti_mempelai_wanita', 'type' => 'text', 'is_required' => false],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'NIK', 'key' => 'nik_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Agama', 'key' => 'agama_mempelai_wanita', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Alamat', 'key' => 'alamat_mempelai_wanita', 'type' => 'textarea'],
            // Data Calon Mempelai Pria
            ['section' => 'Calon Mempelai Pria', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Pria', 'label' => 'Bin', 'key' => 'bin_mempelai_pria', 'type' => 'text', 'is_required' => false],
            ['section' => 'Calon Mempelai Pria', 'label' => 'NIK', 'key' => 'nik_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Pria', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Pria', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Pria', 'label' => 'Agama', 'key' => 'agama_mempelai_pria', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Calon Mempelai Pria', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Pria', 'label' => 'Alamat', 'key' => 'alamat_mempelai_pria', 'type' => 'textarea'],
        
            ['label' => 'Hubungan Wali Nasab dengan Calon Mempelai Wanita', 'key' => 'hubungan_wali_nasab', 'type' => 'text'],
        ]);

        $this->seedForCode('SKWHP', [ // Surat Keterangan Wali Hakim 
            // Calon Mempelai Wanita
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Binti', 'key' => 'binti_mempelai_wanita', 'type' => 'text', 'is_required' => false],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'NIK', 'key' => 'nik_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Agama', 'key' => 'agama_mempelai_wanita', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_mempelai_wanita', 'type' => 'text'],
            ['section' => 'Calon Mempelai Wanita', 'label' => 'Alamat', 'key' => 'alamat_mempelai_wanita', 'type' => 'textarea'],
            // Calon Mempelai Laki-laki
            ['section' => 'Calon Mempelai Laki-laki', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Laki-laki', 'label' => 'Bin', 'key' => 'bin_mempelai_pria', 'type' => 'text', 'is_required' => false],
            ['section' => 'Calon Mempelai Laki-laki', 'label' => 'NIK', 'key' => 'nik_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Laki-laki', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Laki-laki', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Laki-laki', 'label' => 'Agama', 'key' => 'agama_mempelai_pria', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Calon Mempelai Laki-laki', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_mempelai_pria', 'type' => 'text'],
            ['section' => 'Calon Mempelai Laki-laki', 'label' => 'Alamat', 'key' => 'alamat_mempelai_pria', 'type' => 'textarea'],
        
            ['label' => 'Sebab Dilangsungkan dengan Wali Hakim', 'key' => 'sebab_wali_hakim', 'type' => 'textarea'],
        ]);

        $this->seedForCode('SPTKP', [ // Surat  Pengantar Tes  Kesehatan Perempuan
            ['label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['label' => 'Tempat Lahir', 'key' => 'tempat_lahir', 'type' => 'text'],
            ['label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir', 'type' => 'date'],
            ['label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan', 'type' => 'text'],
            ['label' => 'Agama', 'key' => 'agama', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['label' => 'Pendidikan', 'key' => 'pendidikan', 'type' => 'text'],
            ['label' => 'Pekerjaan', 'key' => 'pekerjaan', 'type' => 'text'],
            ['label' => 'Status Perkawinan', 'key' => 'status_perkawinan', 'type' => 'select', 'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']],
            ['label' => 'Kelakuan', 'key' => 'kelakuan', 'type' => 'text'],
            ['label' => 'Tujuan Ke', 'key' => 'tujuan_ke', 'type' => 'text'],
            ['label' => 'Keperluan', 'key' => 'keperluan', 'type' => 'text'],
            ['label' => 'Keterangan Lain-lain', 'key' => 'keterangan_lain', 'type' => 'textarea', 'is_required' => false],
            ['label' => 'Surat Berlaku Mulai Tanggal', 'key' => 'berlaku_mulai', 'type' => 'date'],
            ['label' => 'Surat Berlaku Sampai Tanggal', 'key' => 'berlaku_sampai', 'type' => 'date'],
        ]);

        $this->seedForCode('SPBMLP', [ // Surat Pernyataan Belum Menikah Lagi Perempuan 
            ['label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['label' => 'Tempat Lahir', 'key' => 'tempat_lahir', 'type' => 'text'],
            ['label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir', 'type' => 'date'],
            ['label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan', 'type' => 'text'],
            ['label' => 'Agama', 'key' => 'agama', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['label' => 'Pekerjaan', 'key' => 'pekerjaan', 'type' => 'text'],
            ['label' => 'Pendidikan Terakhir', 'key' => 'pendidikan_terakhir', 'type' => 'text'],
            ['label' => 'Status Perkawinan', 'key' => 'status_perkawinan', 'type' => 'select', 'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']],
        ]);

        $this->seedForCode('SKNNP', [ // Surat Keterangan Numpang Nikah Perempuan
            // Warga Kami (yang mengajukan)
            ['section' => 'Data Warga', 'label' => 'Bin/Binti', 'key' => 'bin_binti_warga', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Warga', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir_warga', 'type' => 'text'],
            ['section' => 'Data Warga', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_warga', 'type' => 'date'],
            ['section' => 'Data Warga', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_warga', 'type' => 'text'],
            ['section' => 'Data Warga', 'label' => 'Agama', 'key' => 'agama_warga', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Warga', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_warga', 'type' => 'text'],
            ['section' => 'Data Warga', 'label' => 'Pendidikan Terakhir', 'key' => 'pendidikan_warga', 'type' => 'text'],
            // Calon Pasangan
            ['section' => 'Calon Pasangan', 'label' => 'Nama Lengkap dan Alias', 'key' => 'nama_calon_pasangan', 'type' => 'text'],
            ['section' => 'Calon Pasangan', 'label' => 'Bin/Binti', 'key' => 'bin_binti_calon_pasangan', 'type' => 'text', 'is_required' => false],
            ['section' => 'Calon Pasangan', 'label' => 'NIK', 'key' => 'nik_calon_pasangan', 'type' => 'text'],
            ['section' => 'Calon Pasangan', 'label' => 'Tempat dan Tanggal Lahir', 'key' => 'ttl_calon_pasangan', 'type' => 'text'],
            ['section' => 'Calon Pasangan', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_calon_pasangan', 'type' => 'text'],
            ['section' => 'Calon Pasangan', 'label' => 'Agama', 'key' => 'agama_calon_pasangan', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Calon Pasangan', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_calon_pasangan', 'type' => 'text'],
            ['section' => 'Calon Pasangan', 'label' => 'Alamat', 'key' => 'alamat_calon_pasangan', 'type' => 'textarea'],
        ]);


    // KELAHIRAN BARU
        $this->seedForCode('PAK', [ // Permohonan Akta Kelahiran 
            // Data Keluarga
            ['section' => 'Data Keluarga', 'label' => 'Nomor Kartu Keluarga', 'key' => 'nomor_kk', 'type' => 'text'],
            ['section' => 'Data Keluarga', 'label' => 'Nama Kepala Keluarga', 'key' => 'nama_kepala_keluarga', 'type' => 'text'],
            // Data Anak
            ['section' => 'Data Anak', 'label' => 'NIK Anak', 'key' => 'nik_anak', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Anak', 'label' => 'Nama Lengkap', 'key' => 'nama_anak', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir_anak', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_anak', 'type' => 'date'],
            ['section' => 'Data Anak', 'label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin_anak', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
        
            // Data Ibu Kandung
            ['section' => 'Data Ibu Kandung', 'label' => 'NIK', 'key' => 'nik_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu Kandung', 'label' => 'Nama Lengkap', 'key' => 'nama_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu Kandung', 'label' => 'Alamat', 'key' => 'alamat_ibu', 'type' => 'textarea'],
            ['section' => 'Data Ibu Kandung', 'label' => 'RT', 'key' => 'rt_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu Kandung', 'label' => 'RW', 'key' => 'rw_ibu', 'type' => 'text'],
        
            // Data Ayah Kandung
            ['section' => 'Data Ayah Kandung', 'label' => 'NIK', 'key' => 'nik_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah Kandung', 'label' => 'Nama Lengkap', 'key' => 'nama_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah Kandung', 'label' => 'Alamat', 'key' => 'alamat_ayah', 'type' => 'textarea'],
            ['section' => 'Data Ayah Kandung', 'label' => 'RT', 'key' => 'rt_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah Kandung', 'label' => 'RW', 'key' => 'rw_ayah', 'type' => 'text'],
        
            // Data Status Perkawinan (Orang Tua)
            ['section' => 'Data Status Perkawinan Orang Tua', 'label' => 'Nomor Kutipan Akta Perkawinan', 'key' => 'nomor_akta_perkawinan_ortu', 'type' => 'text'],
            ['section' => 'Data Status Perkawinan Orang Tua', 'label' => 'Tanggal Pernikahan', 'key' => 'tanggal_pernikahan_ortu', 'type' => 'date'],
        
            // Data Pelapor
            ['section' => 'Data Pelapor', 'label' => 'NIK Pelapor', 'key' => 'nik_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Nama Lengkap Pelapor', 'key' => 'nama_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Alamat Pelapor', 'key' => 'alamat_pelapor', 'type' => 'textarea'],
            ['section' => 'Data Pelapor', 'label' => 'No HP/Telepon/Email Aktif', 'key' => 'kontak_pelapor', 'type' => 'text'],
        ]);
        $this->seedForCode('FPK', [ // Formulir Pelaporan Kelahiran
            ['section' => 'Data Keluarga', 'label' => 'Nomor Kartu Keluarga', 'key' => 'nomor_kk', 'type' => 'text'],
            ['section' => 'Data Keluarga', 'label' => 'Nama Kepala Keluarga', 'key' => 'nama_kepala_keluarga', 'type' => 'text'],
        
            // Bayi/Anak
            ['section' => 'Data Bayi/Anak', 'label' => 'NIK', 'key' => 'nik_anak', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Bayi/Anak', 'label' => 'Nama Lengkap', 'key' => 'nama_anak', 'type' => 'text'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin_anak', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['section' => 'Data Bayi/Anak', 'label' => 'Tempat Dilahirkan', 'key' => 'tempat_dilahirkan', 'type' => 'select', 'options' => ['Rumah Sakit', 'Puskesmas', 'Rumah Bersalin', 'Rumah', 'Lainnya']],
            ['section' => 'Data Bayi/Anak', 'label' => 'Tempat Kelahiran (Kota)', 'key' => 'tempat_kelahiran_kota', 'type' => 'text'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Hari/Tanggal Lahir', 'key' => 'hari_tanggal_lahir', 'type' => 'text'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Waktu/Jam Kelahiran', 'key' => 'jam_kelahiran', 'type' => 'text'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Jenis Kelahiran', 'key' => 'jenis_kelahiran', 'type' => 'select', 'options' => ['Tunggal', 'Kembar Dua', 'Kembar Tiga', 'Kembar Empat atau Lebih']],
            ['section' => 'Data Bayi/Anak', 'label' => 'Kelahiran Ke', 'key' => 'kelahiran_ke', 'type' => 'text'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Penolong Kelahiran', 'key' => 'penolong_kelahiran', 'type' => 'select', 'options' => ['Dokter', 'Bidan', 'Dukun', 'Lainnya']],
            ['section' => 'Data Bayi/Anak', 'label' => 'Berat Bayi (Kg)', 'key' => 'berat_bayi', 'type' => 'number'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Panjang Bayi (Cm)', 'key' => 'panjang_bayi', 'type' => 'number'],
        
            // Ibu
            ['section' => 'Data Ibu', 'label' => 'NIK', 'key' => 'nik_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Nama Lengkap', 'key' => 'nama_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Alamat', 'key' => 'alamat_ibu', 'type' => 'textarea'],
            ['section' => 'Data Ibu', 'label' => 'RT', 'key' => 'rt_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'RW', 'key' => 'rw_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Tempat Pencatatan Perkawinan', 'key' => 'tempat_pencatatan_perkawinan', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Tanggal Pencatatan Perkawinan', 'key' => 'tanggal_pencatatan_perkawinan', 'type' => 'date'],
        
            // Ayah
            ['section' => 'Data Ayah', 'label' => 'NIK', 'key' => 'nik_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Nama Lengkap', 'key' => 'nama_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Alamat', 'key' => 'alamat_ayah', 'type' => 'textarea'],
            ['section' => 'Data Ayah', 'label' => 'RT', 'key' => 'rt_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'RW', 'key' => 'rw_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ayah', 'type' => 'text'],
        
            // Pelapor
            ['section' => 'Data Pelapor', 'label' => 'NIK', 'key' => 'nik_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Nama Lengkap', 'key' => 'nama_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Umur', 'key' => 'umur_pelapor', 'type' => 'number'],
            ['section' => 'Data Pelapor', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Alamat', 'key' => 'alamat_pelapor', 'type' => 'textarea'],
            ['section' => 'Data Pelapor', 'label' => 'Tanggal Lapor', 'key' => 'tanggal_lapor', 'type' => 'date'],
        
            // Saksi I
            ['section' => 'Saksi I', 'label' => 'NIK', 'key' => 'nik_saksi_1', 'type' => 'text'],
            ['section' => 'Saksi I', 'label' => 'Nama Lengkap', 'key' => 'nama_saksi_1', 'type' => 'text'],
            ['section' => 'Saksi I', 'label' => 'Umur', 'key' => 'umur_saksi_1', 'type' => 'number'],
            ['section' => 'Saksi I', 'label' => 'Alamat', 'key' => 'alamat_saksi_1', 'type' => 'text'],
        
            // Saksi II
            ['section' => 'Saksi II', 'label' => 'NIK', 'key' => 'nik_saksi_2', 'type' => 'text'],
            ['section' => 'Saksi II', 'label' => 'Nama Lengkap', 'key' => 'nama_saksi_2', 'type' => 'text'],
            ['section' => 'Saksi II', 'label' => 'Umur', 'key' => 'umur_saksi_2', 'type' => 'number'],
            ['section' => 'Saksi II', 'label' => 'Alamat', 'key' => 'alamat_saksi_2', 'type' => 'text'],
        ]);
        $this->seedForCode('LK', [ // Laporan Kelahiran 
            // Data Pelapor
            ['section' => 'Data Pelapor', 'label' => 'NIK', 'key' => 'nik_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Nama Lengkap', 'key' => 'nama_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_pelapor', 'type' => 'date'],
            ['section' => 'Data Pelapor', 'label' => 'Umur', 'key' => 'umur_pelapor', 'type' => 'number'],
            ['section' => 'Data Pelapor', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Alamat', 'key' => 'alamat_pelapor', 'type' => 'textarea'],
            ['section' => 'Data Pelapor', 'label' => 'Hubungan dengan Si Bayi', 'key' => 'hubungan_dengan_bayi', 'type' => 'select', 'options' => ['Ayah', 'Ibu', 'Wali', 'Lainnya']],
        
            // Data Ibu
            ['section' => 'Data Ibu', 'label' => 'Nama', 'key' => 'nama_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Alamat', 'key' => 'alamat_ibu', 'type' => 'textarea'],
            ['section' => 'Data Ibu', 'label' => 'RT', 'key' => 'rt_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'RW', 'key' => 'rw_ibu', 'type' => 'text'],
        
            // Data Ayah (Suami Dari)
            ['section' => 'Data Ayah', 'label' => 'Nama', 'key' => 'nama_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Alamat', 'key' => 'alamat_ayah', 'type' => 'textarea'],
            ['section' => 'Data Ayah', 'label' => 'RT', 'key' => 'rt_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'RW', 'key' => 'rw_ayah', 'type' => 'text'],
        
            // Data Kelahiran
            ['section' => 'Data Kelahiran', 'label' => 'Nama Lengkap Anak', 'key' => 'nama_anak', 'type' => 'text'],
            ['section' => 'Data Kelahiran', 'label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin_anak', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['section' => 'Data Kelahiran', 'label' => 'Tempat Dilahirkan', 'key' => 'tempat_dilahirkan', 'type' => 'select', 'options' => ['Rumah Sakit', 'Puskesmas', 'Rumah Bersalin', 'Rumah', 'Lainnya']],
            ['section' => 'Data Kelahiran', 'label' => 'Alamat RS/RB', 'key' => 'alamat_rs', 'type' => 'textarea', 'is_required' => false],
            ['section' => 'Data Kelahiran', 'label' => 'Tempat Kelahiran (Kota)', 'key' => 'tempat_kelahiran_kota', 'type' => 'text'],
            ['section' => 'Data Kelahiran', 'label' => 'Hari/Tanggal Lahir', 'key' => 'hari_tanggal_lahir', 'type' => 'text'],
            ['section' => 'Data Kelahiran', 'label' => 'Waktu/Jam Kelahiran', 'key' => 'jam_kelahiran', 'type' => 'text'],
            ['section' => 'Data Kelahiran', 'label' => 'Jenis Kelahiran', 'key' => 'jenis_kelahiran', 'type' => 'select', 'options' => ['Tunggal', 'Kembar Dua', 'Kembar Tiga', 'Kembar Empat atau Lebih']],
            ['section' => 'Data Kelahiran', 'label' => 'Kelahiran Ke', 'key' => 'kelahiran_ke', 'type' => 'text'],
            ['section' => 'Data Kelahiran', 'label' => 'Penolong Kelahiran', 'key' => 'penolong_kelahiran', 'type' => 'select', 'options' => ['Dokter', 'Bidan', 'Dukun', 'Lainnya']],
            ['section' => 'Data Kelahiran', 'label' => 'Berat Bayi (Kg)', 'key' => 'berat_bayi', 'type' => 'number'],
            ['section' => 'Data Kelahiran', 'label' => 'Panjang Bayi (Cm)', 'key' => 'panjang_bayi', 'type' => 'number'],
            ['section' => 'Data Kelahiran', 'label' => 'Umur Kelahiran', 'key' => 'umur_kelahiran', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Kelahiran', 'label' => 'Cara Kelahiran', 'key' => 'cara_kelahiran', 'type' => 'select', 'options' => ['Spontan', 'Caesar', 'Bantuan Alat (Vakum/Forsep)']],
            ['section' => 'Data Kelahiran', 'label' => 'Biaya Kelahiran', 'key' => 'biaya_kelahiran', 'type' => 'number', 'is_required' => false],
        ]);
        $this->seedForCode('SKKL', [ // Surat Keterangan Kelahiran 
            ['section' => 'Data Keluarga', 'label' => 'Nomor Kartu Keluarga', 'key' => 'nomor_kk', 'type' => 'text'],
            ['section' => 'Data Keluarga', 'label' => 'Nama Kepala Keluarga', 'key' => 'nama_kepala_keluarga', 'type' => 'text'],
        
            // Bayi/Anak
            ['section' => 'Data Bayi/Anak', 'label' => 'NIK', 'key' => 'nik_anak', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Bayi/Anak', 'label' => 'Nama Lengkap', 'key' => 'nama_anak', 'type' => 'text'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin_anak', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['section' => 'Data Bayi/Anak', 'label' => 'Tempat Dilahirkan', 'key' => 'tempat_dilahirkan', 'type' => 'select', 'options' => ['Rumah Sakit', 'Puskesmas', 'Rumah Bersalin', 'Rumah', 'Lainnya']],
            ['section' => 'Data Bayi/Anak', 'label' => 'Tempat Kelahiran (Kota)', 'key' => 'tempat_kelahiran_kota', 'type' => 'text'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Hari/Tanggal Lahir', 'key' => 'hari_tanggal_lahir', 'type' => 'text'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Waktu/Jam Kelahiran', 'key' => 'jam_kelahiran', 'type' => 'text'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Jenis Kelahiran', 'key' => 'jenis_kelahiran', 'type' => 'select', 'options' => ['Tunggal', 'Kembar Dua', 'Kembar Tiga', 'Kembar Empat atau Lebih']],
            ['section' => 'Data Bayi/Anak', 'label' => 'Kelahiran Ke (dengan huruf)', 'key' => 'kelahiran_ke', 'type' => 'text'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Penolong Kelahiran', 'key' => 'penolong_kelahiran', 'type' => 'select', 'options' => ['Dokter', 'Bidan', 'Dukun', 'Lainnya']],
            ['section' => 'Data Bayi/Anak', 'label' => 'Berat Bayi (Kg)', 'key' => 'berat_bayi', 'type' => 'number'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Panjang Bayi (Cm)', 'key' => 'panjang_bayi', 'type' => 'number'],
        
            // Ibu
            ['section' => 'Data Ibu', 'label' => 'NIK', 'key' => 'nik_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Nama Lengkap (Sesuai Buku Nikah)', 'key' => 'nama_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Alamat', 'key' => 'alamat_ibu', 'type' => 'textarea'],
            ['section' => 'Data Ibu', 'label' => 'RT', 'key' => 'rt_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'RW', 'key' => 'rw_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Tempat Pencatatan Perkawinan', 'key' => 'tempat_pencatatan_perkawinan', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Tanggal Pencatatan Perkawinan', 'key' => 'tanggal_pencatatan_perkawinan', 'type' => 'date'],
        
            // Ayah
            ['section' => 'Data Ayah', 'label' => 'NIK', 'key' => 'nik_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Nama Lengkap', 'key' => 'nama_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Alamat', 'key' => 'alamat_ayah', 'type' => 'textarea'],
            ['section' => 'Data Ayah', 'label' => 'RT', 'key' => 'rt_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'RW', 'key' => 'rw_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ayah', 'type' => 'text'],
        
            // Pelapor
            ['section' => 'Data Pelapor', 'label' => 'NIK', 'key' => 'nik_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Nama Lengkap', 'key' => 'nama_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Umur', 'key' => 'umur_pelapor', 'type' => 'number'],
            ['section' => 'Data Pelapor', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Alamat', 'key' => 'alamat_pelapor', 'type' => 'textarea'],
            ['section' => 'Data Pelapor', 'label' => 'Tanggal Lapor', 'key' => 'tanggal_lapor', 'type' => 'date'],
        
            // Saksi I
            ['section' => 'Saksi I', 'label' => 'NIK', 'key' => 'nik_saksi_1', 'type' => 'text'],
            ['section' => 'Saksi I', 'label' => 'Nama Lengkap', 'key' => 'nama_saksi_1', 'type' => 'text'],
            ['section' => 'Saksi I', 'label' => 'Umur', 'key' => 'umur_saksi_1', 'type' => 'number'],
            ['section' => 'Saksi I', 'label' => 'Alamat', 'key' => 'alamat_saksi_1', 'type' => 'text'],
        
            // Saksi II
            ['section' => 'Saksi II', 'label' => 'NIK', 'key' => 'nik_saksi_2', 'type' => 'text'],
            ['section' => 'Saksi II', 'label' => 'Nama Lengkap', 'key' => 'nama_saksi_2', 'type' => 'text'],
            ['section' => 'Saksi II', 'label' => 'Umur', 'key' => 'umur_saksi_2', 'type' => 'number'],
            ['section' => 'Saksi II', 'label' => 'Alamat', 'key' => 'alamat_saksi_2', 'type' => 'text'],
        ]);
        $this->seedForCode('SKAK', [ // Surat Kuasa Permohonan Akta Kelahiran
            // Pemberi Kuasa
            ['section' => 'Pemberi Kuasa', 'label' => 'Nama', 'key' => 'nama_pemberi_kuasa', 'type' => 'text'],
            ['section' => 'Pemberi Kuasa', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_pemberi_kuasa', 'type' => 'text'],
            ['section' => 'Pemberi Kuasa', 'label' => 'Alamat', 'key' => 'alamat_pemberi_kuasa', 'type' => 'textarea'],
        
            // Penerima Kuasa
            ['section' => 'Penerima Kuasa', 'label' => 'Nama', 'key' => 'nama_penerima_kuasa', 'type' => 'text'],
            ['section' => 'Penerima Kuasa', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_penerima_kuasa', 'type' => 'text'],
            ['section' => 'Penerima Kuasa', 'label' => 'Alamat', 'key' => 'alamat_penerima_kuasa', 'type' => 'textarea'],
        
            // Objek Kuasa
            ['label' => 'Nama Anak (untuk Permohonan Akta Kelahiran)', 'key' => 'nama_anak', 'type' => 'text'],
        ]);
        $this->seedForCode('PPKT', [ // Persetujuan Pencatatan Kelahiran Terlambat 
            ['label' => 'Nomor Surat Pelaporan (dari Kelurahan)', 'key' => 'nomor_surat_pelaporan', 'type' => 'text'],
            ['label' => 'Nama Pelapor', 'key' => 'nama_pelapor', 'type' => 'text'],
            ['label' => 'Tanggal Surat Pelaporan', 'key' => 'tanggal_surat_pelaporan', 'type' => 'date'],
        
            // Data Anak yang Diajukan
            ['section' => 'Data Anak', 'label' => 'Nama', 'key' => 'nama_anak', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'NIK', 'key' => 'nik_anak', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Anak', 'label' => 'Tempat Lahir', 'key' => 'tempat_lahir_anak', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_anak', 'type' => 'date'],
            ['section' => 'Data Anak', 'label' => 'Anak Ke', 'key' => 'anak_ke', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin_anak', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['section' => 'Data Anak', 'label' => 'Nama Ibu', 'key' => 'nama_ibu', 'type' => 'text'],
            ['section' => 'Data Anak', 'label' => 'Nama Ayah', 'key' => 'nama_ayah', 'type' => 'text'],
        ]);
        $this->seedForCode('LKLD', [ // Laporan Kelahiran Luar Domisili 
            ['section' => 'Data Keluarga', 'label' => 'Nomor Kartu Keluarga', 'key' => 'nomor_kk', 'type' => 'text'],
            ['section' => 'Data Keluarga', 'label' => 'Nama Kepala Keluarga', 'key' => 'nama_kepala_keluarga', 'type' => 'text'],
        
            // Bayi/Anak
            ['section' => 'Data Bayi/Anak', 'label' => 'NIK', 'key' => 'nik_anak', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Bayi/Anak', 'label' => 'Nama Lengkap', 'key' => 'nama_anak', 'type' => 'text'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin_anak', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['section' => 'Data Bayi/Anak', 'label' => 'Tempat Dilahirkan', 'key' => 'tempat_dilahirkan', 'type' => 'select', 'options' => ['Rumah Sakit', 'Puskesmas', 'Rumah Bersalin', 'Rumah', 'Lainnya']],
            ['section' => 'Data Bayi/Anak', 'label' => 'Tempat Kelahiran (Kota)', 'key' => 'tempat_kelahiran_kota', 'type' => 'text'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Hari/Tanggal Lahir', 'key' => 'hari_tanggal_lahir', 'type' => 'text'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Waktu/Jam Kelahiran', 'key' => 'jam_kelahiran', 'type' => 'text'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Jenis Kelahiran', 'key' => 'jenis_kelahiran', 'type' => 'select', 'options' => ['Tunggal', 'Kembar Dua', 'Kembar Tiga', 'Kembar Empat atau Lebih']],
            ['section' => 'Data Bayi/Anak', 'label' => 'Anak Ke (dengan huruf)', 'key' => 'anak_ke', 'type' => 'text'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Penolong Kelahiran', 'key' => 'penolong_kelahiran', 'type' => 'select', 'options' => ['Dokter', 'Bidan', 'Dukun', 'Lainnya']],
            ['section' => 'Data Bayi/Anak', 'label' => 'Berat Bayi (Kg)', 'key' => 'berat_bayi', 'type' => 'number'],
            ['section' => 'Data Bayi/Anak', 'label' => 'Panjang Bayi (Cm)', 'key' => 'panjang_bayi', 'type' => 'number'],
        
            // Ibu
            ['section' => 'Data Ibu', 'label' => 'NIK', 'key' => 'nik_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Nama Lengkap', 'key' => 'nama_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Alamat', 'key' => 'alamat_ibu', 'type' => 'textarea'],
            ['section' => 'Data Ibu', 'label' => 'RT', 'key' => 'rt_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'RW', 'key' => 'rw_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ibu', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Tempat Pencatatan Perkawinan', 'key' => 'tempat_pencatatan_perkawinan', 'type' => 'text'],
            ['section' => 'Data Ibu', 'label' => 'Tanggal Pencatatan Perkawinan', 'key' => 'tanggal_pencatatan_perkawinan', 'type' => 'date'],
        
            // Ayah
            ['section' => 'Data Ayah', 'label' => 'NIK', 'key' => 'nik_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Nama Lengkap', 'key' => 'nama_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ayah', 'type' => 'text'],
            ['section' => 'Data Ayah', 'label' => 'Alamat', 'key' => 'alamat_ayah', 'type' => 'textarea'],
            ['section' => 'Data Ayah', 'label' => 'RT', 'key' => 'rt_ayah', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ayah', 'label' => 'RW', 'key' => 'rw_ayah', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ayah', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ayah', 'type' => 'text'],
        
            // Pelapor
            ['section' => 'Data Pelapor', 'label' => 'NIK', 'key' => 'nik_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Nama Lengkap', 'key' => 'nama_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Umur', 'key' => 'umur_pelapor', 'type' => 'number'],
            ['section' => 'Data Pelapor', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Alamat', 'key' => 'alamat_pelapor', 'type' => 'textarea'],
            ['section' => 'Data Pelapor', 'label' => 'Tanggal Lapor', 'key' => 'tanggal_lapor', 'type' => 'date'],
        
            // Saksi I
            ['section' => 'Saksi I', 'label' => 'NIK', 'key' => 'nik_saksi_1', 'type' => 'text'],
            ['section' => 'Saksi I', 'label' => 'Nama Lengkap', 'key' => 'nama_saksi_1', 'type' => 'text'],
            ['section' => 'Saksi I', 'label' => 'Umur', 'key' => 'umur_saksi_1', 'type' => 'number'],
            ['section' => 'Saksi I', 'label' => 'Alamat', 'key' => 'alamat_saksi_1', 'type' => 'text'],
        
            // Saksi II
            ['section' => 'Saksi II', 'label' => 'NIK', 'key' => 'nik_saksi_2', 'type' => 'text'],
            ['section' => 'Saksi II', 'label' => 'Nama Lengkap', 'key' => 'nama_saksi_2', 'type' => 'text'],
            ['section' => 'Saksi II', 'label' => 'Umur', 'key' => 'umur_saksi_2', 'type' => 'number'],
            ['section' => 'Saksi II', 'label' => 'Alamat', 'key' => 'alamat_saksi_2', 'type' => 'text'],
        
            // Keterangan tambahan spesifik surat ini
            ['label' => 'Pihak yang Berdomisili di Luar Bimomartani', 'key' => 'pihak_luar_domisili', 'type' => 'select', 'options' => ['Ayah', 'Ibu', 'Ayah dan Ibu']],
        ]);
        $this->seedForCode('SPTJMPSI', [ // SPTJM Kebenaran Sebagai Pasangan Suami Istri 
            // Pelapor / yang membuat pernyataan
            ['section' => 'Pelapor', 'label' => 'Nama', 'key' => 'nama_pelapor', 'type' => 'text'],
            ['section' => 'Pelapor', 'label' => 'NIK', 'key' => 'nik_pelapor', 'type' => 'text'],
            ['section' => 'Pelapor', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_pelapor', 'type' => 'text'],
            ['section' => 'Pelapor', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_pelapor', 'type' => 'text'],
            ['section' => 'Pelapor', 'label' => 'Alamat', 'key' => 'alamat_pelapor', 'type' => 'textarea'],
            ['section' => 'Pelapor', 'label' => 'Status Hubungan Keluarga dengan Pasangan', 'key' => 'status_hubungan_keluarga', 'type' => 'select', 'options' => ['Suami', 'Istri', 'Anak']],
        
            // Pihak pertama yang dinyatakan (bisa = pelapor sendiri kalau pelapor = suami/istri)
            ['section' => 'Pihak Pertama', 'label' => 'Nama', 'key' => 'nama_pihak_pertama', 'type' => 'text'],
            ['section' => 'Pihak Pertama', 'label' => 'NIK', 'key' => 'nik_pihak_pertama', 'type' => 'text', 'is_required' => false],
            ['section' => 'Pihak Pertama', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_pihak_pertama', 'type' => 'text', 'is_required' => false],
            ['section' => 'Pihak Pertama', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_pihak_pertama', 'type' => 'text', 'is_required' => false],
            ['section' => 'Pihak Pertama', 'label' => 'Alamat', 'key' => 'alamat_pihak_pertama', 'type' => 'textarea', 'is_required' => false],
        
            // Pasangan (suami/istri dari pihak pertama)
            ['section' => 'Pasangan', 'label' => 'Nama', 'key' => 'nama_pasangan', 'type' => 'text'],
            ['section' => 'Pasangan', 'label' => 'NIK', 'key' => 'nik_pasangan', 'type' => 'text', 'is_required' => false],
            ['section' => 'Pasangan', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_pasangan', 'type' => 'text', 'is_required' => false],
            ['section' => 'Pasangan', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_pasangan', 'type' => 'text', 'is_required' => false],
            ['section' => 'Pasangan', 'label' => 'Alamat', 'key' => 'alamat_pasangan', 'type' => 'textarea'],
        
            ['label' => 'Nomor Kartu Keluarga (KK)', 'key' => 'nomor_kk', 'type' => 'text'],
        
            // Saksi I & II
            ['section' => 'Saksi I', 'label' => 'Nama', 'key' => 'nama_saksi_1', 'type' => 'text'],
            ['section' => 'Saksi I', 'label' => 'NIK', 'key' => 'nik_saksi_1', 'type' => 'text'],
            ['section' => 'Saksi II', 'label' => 'Nama', 'key' => 'nama_saksi_2', 'type' => 'text'],
            ['section' => 'Saksi II', 'label' => 'NIK', 'key' => 'nik_saksi_2', 'type' => 'text'],
        ]);

    // KEMATIAN BARU
        $this->seedForCode('FPK', [ // Formulir Pelaporan Kematian — cek nama kode asli
            ['section' => 'Data Keluarga', 'label' => 'Nomor Kartu Keluarga', 'key' => 'nomor_kk', 'type' => 'text'],
            ['section' => 'Data Keluarga', 'label' => 'Nama Kepala Keluarga', 'key' => 'nama_kepala_keluarga', 'type' => 'text'],
        
            // Jenazah
            ['section' => 'Data Jenazah', 'label' => 'NIK', 'key' => 'nik_jenazah', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Nama Lengkap', 'key' => 'nama_jenazah', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin_jenazah', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['section' => 'Data Jenazah', 'label' => 'Tempat Kelahiran', 'key' => 'tempat_kelahiran_jenazah', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_jenazah', 'type' => 'date'],
            ['section' => 'Data Jenazah', 'label' => 'Umur', 'key' => 'umur_jenazah', 'type' => 'number'],
            ['section' => 'Data Jenazah', 'label' => 'Agama', 'key' => 'agama_jenazah', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Jenazah', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_jenazah', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Alamat', 'key' => 'alamat_jenazah', 'type' => 'textarea'],
            ['section' => 'Data Jenazah', 'label' => 'Anak Ke (dengan huruf)', 'key' => 'anak_ke_jenazah', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Jenazah', 'label' => 'Meninggal Hari/Tanggal', 'key' => 'meninggal_hari_tanggal', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Jam Meninggal', 'key' => 'jam_meninggal', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Sebab Kematian', 'key' => 'sebab_kematian', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Tempat Kematian', 'key' => 'tempat_kematian', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Yang Menerangkan (Hubungan)', 'key' => 'yang_menerangkan', 'type' => 'select', 'options' => ['Suami', 'Istri', 'Anak', 'Orang Tua', 'Saudara', 'Lainnya']],
        
            // Ibu Jenazah
            ['section' => 'Data Ibu', 'label' => 'NIK', 'key' => 'nik_ibu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ibu', 'label' => 'Nama Lengkap', 'key' => 'nama_ibu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ibu', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_ibu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ibu', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ibu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ibu', 'label' => 'Alamat (Sesuai KTP)', 'key' => 'alamat_ibu', 'type' => 'textarea', 'is_required' => false],
            ['section' => 'Data Ibu', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ibu', 'type' => 'text', 'is_required' => false],
        
            // Ayah Jenazah
            ['section' => 'Data Ayah', 'label' => 'NIK', 'key' => 'nik_ayah', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ayah', 'label' => 'Nama Lengkap', 'key' => 'nama_ayah', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ayah', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_ayah', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ayah', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ayah', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ayah', 'label' => 'Alamat', 'key' => 'alamat_ayah', 'type' => 'textarea', 'is_required' => false],
            ['section' => 'Data Ayah', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ayah', 'type' => 'text', 'is_required' => false],
        
            // Pelapor
            ['section' => 'Data Pelapor', 'label' => 'NIK', 'key' => 'nik_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Nama Lengkap', 'key' => 'nama_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Umur', 'key' => 'umur_pelapor', 'type' => 'number'],
            ['section' => 'Data Pelapor', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Alamat', 'key' => 'alamat_pelapor', 'type' => 'textarea'],
            ['section' => 'Data Pelapor', 'label' => 'Tanggal Lapor', 'key' => 'tanggal_lapor', 'type' => 'date'],
        
            // Saksi I
            ['section' => 'Saksi I', 'label' => 'NIK', 'key' => 'nik_saksi_1', 'type' => 'text'],
            ['section' => 'Saksi I', 'label' => 'Nama Lengkap', 'key' => 'nama_saksi_1', 'type' => 'text'],
            ['section' => 'Saksi I', 'label' => 'Umur', 'key' => 'umur_saksi_1', 'type' => 'number'],
            ['section' => 'Saksi I', 'label' => 'Alamat', 'key' => 'alamat_saksi_1', 'type' => 'text'],
        
            // Saksi II
            ['section' => 'Saksi II', 'label' => 'NIK', 'key' => 'nik_saksi_2', 'type' => 'text'],
            ['section' => 'Saksi II', 'label' => 'Nama Lengkap', 'key' => 'nama_saksi_2', 'type' => 'text'],
            ['section' => 'Saksi II', 'label' => 'Umur', 'key' => 'umur_saksi_2', 'type' => 'number'],
            ['section' => 'Saksi II', 'label' => 'Alamat', 'key' => 'alamat_saksi_2', 'type' => 'text'],
        ]);
        $this->seedForCode('SKKM', [ // Surat Keterangan Kematian 
            ['section' => 'Data Keluarga', 'label' => 'Nomor Kartu Keluarga', 'key' => 'nomor_kk', 'type' => 'text'],
            ['section' => 'Data Keluarga', 'label' => 'Nama Kepala Keluarga', 'key' => 'nama_kepala_keluarga', 'type' => 'text'],
        
            // Jenazah
            ['section' => 'Data Jenazah', 'label' => 'NIK', 'key' => 'nik_jenazah', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Nama Lengkap', 'key' => 'nama_jenazah', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Jenis Kelamin', 'key' => 'jenis_kelamin_jenazah', 'type' => 'select', 'options' => ['Laki-laki', 'Perempuan']],
            ['section' => 'Data Jenazah', 'label' => 'Tempat Kelahiran', 'key' => 'tempat_kelahiran_jenazah', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Tanggal Lahir', 'key' => 'tanggal_lahir_jenazah', 'type' => 'date'],
            ['section' => 'Data Jenazah', 'label' => 'Umur', 'key' => 'umur_jenazah', 'type' => 'number'],
            ['section' => 'Data Jenazah', 'label' => 'Agama', 'key' => 'agama_jenazah', 'type' => 'select', 'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']],
            ['section' => 'Data Jenazah', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_jenazah', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Alamat', 'key' => 'alamat_jenazah', 'type' => 'textarea'],
            ['section' => 'Data Jenazah', 'label' => 'Anak Ke (dengan huruf)', 'key' => 'anak_ke_jenazah', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Jenazah', 'label' => 'Meninggal Hari/Tanggal', 'key' => 'meninggal_hari_tanggal', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Lokasi Meninggal (mis. Rumah/RS)', 'key' => 'lokasi_meninggal', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Kota/Kabupaten Tempat Meninggal', 'key' => 'kota_tempat_meninggal', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Jam Meninggal', 'key' => 'jam_meninggal', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Sebab Kematian', 'key' => 'sebab_kematian', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Tempat Kematian (Kabupaten)', 'key' => 'tempat_kematian', 'type' => 'text'],
            ['section' => 'Data Jenazah', 'label' => 'Yang Menerangkan (Hubungan)', 'key' => 'yang_menerangkan', 'type' => 'select', 'options' => ['Suami', 'Istri', 'Anak', 'Orang Tua', 'Saudara', 'Lainnya']],
        
            // Ibu
            ['section' => 'Data Ibu', 'label' => 'NIK', 'key' => 'nik_ibu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ibu', 'label' => 'Nama Lengkap', 'key' => 'nama_ibu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ibu', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_ibu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ibu', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ibu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ibu', 'label' => 'Alamat (Sesuai KTP)', 'key' => 'alamat_ibu', 'type' => 'textarea', 'is_required' => false],
            ['section' => 'Data Ibu', 'label' => 'RW', 'key' => 'rw_ibu', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ibu', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ibu', 'type' => 'text', 'is_required' => false],
        
            // Ayah
            ['section' => 'Data Ayah', 'label' => 'NIK', 'key' => 'nik_ayah', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ayah', 'label' => 'Nama Lengkap', 'key' => 'nama_ayah', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ayah', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_ayah', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ayah', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_ayah', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ayah', 'label' => 'Alamat (Sesuai KTP)', 'key' => 'alamat_ayah', 'type' => 'textarea', 'is_required' => false],
            ['section' => 'Data Ayah', 'label' => 'RW', 'key' => 'rw_ayah', 'type' => 'text', 'is_required' => false],
            ['section' => 'Data Ayah', 'label' => 'Kewarganegaraan', 'key' => 'kewarganegaraan_ayah', 'type' => 'text', 'is_required' => false],
        
            // Pelapor
            ['section' => 'Data Pelapor', 'label' => 'NIK', 'key' => 'nik_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Nama Lengkap', 'key' => 'nama_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Tempat/Tanggal Lahir', 'key' => 'ttl_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Umur', 'key' => 'umur_pelapor', 'type' => 'number'],
            ['section' => 'Data Pelapor', 'label' => 'Pekerjaan', 'key' => 'pekerjaan_pelapor', 'type' => 'text'],
            ['section' => 'Data Pelapor', 'label' => 'Alamat (Sesuai KTP)', 'key' => 'alamat_pelapor', 'type' => 'textarea'],
            ['section' => 'Data Pelapor', 'label' => 'Tanggal Lapor', 'key' => 'tanggal_lapor', 'type' => 'date'],
        
            // Saksi I
            ['section' => 'Saksi I', 'label' => 'NIK', 'key' => 'nik_saksi_1', 'type' => 'text'],
            ['section' => 'Saksi I', 'label' => 'Nama Lengkap', 'key' => 'nama_saksi_1', 'type' => 'text'],
            ['section' => 'Saksi I', 'label' => 'Umur', 'key' => 'umur_saksi_1', 'type' => 'number'],
            ['section' => 'Saksi I', 'label' => 'Alamat', 'key' => 'alamat_saksi_1', 'type' => 'text'],
        
            // Saksi II
            ['section' => 'Saksi II', 'label' => 'NIK', 'key' => 'nik_saksi_2', 'type' => 'text'],
            ['section' => 'Saksi II', 'label' => 'Nama Lengkap', 'key' => 'nama_saksi_2', 'type' => 'text'],
            ['section' => 'Saksi II', 'label' => 'Umur', 'key' => 'umur_saksi_2', 'type' => 'number'],
            ['section' => 'Saksi II', 'label' => 'Alamat', 'key' => 'alamat_saksi_2', 'type' => 'text'],
        ]);
    }

    private function seedForCode(string $code, array $fields): void
    {
        // Some legacy blocks contain the same code twice. The first block is
        // intentionally canonical; later blocks must not silently append a
        // second definition for the same letter type.
        if (isset($this->canonicalCodes[$code])) {
            return;
        }

        $this->canonicalCodes[$code] = true;

        $letterTypeId = DB::table('letter_types')
            ->where('code', $code)
            ->value('letter_type_id');

        if (!$letterTypeId) {
            throw new \RuntimeException("Letter type [{$code}] must exist before its fields are seeded.");
        }
    
        $alreadySeeded = DB::table('letter_type_fields')->where('letter_type_id', $letterTypeId)->exists();
        if ($alreadySeeded) return;
    
        foreach ($fields as $i => $f) {
            DB::table('letter_type_fields')->insert([
                'letter_type_id' => $letterTypeId,
                'section_name' => $f['section'] ?? null,
                'field_label' => $f['label'],
                'field_key' => $f['key'],
                'field_type' => $f['type'],
                'is_required' => $f['is_required'] ?? true,
                'options' => isset($f['options']) ? json_encode($f['options']) : null,
                'sort_order' => $i + 1,
                'created_at' => now(),
            ]);
        }
    }
}
