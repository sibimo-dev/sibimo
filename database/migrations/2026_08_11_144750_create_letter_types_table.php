<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('letter_types', function (Blueprint $table) {
            $table->id('letter_type_id');
            $table->string('code', 20)->unique();
            $table->string('letter_name', 100);
            $table->enum('category', ['Perintah', 'Keterangan', 'Pengantar', 'Permohonan', 'Pernyataan']);
            $table->text('description')->nullable();
            $table->string('blade_view', 225)->nullable();
            $table->string('number_prefix', 50)->nullable();
            $table->string('processing_time', 50)->nullable();
            $table->enum('signature_method', ['digital', 'manual'])->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreignId('signer_id')->nullable()->constrained('staff', 'staff_id')->onDelete('set null');

            $table->index(['category', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_types');
    }
};
