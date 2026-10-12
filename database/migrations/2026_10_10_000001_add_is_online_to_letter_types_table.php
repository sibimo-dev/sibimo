<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('letter_types', function (Blueprint $table) {
            $table->boolean('is_online')->default(false)->after('is_active');
            $table->index(['is_online', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('letter_types', function (Blueprint $table) {
            $table->dropIndex('letter_types_is_online_is_active_index');
            $table->dropColumn('is_online');
        });
    }
};
