<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('developments', function (Blueprint $table) {
            $table->id('development_id');
            $table->string('name', 255);
            $table->string('slug', 280)->unique();
            $table->string('category', 50);
            $table->enum('status', ['perencanaan', 'sedang_berjalan', 'selesai'])->default('perencanaan');
            $table->string('address', 255)->nullable();
            $table->text('description')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->decimal('volume', 12, 2)->nullable();
            $table->string('volume_unit', 20)->default('meter');
            $table->string('funding_source', 100)->nullable();
            $table->string('executor', 100)->nullable();
            $table->unsignedSmallInteger('year');
            $table->date('start_date');
            $table->date('target_date')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('end_latitude', 10, 7)->nullable();
            $table->decimal('end_longitude', 10, 7)->nullable();
            $table->string('cover_image', 255)->nullable();
            $table->string('photo_0', 255)->nullable();
            $table->string('photo_50', 255)->nullable();
            $table->string('photo_100', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->timestamps();
        
            $table->index('status');
            $table->index('category');
            $table->index('year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('developments');
    }
};