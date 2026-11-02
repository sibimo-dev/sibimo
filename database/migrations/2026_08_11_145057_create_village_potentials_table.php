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
            $table->string('category', 100);
            $table->string('title', 200);
            $table->string('slug', 220)->nullable()->unique();
            $table->string('short_desc', 255)->nullable();
            $table->longText('description')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('location', 255)->nullable();
            $table->string('contact', 255)->nullable();
            $table->json('extra_info')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('village_potentials');
    }
};
