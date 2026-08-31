<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('full_name', 100)->nullable();
            $table->string('username', 50)->nullable()->unique();
            $table->string('email', 100)->nullable()->unique();
            $table->string('password', 255)->nullable();
            $table->enum('role', ['Superadmin', 'Admin', 'Operator'])->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreignId('role_id')->nullable()->constrained('roles', 'role_id')->onDelete('set null');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};