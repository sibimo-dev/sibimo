<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('letter_type_fields', function (Blueprint $table) {
            $table->string('section_name', 100)->nullable()->after('letter_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('letter_type_fields', function (Blueprint $table) {
            $table->dropColumn('section_name');
        });
    }
};