<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_products', function (Blueprint $table): void {
            $table->id('legal_product_id');
            $table->string('title', 255);
            $table->string('category', 40);
            $table->string('status', 40)->default('berlaku');
            $table->string('number', 150)->nullable();
            $table->unsignedSmallInteger('year');
            $table->text('description')->nullable();
            $table->string('document')->nullable();
            $table->timestamps();

            $table->index(['category', 'status']);
            $table->index('year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_products');
    }
};
