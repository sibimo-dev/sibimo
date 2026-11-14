<?php

namespace App\Console\Commands;

use App\Models\Citizen;
use App\Models\Region;
use Illuminate\Console\Command;

class BackfillCitizenAreas extends Command
{
    protected $signature = 'citizens:backfill-area-relations
        {--dry-run : Tampilkan hasil tanpa mengubah database}';

    protected $description = 'Mengisi rt_id warga lama dari nama padukuhan dan nomor RT/RW yang cocok';

    public function handle(): int
    {
        $normalizeName = static fn ($value): string => mb_strtolower(trim((string) $value), 'UTF-8');
        $normalizeNumber = static fn ($value): string => preg_replace('/^0+/', '', trim((string) $value)) ?: '0';

        $regions = Region::query()->with('rws.rts')->get();
        $regionsByName = $regions->keyBy(fn (Region $region): string => $normalizeName($region->name));

        $matched = 0;
        $ambiguous = 0;
        $unmatched = 0;

        Citizen::query()
            ->whereNull('rt_id')
            ->orderBy('citizen_id')
            ->chunkById(500, function ($citizens) use (&$matched, &$ambiguous, &$unmatched, $regionsByName, $normalizeName, $normalizeNumber): void {
                foreach ($citizens as $citizen) {
                    // Data lama tidak memiliki kolom dusun terpisah. Hanya
                    // gunakan address bila sama persis dengan master wilayah;
                    // jangan menebak dari alamat jalan bebas.
                    $region = $regionsByName->get($normalizeName($citizen->address));
                    if (! $region) {
                        $unmatched++;
                        continue;
                    }

                    $rtNumber = trim((string) $citizen->rt);
                    if ($rtNumber === '') {
                        $unmatched++;
                        continue;
                    }

                    $candidates = $region->rws
                        ->flatMap(fn ($rw) => $rw->rts)
                        ->filter(fn ($rt) => $normalizeNumber($rt->number) === $normalizeNumber($rtNumber))
                        ->values();

                    // Jika RT yang sama ada di beberapa RW, jangan memilih
                    // secara acak karena data lama tidak memiliki nomor RW.
                    if ($candidates->count() !== 1) {
                        $candidates->count() > 1 ? $ambiguous++ : $unmatched++;
                        continue;
                    }

                    $rt = $candidates->first();
                    $matched++;

                    if (! $this->option('dry-run')) {
                        $citizen->forceFill([
                            'rt_id' => $rt->id,
                            'rt' => $rt->number,
                        ])->saveQuietly();
                    }
                }
            }, 'citizen_id');

        $this->info("Berhasil dicocokkan: {$matched}");
        $this->line("Tidak ditemukan: {$unmatched}");
        $this->line("Ambigu karena RT sama di beberapa RW: {$ambiguous}");
        if ($this->option('dry-run')) {
            $this->comment('Dry-run: tidak ada data yang diubah.');
        }

        return self::SUCCESS;
    }
}
