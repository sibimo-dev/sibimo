<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_type_documents', function (Blueprint $table) {
            $table->id('letter_type_document_id');
            $table->foreignId('letter_type_id')->constrained('letter_types', 'letter_type_id');
            $table->string('document_name', 100)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(false);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_type_documents');
    }
};
