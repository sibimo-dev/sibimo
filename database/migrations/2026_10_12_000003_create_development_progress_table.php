<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('development_progress', function (Blueprint $table): void {
            $table->id('progress_id');
            $table->foreignId('development_id')
                ->constrained('developments', 'development_id')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('percentage');
            $table->string('image')->nullable();
            $table->timestamps();

            $table->unique(['development_id', 'percentage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('development_progress');
    }
};
