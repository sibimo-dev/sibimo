<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gallery_categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('name', 100)->unique();
            $table->string('slug', 120)->unique();
        });

        DB::table('gallery_categories')->insert([
            ['name' => 'Musyawarah',      'slug' => 'musyawarah'],
            ['name' => 'Kegiatan Sosial', 'slug' => 'kegiatan-sosial'],
            ['name' => 'Pembangunan',     'slug' => 'pembangunan'],
            ['name' => 'Budaya',          'slug' => 'budaya'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_categories');
    }
};