<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('letter_number_sequences', function (Blueprint $table) {
            $table->id('sequence_id');
            $table->foreignId('letter_type_id')->constrained('letter_types', 'letter_type_id');
            $table->integer('year');
            $table->unsignedTinyInteger('month')->default(1);
            $table->integer('last_sequence')->default(0);
            $table->timestamp('updated_at')->nullable();

            $table->unique(['letter_type_id', 'year', 'month']);
            $table->index('letter_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_number_sequences');
    }
};
