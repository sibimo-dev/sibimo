<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citizens', function (Blueprint $table): void {
            $table->unsignedBigInteger('padukuhan_id')->nullable()->after('family_card_number');
            $table->unsignedBigInteger('rw_id')->nullable()->after('padukuhan_id');
            $table->unsignedBigInteger('rt_id')->nullable()->after('rw_id');

            $table->index('padukuhan_id');
            $table->index('rw_id');
            $table->index('rt_id');
        });

        Schema::table('citizens', function (Blueprint $table): void {
            $table->foreign('padukuhan_id')->references('region_id')->on('regions')->nullOnDelete();
            $table->foreign('rw_id')->references('id')->on('rws')->nullOnDelete();
            $table->foreign('rt_id')->references('id')->on('rts')->nullOnDelete();
        });

        // Migrasi data lama berdasarkan teks dusun/RW/RT. Kolom teks sengaja
        // dipertahankan sebagai fallback untuk data yang belum bisa dicocokkan.
        $regions = DB::table('regions')->get();
        $rws = DB::table('rws')->get()->groupBy('region_id');
        $rts = DB::table('rts')->get()->groupBy('rw_id');

        foreach (DB::table('citizens')->select('citizen_id', 'dusun', 'rw', 'rt')->get() as $citizen) {
            $region = $regions->first(fn ($row) => $this->same($row->name, $citizen->dusun));
            if (!$region) continue;

            $rw = $rws->get($region->region_id, collect())
                ->first(fn ($row) => $this->same($row->number, $citizen->rw));
            $rt = $rw ? $rts->get($rw->id, collect())
                ->first(fn ($row) => $this->same($row->number, $citizen->rt)) : null;

            DB::table('citizens')->where('citizen_id', $citizen->citizen_id)->update([
                'padukuhan_id' => $region->region_id,
                'rw_id' => $rw?->id,
                'rt_id' => $rt?->id,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('citizens', function (Blueprint $table): void {
            $table->dropForeign(['padukuhan_id']);
            $table->dropForeign(['rw_id']);
            $table->dropForeign(['rt_id']);
            $table->dropColumn(['padukuhan_id', 'rw_id', 'rt_id']);
        });
    }

    private function same(mixed $left, mixed $right): bool
    {
        $normalize = static fn ($value) => preg_replace('/^0+/', '', mb_strtolower(trim((string) $value))) ?: '0';

        return $normalize($left) === $normalize($right);
    }
};
