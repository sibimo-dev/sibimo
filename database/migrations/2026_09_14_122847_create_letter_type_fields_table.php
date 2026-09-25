<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('letter_type_fields', function (Blueprint $table) {
            $table->id('field_id');
            $table->foreignId('letter_type_id')->constrained('letter_types', 'letter_type_id')->onDelete('cascade');
            $table->string('field_label', 150);
            $table->string('field_key', 100);
            $table->enum('field_type', ['text', 'textarea', 'number', 'date', 'select'])->default('text');
            $table->boolean('is_required')->default(true);
            $table->json('options')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamp('created_at')->nullable();

            $table->index(['letter_type_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_type_fields');
    }
};