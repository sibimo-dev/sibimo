<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->enum('category', ['musyawarah', 'kegiatan-sosial', 'pembangunan', 'budaya'])
                  ->default('kegiatan-sosial')
                  ->after('title');
            $table->string('location', 255)->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropColumn(['category', 'location']);
        });
    }
};