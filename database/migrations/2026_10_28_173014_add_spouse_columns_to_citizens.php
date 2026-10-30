<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citizens', function (Blueprint $table) {
            $columns = [
                'spouse_national_id' => fn () => $table->string('spouse_national_id', 16)->nullable(),
                'spouse_name'        => fn () => $table->string('spouse_name', 100)->nullable(),
                'spouse_birth_place' => fn () => $table->string('spouse_birth_place', 50)->nullable(),
                'spouse_birth_date'  => fn () => $table->date('spouse_birth_date')->nullable(),
                'spouse_occupation'  => fn () => $table->string('spouse_occupation', 100)->nullable(),
                'spouse_religion'    => fn () => $table->string('spouse_religion', 100)->nullable(),
                'spouse_nationality' => fn () => $table->string('spouse_nationality', 50)->nullable(),
                'spouse_address'     => fn () => $table->text('spouse_address')->nullable(),
            ];

            foreach ($columns as $name => $add) {
                if (! Schema::hasColumn('citizens', $name)) {
                    $add();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('citizens', function (Blueprint $table) {
            $table->dropColumn([
                'spouse_national_id', 'spouse_name', 'spouse_birth_place', 'spouse_birth_date',
                'spouse_occupation', 'spouse_religion', 'spouse_nationality', 'spouse_address',
            ]);
        });
    }
};