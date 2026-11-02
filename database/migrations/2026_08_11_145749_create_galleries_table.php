<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id('gallery_id');
            $table->string('title', 200);
            $table->string('category', 120)->default('kegiatan-sosial');
            $table->string('location', 255)->nullable();
            $table->date('event_date')->nullable();
            $table->text('description')->nullable();
            $table->string('image', 255);
            $table->foreignId('uploaded_by')->constrained('users', 'user_id');
            $table->timestamp('uploaded_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
