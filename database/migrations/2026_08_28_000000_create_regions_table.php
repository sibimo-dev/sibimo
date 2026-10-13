<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id('region_id');
            $table->string('name');
            $table->string('head_name')->nullable();
            $table->unsignedInteger('rw_count')->default(0);
            $table->unsignedInteger('rt_count')->default(0);
            $table->unsignedInteger('kk_count')->default(0);
            $table->unsignedInteger('population')->default(0);
            $table->unsignedInteger('male_count')->default(0);
            $table->unsignedInteger('female_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
