<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->text('resolution_description')->nullable()->after('status');
        });

        // 'report' = lampiran pelapor, 'resolution' = foto bukti dari admin
        Schema::table('complaint_attachments', function (Blueprint $table) {
            $table->string('type', 20)->default('report')->after('file_path');
        });
    }

    public function down(): void
    {
        Schema::table('complaint_attachments', fn (Blueprint $t) => $t->dropColumn('type'));
        Schema::table('complaints', fn (Blueprint $t) => $t->dropColumn('resolution_description'));
    }
};