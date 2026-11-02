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
            $table->char('national_id', 16)->unique();
            $table->string('family_card_number', 16)->nullable();
            $table->string('full_name', 100);
            $table->string('birth_place', 50)->nullable();
            $table->date('birth_date')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->enum('gender', ['Laki-laki', 'Perempuan'])->nullable();
            $table->text('address')->nullable();

            // Wilayah — rt_id adalah FK utama ke tabel rts (lihat relasi Rt→Rw→Region).
            // Dari rt_id bisa di-derive: RW dan Padukuhan (tidak perlu kolom rw_id/padukuhan_id).
            // FK constraint ditambahkan via Schema::table setelah rts selesai dibuat.
            // Kolom rt (string nomor) dipertahankan untuk backward-compat data lama.
            $table->unsignedBigInteger('rt_id')->nullable();
            $table->string('rt', 20)->nullable();

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
            $table->string('disability_type', 100)->nullable();
            $table->string('disability_status', 100)->nullable();
            $table->string('religion', 100)->nullable();
            $table->string('belief_organization', 150)->nullable();
            $table->string('nationality', 50)->nullable();

            // Relasi NIK dan nama orang tua & pasangan.
            $table->string('mother_national_id', 16)->nullable();
            $table->string('mother_name', 150)->nullable();
            $table->string('father_national_id', 16)->nullable();
            $table->string('father_name', 150)->nullable();
            $table->string('spouse_national_id', 16)->nullable();

            $table->text('ktp_address')->nullable();
            $table->text('previous_address')->nullable();
            $table->string('passport_number', 50)->nullable();
            $table->date('passport_expiry_date')->nullable();
            $table->string('status', 20)->default('Active');
            $table->timestamps();

            $table->index('full_name');
            $table->index('status');
            $table->index('rt_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citizens');
    }
};
