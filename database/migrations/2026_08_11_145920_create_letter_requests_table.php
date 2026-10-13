<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('letter_requests', function (Blueprint $table) {
            $table->id('letter_request_id');
            $table->string('request_code', 30)->nullable()->unique();
            $table->foreignId('citizen_id')->nullable()->constrained('citizens', 'citizen_id');
            $table->string('applicant_name', 100);
            $table->string('applicant_nik', 16);
            $table->string('applicant_phone', 15)->nullable();
            $table->text('applicant_address')->nullable();
            $table->foreignId('letter_type_id')->constrained('letter_types', 'letter_type_id');
            $table->enum('status', ['submitted', 'verified', 'authorized', 'completed', 'rejected'])->default('submitted');
            $table->json('form_data')->nullable();
            $table->string('letter_number', 100)->nullable();
            $table->enum('signature_type', ['manual', 'digital'])->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users', 'user_id');
            $table->foreignId('authorized_by_signer_id')->nullable()->constrained('staff', 'staff_id');
            $table->enum('source', ['Online', 'Manual (Kelurahan)'])->default('Manual (Kelurahan)');
            $table->text('notes')->nullable();
            $table->foreignId('authorized_by')->nullable()->constrained('users', 'user_id');            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('result_file_path', 100)->nullable();
            $table->text('remarks')->nullable();

            $table->index('status');
            $table->index('submitted_at');
            $table->index('applicant_nik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_requests');
    }
};
