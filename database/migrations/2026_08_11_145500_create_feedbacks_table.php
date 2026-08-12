<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id('feedback_id');
            $table->string('full_name', 100);
            $table->string('email', 100);
            $table->text('message');
            $table->enum('status', ['Unread', 'Read', 'Replied'])->default('Unread');
            $table->timestamp('submitted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
