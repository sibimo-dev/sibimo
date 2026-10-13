<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('complaint_status_histories', function (Blueprint $table) {
            $table->id('history_id');
            $table->foreignId('complaint_id')->constrained('complaints', 'complaint_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->string('status', 50);
            $table->text('note')->nullable();
            $table->timestamp('changed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_status_histories');
    }
};