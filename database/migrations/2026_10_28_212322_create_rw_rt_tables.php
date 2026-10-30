<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kepala Dusun: tunjuk ke tabel staff. Kolom head_name lama tetap ada sebagai cadangan.
        Schema::table('regions', function (Blueprint $table) {
            $table->foreignId('head_staff_id')->nullable()->after('head_name')
                ->constrained('staff', 'staff_id')->nullOnDelete();
        });

        Schema::create('rws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('regions', 'region_id')->cascadeOnDelete();
            $table->string('number', 10);          // '01', '02', ... (sama dengan citizens.rw)
            $table->foreignId('head_staff_id')->nullable()
                ->constrained('staff', 'staff_id')->nullOnDelete();
            $table->timestamps();

            $table->unique(['region_id', 'number']);
        });

        Schema::create('rts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rw_id')->constrained('rws')->cascadeOnDelete();
            $table->string('number', 10);          // '01', '02', ... (sama dengan citizens.rt)
            $table->foreignId('head_staff_id')->nullable()
                ->constrained('staff', 'staff_id')->nullOnDelete();
            $table->timestamps();

            $table->unique(['rw_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rts');
        Schema::dropIfExists('rws');

        Schema::table('regions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('head_staff_id');
        });
    }
};