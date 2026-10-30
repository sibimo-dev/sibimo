<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rws', function (Blueprint $table) {
            $table->dropConstrainedForeignId('head_staff_id');
            $table->string('head_name', 100)->nullable()->after('number');
            $table->string('head_phone', 20)->nullable()->after('head_name');
        });

        Schema::table('rts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('head_staff_id');
            $table->string('head_name', 100)->nullable()->after('number');
            $table->string('head_phone', 20)->nullable()->after('head_name');
        });
    }

    public function down(): void
    {
        Schema::table('rts', function (Blueprint $table) {
            $table->dropColumn(['head_name', 'head_phone']);
            $table->foreignId('head_staff_id')->nullable()
                ->constrained('staff', 'staff_id')->nullOnDelete();
        });

        Schema::table('rws', function (Blueprint $table) {
            $table->dropColumn(['head_name', 'head_phone']);
            $table->foreignId('head_staff_id')->nullable()
                ->constrained('staff', 'staff_id')->nullOnDelete();
        });
    }
};