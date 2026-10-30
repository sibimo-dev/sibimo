<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('staff_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staffs')->restrictOnDelete();
            $table->string('position', 100);
            $table->unsignedBigInteger('padukuhan_id')->nullable();
            $table->unsignedBigInteger('rw_id')->nullable();
            $table->unsignedBigInteger('rt_id')->nullable();
            $table->foreign('padukuhan_id')->references('region_id')->on('regions')->nullOnDelete();
            $table->foreign('rw_id')->references('id')->on('rws')->nullOnDelete();
            $table->foreign('rt_id')->references('id')->on('rts')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('term_end_date')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_signer')->default(false);
            $table->string('sk_number', 100)->nullable();
            $table->date('sk_date')->nullable();
            $table->string('sk_issuer', 150)->nullable();
            $table->string('sk_document')->nullable();
            $table->string('sk_document_name')->nullable();
            $table->timestamps();
            $table->index(['position', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_assignments');
    }
};
