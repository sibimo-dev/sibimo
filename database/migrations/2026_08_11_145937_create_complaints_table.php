<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id('complaint_id');
            $table->foreignId('citizen_id')->constrained('citizens', 'citizen_id');
            $table->enum('category', ['Infrastructure', 'Public Service', 'Environment', 'Security', 'Other']);
            $table->string('title', 200);
            $table->longText('description');
            $table->enum('status', ['Submitted', 'In Progress', 'Resolved', 'Rejected'])->default('Submitted');
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