<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id('news_id');
            $table->foreignId('category_id')->constrained('news_categories', 'category_id');
            $table->foreignId('author_id')->constrained('users', 'user_id');
            $table->string('title', 200);
            $table->string('excerpt', 255)->nullable();
            $table->string('slug', 200)->unique();
            $table->longText('content');
            $table->json('content_blocks')->nullable();
            $table->string('thumbnail', 225)->nullable();
            $table->enum('status', ['Draft', 'Published', 'Archived'])->default('Draft');
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};