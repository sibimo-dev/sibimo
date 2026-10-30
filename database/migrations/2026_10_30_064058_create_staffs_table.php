<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('staffs', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20)->nullable()->unique();
            $table->string('name', 100);
            $table->string('phone', 20)->nullable();
            $table->string('photo')->nullable();
            $table->string('signature_image')->nullable();
            $table->string('gender', 20)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('education', 50)->nullable();
            $table->string('major', 100)->nullable();
            $table->string('school_name', 150)->nullable();
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staffs');
    }
};
