<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('village_potentials', function (Blueprint $table) {
            $table->string('slug', 220)->nullable()->unique()->after('title');
            $table->string('short_desc', 255)->nullable()->after('slug');
            $table->string('contact', 255)->nullable()->after('location');
            $table->json('extra_info')->nullable()->after('contact');
        });
    }

    public function down(): void
    {
        Schema::table('village_potentials', function (Blueprint $table) {
            $table->dropColumn(['slug', 'short_desc', 'contact', 'extra_info']);
        });
    }
};