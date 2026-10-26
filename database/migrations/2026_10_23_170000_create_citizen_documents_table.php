<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('citizen_documents')) {
            Schema::create('citizen_documents', function (Blueprint $table) {
            $table->id('citizen_document_id');
            $table->foreignId('citizen_id')->constrained('citizens', 'citizen_id')->cascadeOnDelete();
            $table->string('document_type', 80);
            $table->string('file_name', 225);
            $table->string('storage_path', 255);
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('file_hash', 64);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index(['citizen_id', 'document_type', 'is_active']);
            $table->unique(['citizen_id', 'document_type', 'file_hash']);
            });
        }

        if (! Schema::hasColumn('letter_request_attachments', 'citizen_document_id')) {
            Schema::table('letter_request_attachments', function (Blueprint $table) {
                $table->foreignId('citizen_document_id')
                    ->nullable()
                    ->after('letter_type_document_id')
                    ->constrained('citizen_documents', 'citizen_document_id')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('letter_request_attachments', 'document_type')) {
            Schema::table('letter_request_attachments', function (Blueprint $table) {
                $table->string('document_type', 80)->nullable()->after('citizen_document_id');
                $table->index(
                    ['citizen_document_id', 'document_type'],
                    'lra_citizen_doc_type_idx'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::table('letter_request_attachments', function (Blueprint $table) {
            $table->dropForeign(['citizen_document_id']);
            $table->dropIndex('lra_citizen_doc_type_idx');
            $table->dropColumn(['citizen_document_id', 'document_type']);
        });

        Schema::dropIfExists('citizen_documents');
    }
};
