<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            $now = now();
            $regions = DB::table('regions')->get(['region_id', 'name']);
            $all = DB::table('staff')->orderBy('staff_id')->get();
            $signers = $all->where('is_signer', 1)->values();
            $people = $all->where('is_signer', 0)->values();
            $pairedSignerIds = [];

            foreach ($people as $s) {
                // Baris penandatangan (is_signer = 1) dengan nama+jabatan sama dilebur ke orang ini.
                $sig = $signers->first(fn ($x) => $x->name === $s->name);                
                if ($sig) $pairedSignerIds[] = $sig->staff_id;

                $this->insertPerson($s, $sig, (bool) $sig, $regions, $now);
            }

            // Baris penandatangan tanpa pasangan tetap dibawa supaya tidak hilang.
            foreach ($signers as $x) {
                if (in_array($x->staff_id, $pairedSignerIds, true)) continue;
                $this->insertPerson($x, null, true, $regions, $now);
            }
        });
    }

    private function insertPerson($s, $sig, bool $isSigner, $regions, $now): void
    {
        // id staffs = staff_id lama, jadi staff_id yang dikirim FE (758, 759, ...) tetap cocok.
        DB::table('staffs')->insert([
            'id' => $s->staff_id,
            'name' => $s->name,
            'photo' => $s->photo,
            'signature_image' => $s->signature_image ?: ($sig->signature_image ?? null),
            'created_at' => $s->created_at ?? $now,
            'updated_at' => $now,
        ]);

        DB::table('staff_assignments')->insert([
            'staff_id' => $s->staff_id,
            'position' => $s->position,
            'padukuhan_id' => $this->matchRegion($s->position, $regions),
            'start_date' => $s->start_year ? "{$s->start_year}-01-01" : null,
            'end_date' => $s->end_year ? "{$s->end_year}-12-31" : null,
            'description' => $s->description,
            'is_signer' => $isSigner,
            'created_at' => $s->created_at ?? $now,
            'updated_at' => $now,
        ]);
    }

    // "Dukuh I Krebet" -> cari region bernama "Krebet".
    private function matchRegion(string $position, $regions): ?int
    {
        if (!preg_match('/^Dukuh\s+[IVXL]+\s+(.+)$/i', $position, $m)) return null;
        $name = mb_strtolower(trim($m[1]));
        $hit = $regions->first(fn ($r) => mb_strtolower($r->name) === $name)
            ?? $regions->first(fn ($r) => str_contains(mb_strtolower($r->name), $name)
                || str_contains($name, mb_strtolower($r->name)));
        return $hit?->region_id;
    }

    public function down(): void
    {
        DB::table('staff_assignments')->delete();
        DB::table('staffs')->delete();
    }
};
