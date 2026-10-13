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
            $table->string('record_type', 100)->nullable();
            $table->string('record_event', 100)->nullable();
            $table->char('national_id', 16)->unique();
            $table->string('family_card_number', 16)->nullable();
            $table->string('dusun', 100)->nullable();
            $table->string('full_name', 100);
            $table->string('birth_place', 50)->nullable();
            $table->date('birth_date')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->enum('gender', ['Laki-laki', 'Perempuan'])->nullable();
            $table->text('address')->nullable();
            $table->string('rt', 20)->nullable();
            $table->string('rw', 20)->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('birth_certificate_status', 50)->nullable();
            $table->string('birth_certificate_number', 50)->nullable();
            $table->string('blood_type', 10)->nullable();
            $table->string('occupation', 100)->nullable();
            $table->string('education', 50)->nullable();
            $table->string('marital_status', 30)->nullable();
            $table->string('marriage_certificate_status', 50)->nullable();
            $table->string('marriage_certificate_number', 50)->nullable();
            $table->date('marriage_date')->nullable();
            $table->string('divorce_certificate_status', 50)->nullable();
            $table->string('divorce_certificate_number', 50)->nullable();
            $table->date('divorce_date')->nullable();
            $table->string('family_relationship', 100)->nullable();
            $table->string('physical_disability', 100)->nullable();
            $table->string('disability_status', 100)->nullable();
            $table->string('religion', 30)->nullable();
            $table->string('mother_national_id', 16)->nullable();
            $table->string('mother_name', 100)->nullable();
            $table->string('father_national_id', 16)->nullable();
            $table->string('father_name', 100)->nullable();
            $table->string('nationality', 50)->nullable();
            $table->text('ktp_address')->nullable();
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
