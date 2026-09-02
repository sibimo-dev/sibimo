<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VisionMissionSeeder extends Seeder
{
    public function run(): void
    {
        $publisherId = DB::table('users')->value('user_id');

        DB::table('vision_missions')->insert([
            'title' => 'Visi & Misi Kalurahan Bimomartani',
            'vision' => 'Mewujudkan Kalurahan Bimomartani yang mandiri, sejahtera, dan berbudaya.',
            'missions' => json_encode([
                'Meningkatkan kualitas pelayanan publik berbasis teknologi informasi.',
                'Mengembangkan potensi ekonomi lokal melalui pemberdayaan masyarakat.',
                'Melestarikan nilai budaya dan kearifan lokal masyarakat.',
                'Membangun infrastruktur desa yang memadai dan berwawasan lingkungan.',
            ]),
            'published_by' => $publisherId,
            'status' => 'Published',
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
