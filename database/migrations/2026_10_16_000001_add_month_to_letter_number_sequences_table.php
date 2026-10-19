<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('letter_number_sequences', 'month')) {
            Schema::table('letter_number_sequences', function (Blueprint $table) {
                $table->unsignedTinyInteger('month')->default(now()->month)->after('year');
            });
        }

        Schema::table('letter_number_sequences', function (Blueprint $table) {
            $table->index('letter_type_id');
            $table->dropUnique(['letter_type_id', 'year']);
            $table->unique(['letter_type_id', 'year', 'month']);
        });

        // Set month for existing records to current month
        DB::table('letter_number_sequences')->update(['month' => now()->month]);
    }

    public function down(): void
    {
        Schema::table('letter_number_sequences', function (Blueprint $table) {
            $table->dropUnique(['letter_type_id', 'year', 'month']);
            $table->unique(['letter_type_id', 'year']);
            $table->dropIndex(['letter_type_id']);
            $table->dropColumn('month');
        });
    }
};
