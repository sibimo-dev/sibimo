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
        Schema::table('letter_requests', function (Blueprint $table) {
            $table->string('request_code', 30)->unique()->nullable()->after('letter_request_id');

            $table->string('applicant_name', 100)->after('citizen_id');
            $table->string('applicant_nik', 16)->after('applicant_name');
            $table->string('applicant_phone', 15)->nullable()->after('applicant_nik');
            $table->text('applicant_address')->nullable()->after('applicant_phone');

            $table->foreignId('authorized_by_signer_id')
                ->nullable()
                ->after('verified_by')
                ->constrained('signers', 'signer_id');

            $table->enum('source', ['Online', 'Manual (Kelurahan)'])
                ->default('Manual (Kelurahan)')
                ->after('authorized_by_signer_id');

            $table->text('notes')->nullable()->after('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letter_requests', function (Blueprint $table) {
            $table->dropForeign(['authorized_by_signer_id']);

            $table->dropColumn([
                'request_code',
                'applicant_name',
                'applicant_nik',
                'applicant_phone',
                'applicant_address',
                'authorized_by_signer_id',
                'source',
                'notes',
            ]);
        });
    }
};
