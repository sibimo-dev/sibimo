<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = ['Sejarah Desa', 'Visi & Misi', 'Struktur Organisasi', 'Peta Wilayah', 'Data Demografi'];
        foreach ($sections as $i => $name) {
            DB::table('profile_sections')->insert([
                'section_name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
                'sort_order' => $i,
                'is_active' => 1,
                'created_at' => now(),
            ]);
        }
    }
}