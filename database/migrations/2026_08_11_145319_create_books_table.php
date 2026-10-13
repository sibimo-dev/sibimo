<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id('book_id');
            $table->string('title', 200);
            $table->string('author', 150)->nullable();
            $table->string('isbn', 20)->nullable();
            $table->integer('stock')->default(0);
            $table->timestamps();
            $table->foreignId('category_id')->constrained('book_categories', 'category_id');

            $table->index('title');
            $table->index('isbn');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};