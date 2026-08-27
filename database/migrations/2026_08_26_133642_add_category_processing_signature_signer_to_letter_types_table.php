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
        Schema::table('letter_types', function (Blueprint $table) {
            $table->enum('category', [
                'Perintah',
                'Keterangan',
                'Pengantar',
                'Permohonan',
                'Pernyataan'
            ])->after('letter_name');
    
            $table->string('processing_time', 50)
                ->nullable()
                ->after('number_prefix');
    
            $table->enum('signature_method', ['digital', 'manual'])
                ->nullable()
                ->after('processing_time');
    
            $table->unsignedBigInteger('signer_id')
                ->nullable()
                ->after('signature_method');
    
            $table->foreign('signer_id')
                ->references('signer_id')
                ->on('signers')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letter_types', function (Blueprint $table) {
            $table->dropForeign(['signer_id']);
            $table->dropColumn([
                'category',
                'processing_time',
                'signature_method',
                'signer_id'
            ]);
        });
    }   
};