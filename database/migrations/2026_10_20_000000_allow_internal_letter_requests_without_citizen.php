<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('letter_requests', function (Blueprint $table) {
            $table->string('applicant_name', 100)->nullable()->change();
            $table->string('applicant_nik', 16)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('letter_requests', function (Blueprint $table) {
            $table->string('applicant_name', 100)->nullable(false)->change();
            $table->string('applicant_nik', 16)->nullable(false)->change();
        });
    }
};
