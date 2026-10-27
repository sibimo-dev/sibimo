<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('village_potential_categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('name', 100)->unique();
            $table->string('slug', 120)->unique();
        });

        DB::table('village_potential_categories')->insert([
            ['name' => 'UMKM',       'slug' => 'umkm'],
            ['name' => 'Pertanian',  'slug' => 'pertanian'],
            ['name' => 'Pariwisata', 'slug' => 'pariwisata'],
            ['name' => 'BUMDes',     'slug' => 'bumdes'],
        ]);

        // Kolom category bisa saja masih enum, jadi ubah ke string dulu
        Schema::table('village_potentials', function (Blueprint $table) {
            $table->string('category', 120)->change();
        });

        // Konversi data lama ke slug
        foreach (['Agriculture' => 'pertanian', 'BUMDes' => 'bumdes', 'Tourism' => 'pariwisata', 'UMKM' => 'umkm'] as $old => $slug) {
            DB::table('village_potentials')->where('category', $old)->update(['category' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('village_potential_categories');
    }
};