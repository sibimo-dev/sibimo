<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VillagePotentialSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan data dummy generic lama jika ada
        DB::table('village_potentials')->where('slug', 'like', 'seed-potential-%')->delete();

        $potentials = [
            [
                'slug' => 'batik-permata-ukmk-bimomartani',
                'category' => 'UMKM',
                'title' => 'Batik Permata UKMK Bimomartani',
                'short_desc' => 'Salah satu produk unggulan UMKM di Kalurahan Bimomartani adalah Batik Permata di bawah naungan kelompok UMKM Desa Prima.',
                'description' => 'Adapun beberapa produk unggulan merupakan ciri khas Batik Parijoto Sleman DIY. Bagi customer yang berminat produk Batik Parijoto buatan Batik Permata dapat langsung datang ke Padukuhan Sorasan RT 04/ RW 25, Bimomartani, Ngemplak, Sleman.',
                'image' => 'village-potentials/potential-umkm.jpg',
                'location' => 'Padukuhan Sorasan, Bimomartani',
                'contact' => '0812-xxxx-xxxx (Ibu Siti, Ketua Kelompok UMKM)',
                'extra_info' => [
                    'Harga Mulai' => 'Rp45.000 / 100gr',
                    'Jam Operasional' => '08.00 – 17.00 WIB',
                    'Kapasitas Produksi' => '±20 kg / bulan',
                ],
            ],
            [
                'slug' => 'penanaman-kelapa-genjah',
                'category' => 'Agriculture',
                'title' => 'Penanaman Kelapa Genjah Sebagai Alternatif Tanaman Lahan Kering',
                'short_desc' => 'Kegiatan penanaman dan serah terima bantuan Bibit Kelapa Genjah kepada Gapoktan “Bimo Makmur” Kalurahan Bimomartani.',
                'description' => 'Pada hari ini Selasa 07 Oktober 2025 dilakukan kegiatan penanaman dan serah terima bantuan Bibit Kelapa Genjah dari Kementerian Pertanian dan Dinas Pertanian Kabupaten Sleman kepada Gapoktan “Bimo Makmur” Kalurahan Bimomartani di Komplek Embung Bimomartani. Rencananya penanaman bibit kelapa genjah ini akan tersebar seluas 10 hektar di wilayah Kalurahan Bimomartani. Dengan kegiatan tersebut harapannya Bimomartani dapat menjadi lumbung/sentra hasil pertanian kelapa genjah untuk wilayah Kapanewon Ngemplak.',
                'image' => 'village-potentials/potential-agriculture.jpeg',
                'location' => 'Komplek Embung Bimomartani, Bimomartani',
                'contact' => 'Kelompok Tani Makmur — Bapak Wagiyo (Ketua)',
                'extra_info' => [
                    'Luas Lahan' => '±12 Ha',
                    'Komoditas' => 'Buah Segar, Kelapa Kopyor, Bibit Pohon',
                    'Musim Panen' => '3x per tahun',
                ],
            ],
            [
                'slug' => 'embung-bimomartani',
                'category' => 'Tourism',
                'title' => 'Embung Bimomartani',
                'short_desc' => 'Destinasi wisata air buatan yang menawarkan pemandangan asri, spot memancing, dan area rekreasi keluarga.',
                'description' => 'Embung Bimomartani merupakan embung (kolam penampung air) yang disulap menjadi destinasi wisata air oleh Kelompok Sadar Wisata (Pokdarwis) setempat. Selain berfungsi sebagai penampung air untuk irigasi pertanian, area ini juga dilengkapi gazebo, spot memancing, dan jalur jogging mengelilingi embung yang ramai dikunjungi warga di akhir pekan.',
                'image' => 'village-potentials/potential-tourism.jpg',
                'location' => 'Padukuhan Nengahan, Bimomartani',
                'contact' => 'Pokdarwis Bimomartani',
                'extra_info' => [
                    'Jam Buka' => '06.00 – 18.00 WIB',
                    'Tiket Masuk' => 'Rp5.000 / orang',
                    'Fasilitas' => 'Gazebo, spot memancing, area parkir',
                ],
            ],
            [
                'slug' => 'toko-desa-mandiri',
                'category' => 'BUMDes',
                'title' => 'Toko Desa Mandiri',
                'short_desc' => 'Pusat perbelanjaan kebutuhan pokok dan produk lokal yang dikelola oleh BUMDes Bimomartani.',
                'description' => 'Toko Desa Mandiri adalah unit usaha BUMDes Bimomartani Sejahtera yang menyediakan kebutuhan pokok sehari-hari dengan harga terjangkau, sekaligus menjadi etalase produk UMKM lokal seperti kopi, kerajinan tangan, dan hasil pertanian warga. Keuntungan dari unit usaha ini dikembalikan untuk mendukung program pembangunan dan pemberdayaan masyarakat desa.',
                'image' => 'village-potentials/potential-bumdes.jpeg',
                'location' => 'Kompleks Balai Desa Bimomartani',
                'contact' => 'BUMDes Bimomartani Sejahtera',
                'extra_info' => [
                    'Jam Operasional' => '07.00 – 20.00 WIB',
                    'Produk Unggulan' => 'Sembako, kopi lokal, kerajinan tangan',
                ],
            ],
        ];

        foreach ($potentials as $item) {
            DB::table('village_potentials')->updateOrInsert(
                ['slug' => $item['slug']],
                [
                    'category' => $item['category'],
                    'title' => $item['title'],
                    'short_desc' => $item['short_desc'],
                    'description' => $item['description'],
                    'image' => $item['image'],
                    'location' => $item['location'],
                    'contact' => $item['contact'],
                    'extra_info' => json_encode($item['extra_info']),
                    'created_at' => now(),
                ]
            );
        }
    }
}
