<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id('region_id');
            $table->string('name');
            $table->string('head_name')->nullable();
            $table->foreignId('head_staff_id')->nullable()->constrained('staff', 'staff_id')->nullOnDelete();
            $table->unsignedInteger('rw_count')->default(0);
            $table->unsignedInteger('rt_count')->default(0);
            $table->unsignedInteger('kk_count')->default(0);
            $table->unsignedInteger('population')->default(0);
            $table->unsignedInteger('male_count')->default(0);
            $table->unsignedInteger('female_count')->default(0);
            $table->timestamps();
        });

        // Data master awal. Koroulon Lor tetap dipertahankan; Purwobinangun
        // merupakan padukuhan tambahan yang dipakai struktur organisasi.
        DB::table('regions')->insert([
            'name' => 'PURWOBINANGUN',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
