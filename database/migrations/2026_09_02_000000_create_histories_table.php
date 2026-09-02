<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id('history_id');
            $table->string('title', 200);
            $table->unsignedSmallInteger('year_founded');
            $table->json('points');
            $table->json('photos')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users', 'user_id');
            $table->enum('status', ['Draft', 'Published'])->default('Draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
