<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            $now = now();
            $cache = [];

            $person = function ($name, $phone) use (&$cache, $now) {
                $name = trim((string) $name);
                if ($name === '') return null;
                $phone = trim((string) $phone) ?: null;
                // Orang disatukan hanya bila nama dan nomor HP sama, supaya dua orang bernama sama tidak tertukar.
                $key = $phone ? mb_strtolower($name).'|'.$phone : null;
                if ($key && isset($cache[$key])) return $cache[$key];
                $id = DB::table('staffs')->insertGetId([
                    'name' => $name, 'phone' => $phone, 'created_at' => $now, 'updated_at' => $now,
                ]);
                if ($key) $cache[$key] = $id;
                return $id;
            };

            foreach (DB::table('rws')->get() as $rw) {
                if (!($id = $person($rw->head_name, $rw->head_phone))) continue;
                DB::table('staff_assignments')->insert([
                    'staff_id' => $id, 'position' => 'Ketua RW', 'rw_id' => $rw->id,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }

            foreach (DB::table('rts')->get() as $rt) {
                if (!($id = $person($rt->head_name, $rt->head_phone))) continue;
                DB::table('staff_assignments')->insert([
                    'staff_id' => $id, 'position' => 'Ketua RT', 'rt_id' => $rt->id,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        });
    }

    public function down(): void
    {
        DB::table('staff_assignments')->whereIn('position', ['Ketua RW', 'Ketua RT'])->delete();
        DB::table('staffs')->whereNotExists(fn ($q) => $q->from('staff_assignments')
            ->whereColumn('staff_assignments.staff_id', 'staffs.id'))->delete();
    }
};
