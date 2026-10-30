<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom data orang tua lengkap (selain NIK dan nama yang sudah ada).
     */
    private const PARENT_COLUMNS = [
        'birth_place' => ['string', 50],
        'birth_date' => ['date', null],
        'occupation' => ['string', 100],
        'religion' => ['string', 100],
        'nationality' => ['string', 50],
        'address' => ['text', null],
    ];

    public function up(): void
    {
        // Kolom baru. Dicek dulu supaya aman dijalankan di database yang sebagian
        // kolomnya sudah dibuat manual.
        Schema::table('citizens', function (Blueprint $table) {
            if (! Schema::hasColumn('citizens', 'disability_type')) {
                $table->string('disability_type', 100)->nullable();
            }

            foreach (['father', 'mother'] as $parent) {
                foreach (self::PARENT_COLUMNS as $suffix => [$type, $length]) {
                    $column = "{$parent}_{$suffix}";

                    if (Schema::hasColumn('citizens', $column)) {
                        continue;
                    }

                    match ($type) {
                        'string' => $table->string($column, $length)->nullable(),
                        'date' => $table->date($column)->nullable(),
                        'text' => $table->text($column)->nullable(),
                    };
                }
            }
        });

        // Status sekarang Hidup (Active) / Pindah / Mati. Kolom lama berupa enum
        // ('Active', 'Pindah'), jadi diubah menjadi string supaya nilai 'Mati' bisa disimpan.
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Di PostgreSQL, enum Laravel dibuat sebagai varchar + CHECK constraint.
            DB::statement('ALTER TABLE citizens DROP CONSTRAINT IF EXISTS citizens_status_check');
        }

        Schema::table('citizens', function (Blueprint $table) {
            $table->string('status', 20)->default('Active')->change();
            // 'Kepercayaan Terhadap Tuhan Yang Maha Esa' = 40 karakter, kolom lama hanya 30.
            $table->string('religion', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Lebar kolom status/religion sengaja tidak dikembalikan: data 'Mati' atau
        // agama yang panjang akan gagal / terpotong bila kolom diperkecil lagi.
        Schema::table('citizens', function (Blueprint $table) {
            $columns = ['disability_type'];

            foreach (['father', 'mother'] as $parent) {
                foreach (array_keys(self::PARENT_COLUMNS) as $suffix) {
                    $columns[] = "{$parent}_{$suffix}";
                }
            }

            $table->dropColumn(array_values(array_filter(
                $columns,
                fn (string $column) => Schema::hasColumn('citizens', $column),
            )));
        });
    }
};