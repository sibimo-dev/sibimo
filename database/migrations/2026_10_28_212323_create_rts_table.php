<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rw_id')->constrained('rws')->cascadeOnDelete();
            $table->string('number', 10);
            $table->string('head_name', 100)->nullable();
            $table->string('head_phone', 20)->nullable();
            $table->timestamps();

            $table->unique(['rw_id', 'number']);
        });

        // Tambahkan FK constraint citizens.rt_id → rts.id setelah rts selesai dibuat.
        // (citizens dibuat lebih awal dalam urutan timestamp, sehingga constraint tidak
        //  bisa langsung ditambahkan di migration citizens.)
        Schema::table('citizens', function (Blueprint $table) {
            $table->foreign('rt_id')->references('id')->on('rts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('citizens', function (Blueprint $table) {
            $table->dropForeign(['rt_id']);
        });
        Schema::dropIfExists('rts');
    }
};
