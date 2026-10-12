<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer_settings', function (Blueprint $table): void {
            $table->id('footer_setting_id');
            $table->string('village_name', 150);
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->json('social_media')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('stamp')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_settings');
    }
};
