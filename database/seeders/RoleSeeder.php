<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'Superadmin',
                'description' => 'Memiliki akses penuh ke seluruh sistem.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin',
                'description' => 'Mengelola data dan fitur administrasi yang diberikan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Operator',
                'description' => 'Mengelola fitur operasional yang diberikan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
