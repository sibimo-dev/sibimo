<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('developments', function (Blueprint $table): void {
            $table->id('development_id');
            $table->string('name', 200);
            $table->string('slug', 230)->unique();
            $table->string('address', 255)->nullable();
            $table->string('category', 80);
            $table->string('status', 40)->default('perencanaan');
            $table->string('funding_source', 150)->nullable();
            $table->decimal('budget', 16, 2)->nullable();
            $table->decimal('volume', 14, 2)->nullable();
            $table->string('volume_unit', 40)->nullable();
            $table->string('executor', 150)->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->date('start_date')->nullable();
            $table->date('target_date')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('end_latitude', 10, 7)->nullable();
            $table->decimal('end_longitude', 10, 7)->nullable();
            $table->string('cover_image')->nullable();
            $table->timestamps();

            $table->index(['category', 'status']);
            $table->index('year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('developments');
    }
};
