<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('legal_products')) return;

        Schema::create('legal_products', function (Blueprint $table): void {
            $table->id('legal_product_id');
            $table->string('title');
            $table->string('category', 50);
            $table->string('status', 30)->default('berlaku');
            $table->string('number', 100)->nullable();
            $table->unsignedSmallInteger('year');
            $table->text('description')->nullable();
            $table->string('document')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->timestamps();
            $table->index(['year', 'category', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_products');
    }
};
