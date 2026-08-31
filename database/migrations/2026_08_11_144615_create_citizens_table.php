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
            $table->string('family_card_number', 16)->nullable();
            $table->string('full_name', 100);
            $table->string('birth_place', 50)->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['Laki-laki', 'Perempuan'])->nullable();
            $table->text('address')->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('occupation', 100)->nullable();
            $table->string('education', 50)->nullable();
            $table->string('marital_status', 30)->nullable();
            $table->enum('status', ['Active', 'Pindah'])->default('Active');
            $table->timestamps();

            $table->index('full_name');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citizens');
    }
};