<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('full_name', 100)->nullable();
            $table->string('username', 50)->nullable()->unique();
            $table->string('email', 100)->nullable()->unique();
            $table->string('password', 255)->nullable();
            $table->enum('role', ['Admin', 'Operator'])->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

