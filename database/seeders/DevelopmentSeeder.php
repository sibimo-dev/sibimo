<?php

namespace Database\Seeders;

use App\Models\Development;
use App\Models\DevelopmentProgress;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'slug' => 'rehabilitasi-jalan-lingkungan-bimomartani',
                'name' => 'Rehabilitasi Jalan Lingkungan Bimomartani',
                'address' => 'Padukuhan Krajan, Kalurahan Bimomartani',
                'category' => 'infrastruktur',
                'status' => 'selesai',
                'funding_source' => 'Dana Desa Tahun Anggaran 2026',
                'budget' => 185000000,
                'volume' => 850,
                'volume_unit' => 'meter',
                'executor' => 'Pemerintah Kalurahan Bimomartani',
                'year' => 2026,
                'start_date' => '2026-02-10',
                'target_date' => '2026-05-30',
                'description' => 'Rehabilitasi jalan lingkungan untuk meningkatkan akses warga menuju fasilitas umum dan area pertanian.',
                'latitude' => -7.7389000,
                'longitude' => 110.4657000,
            ],
            [
                'slug' => 'peningkatan-posyandu-bimomartani',
                'name' => 'Peningkatan Sarana Posyandu Bimomartani',
                'address' => 'Padukuhan Ngemplak, Kalurahan Bimomartani',
                'category' => 'kesehatan',
                'status' => 'sedang_berjalan',
                'funding_source' => 'APBKal Bimomartani Tahun Anggaran 2026',
                'budget' => 76000000,
                'volume' => 1,
                'volume_unit' => 'paket',
                'executor' => 'Tim Pelaksana Kegiatan Kalurahan',
                'year' => 2026,
                'start_date' => '2026-07-01',
                'target_date' => '2026-10-31',
                'description' => 'Peningkatan sarana posyandu untuk mendukung pelayanan kesehatan ibu dan anak di tingkat kalurahan.',
                'latitude' => -7.7396000,
                'longitude' => 110.4671000,
            ],
            [
                'slug' => 'pengelolaan-sampah-terpadu-bimomartani',
                'name' => 'Pengelolaan Sampah Terpadu Bimomartani',
                'address' => 'Kalurahan Bimomartani, Kapanewon Ngemplak',
                'category' => 'lingkungan',
                'status' => 'perencanaan',
                'funding_source' => 'Dana Keistimewaan dan Swadaya Masyarakat',
                'budget' => 120000000,
                'volume' => 1,
                'volume_unit' => 'paket',
                'executor' => 'Pemerintah Kalurahan bersama Kelompok Masyarakat',
                'year' => 2026,
                'start_date' => '2026-11-01',
                'target_date' => '2026-12-20',
                'description' => 'Perencanaan pengelolaan sampah terpadu berbasis masyarakat untuk mengurangi sampah rumah tangga dan meningkatkan kebersihan lingkungan.',
                'latitude' => -7.7402000,
                'longitude' => 110.4664000,
            ],
        ];

        foreach ($items as $item) {
            $development = Development::query()->updateOrCreate(
                ['slug' => $item['slug']],
                $item,
            );

            foreach ([0, 50, 100] as $percentage) {
                DevelopmentProgress::query()->firstOrCreate(
                    ['development_id' => $development->development_id, 'percentage' => $percentage],
                );
            }
        }
        $creatorId = User::query()->value('user_id');
        $disk      = Storage::disk('public');

        // Sumber foto: file yang sudah ada di folder galleries dan news.
        $isImage = fn (string $f) => in_array(
            strtolower(pathinfo($f, PATHINFO_EXTENSION)),
            ['jpg', 'jpeg', 'png'],
            true
        );

        $pool = array_values(array_filter(
            array_merge($disk->files('galleries'), $disk->files('news')),
            $isImage
        ));

        $coverPool    = $pool;
        $progressPool = $pool;

        if (! $pool) {
            $this->command?->warn('Tidak ada foto di galleries atau news, kolom foto diisi null.');
        }

        // Tiap kategori punya 3 kegiatan: perencanaan, sedang_berjalan, selesai.
        $templates = [
            'infrastruktur' => [
                'names' => ['Peningkatan Jalan Usaha Tani', 'Pembangunan Drainase', 'Rehabilitasi Jalan Lingkungan'],
                'unit'  => 'meter',
            ],
            'kesehatan' => [
                'names' => ['Pembangunan Sumur Resapan', 'Pengadaan Alat Kesehatan Posbindu', 'Renovasi Posyandu'],
                'unit'  => 'unit',
            ],
            'pendidikan' => [
                'names' => ['Pengadaan Buku Taman Baca', 'Pembangunan Perpustakaan', 'Rehabilitasi Gedung PAUD'],
                'unit'  => 'm2',
            ],
            'lingkungan' => [
                'names' => ['Pengadaan Tong Sampah Terpilah', 'Pembangunan TPS3R', 'Penghijauan Bantaran Selokan'],
                'unit'  => 'unit',
            ],
        ];

        $statuses = ['perencanaan', 'sedang_berjalan', 'selesai'];
        $funding  = ['Dana Desa', 'APBKal', 'Bantuan Provinsi'];
        $executor = ['TPK Kalurahan', 'Swakelola Kalurahan', 'Pemerintah Kalurahan'];

        // Titik tengah Kalurahan Bimomartani, digeser sedikit tiap data.
        $centerLat = -7.7012;
        $centerLng = 110.4630;

        $i = 0;
        foreach ($templates as $category => $tpl) {
            foreach ($statuses as $s => $status) {
                $i++;
                $no   = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
                $name = "{$tpl['names'][$s]} Seeder {$no}";

                // Tanggal relatif terhadap hari ini supaya status selalu masuk akal.
                $start = match ($status) {
                    'selesai'         => now()->subMonths(8),
                    'sedang_berjalan' => now()->subMonths(3),
                    default           => now()->addMonth(),
                };
                $target = match ($status) {
                    'selesai'         => now()->subMonths(4),
                    'sedang_berjalan' => now()->addMonths(3),
                    default           => now()->addMonths(4),
                };
                $year = (int) $start->format('Y');
                $slug = Str::slug($name) . '-' . $year;

                $lat = $centerLat + (($i % 5) - 2) * 0.0015;
                $lng = $centerLng + ((intdiv($i, 2) % 5) - 2) * 0.0015;

                // Foto progres: perencanaan tidak ada, berjalan 0% dan 50%, selesai 0/50/100%.
                $levels = match ($status) {
                    'selesai'         => [0, 50, 100],
                    'sedang_berjalan' => [0, 50],
                    default           => [],
                };

                $photos = [
                    'cover_image' => $coverPool
                        ? $this->copyFrom($disk, $coverPool[$i % count($coverPool)], "developments/main/seeder-{$no}")
                        : null,
                    'photo_0'   => null,
                    'photo_50'  => null,
                    'photo_100' => null,
                ];

                foreach ($levels as $pct) {
                    $photos["photo_{$pct}"] = $progressPool
                        ? $this->copyFrom(
                            $disk,
                            $progressPool[($i + $pct) % count($progressPool)],
                            "developments/progress/seeder-{$no}-{$pct}"
                        )
                        : null;
                }

                Development::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name'           => $name,
                        'category'       => $category,
                        'status'         => $status,
                        'address'        => "Padukuhan Seeder {$no}, RT 0" . (($i % 5) + 1) . '/RW 1' . ($i % 4) . ', Bimomartani, Ngemplak, Sleman',
                        'description'    => "{$tpl['names'][$s]} sebagai data contoh untuk pengujian sistem SIBIMO.",
                        'budget'         => 40000000 + $i * 17000000,
                        'volume'         => 20 + $i * 15,
                        'volume_unit'    => $tpl['unit'],
                        'funding_source' => $funding[$i % 3],
                        'executor'       => $executor[$i % 3],
                        'year'           => $year,
                        'start_date'     => $start->toDateString(),
                        'target_date'    => $target->toDateString(),
                        'latitude'       => round($lat, 6),
                        'longitude'      => round($lng, 6),
                        'end_latitude'   => $status === 'perencanaan' ? null : round($lat - 0.0008, 6),
                        'end_longitude'  => $status === 'perencanaan' ? null : round($lng + 0.0008, 6),
                        'created_by'     => $creatorId,
                    ] + $photos,
                );
            }
        }
    }

    /**
     * Menyalin file sumber ke path baru milik data ini, lalu mengembalikan path-nya.
     * Dengan begitu tiap data punya file sendiri.
     */
    private function copyFrom(Filesystem $disk, string $source, string $targetBase): string
    {
        $ext    = pathinfo($source, PATHINFO_EXTENSION) ?: 'jpg';
        $target = "{$targetBase}.{$ext}";

        $disk->put($target, $disk->get($source));

        return $target;
    }
}
