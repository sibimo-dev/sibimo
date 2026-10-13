<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('excerpt', 255)->nullable()->after('title');
            $table->json('content_blocks')->nullable()->after('content');
            $table->boolean('is_popular')->default(false)->after('status');
            $table->boolean('is_pinned')->default(false)->after('is_popular');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['excerpt', 'content_blocks', 'is_popular', 'is_pinned']);
        });
    }
};