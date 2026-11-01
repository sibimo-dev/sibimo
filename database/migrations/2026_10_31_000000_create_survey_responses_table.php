<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id('survey_response_id');
            $table->string('service_type', 120)->default('Lainnya');
            $table->unsignedTinyInteger('age');
            $table->string('gender', 30);
            $table->string('education', 100);
            $table->string('occupation', 100);
            $table->json('answers');
            $table->decimal('average_score', 4, 2);
            $table->text('suggestion')->nullable();
            $table->timestamps();

            $table->index('service_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};
