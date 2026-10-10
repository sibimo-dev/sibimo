<?php

namespace Database\Seeders;

use App\Models\Development;
use App\Models\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
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
