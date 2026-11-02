<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('regions', 'region_id')->cascadeOnDelete();
            $table->string('number', 10);
            $table->string('head_name', 100)->nullable();
            $table->string('head_phone', 20)->nullable();
            $table->timestamps();

            $table->unique(['region_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rws');
    }
};
