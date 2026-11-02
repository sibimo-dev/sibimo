<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id('staff_id');
            $table->string('name', 100);
            $table->string('position', 100);
            $table->string('level', 150)->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('start_year')->nullable();
            $table->unsignedSmallInteger('end_year')->nullable();
            $table->string('photo', 255)->nullable();
            $table->string('signature_image', 255)->nullable();
            $table->boolean('is_signer')->default(false);
            $table->timestamps();

            $table->index(['is_signer', 'name']);
            $table->index('level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
