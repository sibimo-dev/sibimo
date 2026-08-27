<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_requests', function (Blueprint $table) {
            $table->id('letter_request_id');
            $table->string('request_code', 30)->unique()->nullable();

            // Nullable: pemohon boleh warga terdaftar (linked) ATAU walk-in (isi manual)
            $table->foreignId('citizen_id')->nullable()->constrained('citizens', 'citizen_id');
            $table->string('applicant_name', 100);
            $table->string('applicant_nik', 16);
            $table->string('applicant_phone', 15)->nullable();
            $table->text('applicant_address')->nullable();

            $table->foreignId('letter_type_id')->constrained('letter_types', 'letter_type_id');

            $table->enum('status', ['Pending', 'Diverifikasi', 'Disetujui', 'Ditolak'])->default('Pending');
            $table->enum('signature_type', ['Digital', 'Manual'])->nullable();
            $table->string('letter_number', 100)->nullable();

            $table->foreignId('verified_by')->nullable()->constrained('users', 'user_id');
            $table->foreignId('authorized_by_signer_id')->nullable()->constrained('signers', 'signer_id');

            $table->enum('source', ['Online', 'Manual (Kelurahan)'])->default('Manual (Kelurahan)');
            $table->text('notes')->nullable();
            $table->json('form_data')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('authorized_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_requests');
    }
};
