<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('letter_request_attachments', function (Blueprint $table) {
            $table->id('attachment_id');
            $table->foreignId('letter_request_id')->constrained('letter_requests', 'letter_request_id');
            $table->foreignId('letter_type_document_id')->constrained('letter_type_documents', 'letter_type_document_id');
            $table->string('file_name', 225);
            $table->string('file_path', 225);
            $table->timestamp('uploaded_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_request_attachments');
    }
};