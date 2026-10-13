<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('book_loans', function (Blueprint $table) {
            $table->id('loan_id');
            $table->foreignId('book_id')->constrained('books', 'book_id');
            $table->foreignId('citizen_id')->constrained('citizens', 'citizen_id');
            $table->date('borrowed_at');
            $table->date('due_date');
            $table->date('returned_at')->nullable();
            $table->enum('status', ['Borrowed', 'Returned', 'Late'])->default('Borrowed');
            $table->decimal('fine_amount', 10, 2)->default(0.00);
            $table->timestamps();

            $table->index('status');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_loans');
    }
};