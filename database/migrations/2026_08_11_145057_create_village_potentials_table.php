<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('village_potentials', function (Blueprint $table) {
            $table->id('potential_id');
            $table->enum('category', ['UMKM', 'Agriculture', 'Tourism', 'BUMDes']);
            $table->string('title', 200);
            $table->longText('description')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('location', 255)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('village_potentials');
    }
};
