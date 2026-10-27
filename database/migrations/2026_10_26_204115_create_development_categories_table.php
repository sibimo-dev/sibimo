<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('development_categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('category_name')->unique();
            $table->string('slug', 50)->unique();
            $table->timestamps();
        });

        $now = now();
        DB::table('development_categories')->insert(array_map(
            fn ($c) => [...$c, 'created_at' => $now, 'updated_at' => $now],
            [
                ['category_name' => 'Infrastruktur', 'slug' => 'infrastruktur'],
                ['category_name' => 'Kesehatan',     'slug' => 'kesehatan'],
                ['category_name' => 'Pendidikan',    'slug' => 'pendidikan'],
                ['category_name' => 'Lingkungan',    'slug' => 'lingkungan'],
                ['category_name' => 'Sosial',        'slug' => 'sosial'],
            ]
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('development_categories');
    }
};

