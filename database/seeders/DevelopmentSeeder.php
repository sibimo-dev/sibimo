<?php

namespace Database\Seeders;

use App\Models\Development;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Peningkatan Jalan Usaha Tani Bimomartani', 'category' => 'infrastruktur', 'status' => 'selesai', 'address' => 'Padukuhan Krajan, Bimomartani', 'budget' => 185000000, 'volume' => 850, 'volume_unit' => 'meter', 'funding_source' => 'Dana Desa', 'executor' => 'TPK Kalurahan', 'year' => 2026, 'start_date' => '2026-02-10', 'target_date' => '2026-05-30', 'description' => 'Peningkatan jalan usaha tani untuk memperlancar akses warga dan distribusi hasil pertanian.', 'latitude' => -7.7012, 'longitude' => 110.4630],
            ['name' => 'Renovasi Posyandu Bimomartani', 'category' => 'kesehatan', 'status' => 'sedang_berjalan', 'address' => 'Padukuhan Ngemplak, Bimomartani', 'budget' => 76000000, 'volume' => 1, 'volume_unit' => 'paket', 'funding_source' => 'APBKal', 'executor' => 'Pemerintah Kalurahan', 'year' => 2026, 'start_date' => '2026-07-01', 'target_date' => '2026-10-31', 'description' => 'Renovasi sarana posyandu untuk mendukung pelayanan kesehatan ibu dan anak.', 'latitude' => -7.7020, 'longitude' => 110.4640],
            ['name' => 'Pengadaan Buku Taman Baca Kalurahan', 'category' => 'pendidikan', 'status' => 'perencanaan', 'address' => 'Kalurahan Bimomartani', 'budget' => 45000000, 'volume' => 1, 'volume_unit' => 'paket', 'funding_source' => 'APBKal', 'executor' => 'Pemerintah Kalurahan', 'year' => 2026, 'start_date' => '2026-11-01', 'target_date' => '2026-12-15', 'description' => 'Pengadaan bahan bacaan untuk meningkatkan minat literasi masyarakat.', 'latitude' => -7.7005, 'longitude' => 110.4624],
            ['name' => 'Pengelolaan Sampah Terpadu Bimomartani', 'category' => 'lingkungan', 'status' => 'perencanaan', 'address' => 'Kalurahan Bimomartani, Ngemplak', 'budget' => 120000000, 'volume' => 1, 'volume_unit' => 'paket', 'funding_source' => 'Dana Desa', 'executor' => 'Kelompok Masyarakat', 'year' => 2026, 'start_date' => '2026-11-01', 'target_date' => '2026-12-20', 'description' => 'Pengelolaan sampah terpadu berbasis masyarakat untuk menjaga kebersihan lingkungan.', 'latitude' => -7.7018, 'longitude' => 110.4647],
        ];

        $disk = Storage::disk('public');
        $pool = array_values(array_filter(array_merge($disk->files('galleries'), $disk->files('news')), fn (string $file) => in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true)));

        foreach ($items as $index => $data) {
            $slug = str($data['name'])->slug('-') . '-seed';
            $image = $pool ? $pool[$index % count($pool)] : null;
            Development::query()->updateOrCreate(['slug' => $slug], [
                ...$data, 'slug' => $slug, 'cover_image' => $image,
                'photo_0' => $data['status'] !== 'perencanaan' ? $image : null,
                'photo_50' => $data['status'] === 'selesai' ? $image : null,
                'photo_100' => $data['status'] === 'selesai' ? $image : null,
            ]);
        }
    }
}
