<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citizens', function (Blueprint $table) {
            $table->id('citizen_id');
            $table->char('national_id', 16)->unique();
            $table->string('full_name', 100);
            $table->string('birth_place', 50)->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['Male', 'Female'])->nullable();
            $table->text('address')->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('password', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citizens');
    }
};
