<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_request_status_histories', function (Blueprint $table) {
            $table->id('history_id');
            $table->foreignId('letter_request_id')->constrained('letter_requests', 'letter_request_id');
            $table->string('status', 50);
            $table->text('note')->nullable();
            $table->foreignId('change_by')->constrained('users', 'user_id');
            $table->timestamp('changed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_request_status_histories');
    }
};
