<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id('complaint_id');
            $table->string('reporter_name', 100)->nullable();
            $table->string('reporter_phone', 20)->nullable();
            $table->enum('category', ['Infrastructure', 'Public Service', 'Environment', 'Security', 'Other']);
            $table->string('title', 200);
            $table->longText('description');
            $table->string('location', 255)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('status', ['Submitted', 'In Progress', 'Resolved', 'Rejected'])->default('Submitted');
            $table->text('resolution_description')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->index('status');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
