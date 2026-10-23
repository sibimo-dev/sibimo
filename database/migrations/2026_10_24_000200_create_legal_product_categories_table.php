<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('legal_product_categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('category_name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        $now = now();
        DB::table('legal_product_categories')->insert([
            ['category_name' => 'Peraturan Kalurahan', 'slug' => 'perkal',   'created_at' => $now, 'updated_at' => $now],
            ['category_name' => 'SK Lurah',            'slug' => 'sk-lurah', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_product_categories');
    }
};

