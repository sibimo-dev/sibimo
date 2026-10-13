<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_products', function (Blueprint $table) {
            $table->id('legal_product_id');
            $table->string('title');
            $table->string('category', 50);
            $table->string('status', 30)->default('berlaku');
            $table->string('number', 100)->nullable();
            $table->unsignedSmallInteger('year');
            $table->text('description')->nullable();
            $table->string('document')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['year', 'category', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_products');
    }
};