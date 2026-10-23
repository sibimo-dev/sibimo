<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('book_loans', function (Blueprint $table) {
            $table->unsignedBigInteger('citizen_id')->nullable()->change();
            $table->string('borrower_name', 150)->nullable()->after('citizen_id');
        });
    }

    public function down(): void
    {
        Schema::table('book_loans', function (Blueprint $table) {
            $table->dropColumn('borrower_name');
            $table->unsignedBigInteger('citizen_id')->nullable(false)->change();
        });
    }
};