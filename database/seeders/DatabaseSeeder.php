<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'full_name' => 'Administrator SIBIMO',
            'username' => 'admin',
            'email' => 'admin@sibimo.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone_number' => '081234567890',
            'is_active' => true,
        ]);

        User::factory()->create([
            'full_name' => 'Operator SIBIMO',
            'username' => 'operator',
            'email' => 'operator@sibimo.test',
            'password' => Hash::make('password123'),
            'role' => 'operator',
            'phone_number' => '081234567891',
            'is_active' => true,
        ]);

        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,        ]);
    }
}