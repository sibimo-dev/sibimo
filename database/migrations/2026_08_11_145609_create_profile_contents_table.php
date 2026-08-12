<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_contents', function (Blueprint $table) {
            $table->id('profile_content_id');
            $table->foreignId('section_id')->constrained('profile_sections', 'section_id');
            $table->string('title', 200);
            $table->longText('content')->nullable();
            $table->string('thumbnail', 255)->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users', 'user_id');
            $table->enum('status', ['Draft', 'Published'])->default('Draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_contents');
    }
};
