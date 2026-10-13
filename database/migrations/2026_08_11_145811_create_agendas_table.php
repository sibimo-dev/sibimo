<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agendas', function (Blueprint $table) {
            $table->id('agenda_id');
            $table->string('title', 200);
            $table->longText('description')->nullable();
            $table->date('event_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location', 255)->nullable();
            $table->foreignId('created_by')->constrained('users', 'user_id');
            $table->timestamp('created_at')->nullable();

            $table->index('event_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};