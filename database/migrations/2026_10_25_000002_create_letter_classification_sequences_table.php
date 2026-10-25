<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('letter_classification_sequences', function (Blueprint $table): void {
            $table->id('classification_sequence_id');
            $table->string('classification_index', 20);
            $table->unsignedInteger('year');
            $table->unsignedInteger('last_sequence')->default(0);
            $table->timestamp('updated_at')->nullable();
            $table->unique(['classification_index', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_classification_sequences');
    }
};
