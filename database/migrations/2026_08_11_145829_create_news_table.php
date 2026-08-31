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
            $table->string('slug', 200)->unique();
            $table->longText('content');
            $table->string('thumbnail', 225)->nullable();
            $table->enum('status', ['Draft', 'Published', 'Archived'])->default('Draft');
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