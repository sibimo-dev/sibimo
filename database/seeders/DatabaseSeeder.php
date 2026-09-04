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
            'full_name' => 'Superadmin SIBIMO',
            'username' => 'superadmin',
            'email' => 'superadmin@sibimo.test',
            'password' => Hash::make('password123'),
            'role' => 'Superadmin',
            'phone_number' => '081234567899',
            'is_active' => true,
        ]);

        User::factory()->create([
            'full_name' => 'Administrator SIBIMO',
            'username' => 'admin',
            'email' => 'admin@sibimo.test',
            'password' => Hash::make('password123'),
            'role' => 'Admin',
            'phone_number' => '081234567890',
            'is_active' => true,
        ]);

        User::factory()->create([
            'full_name' => 'Operator SIBIMO',
            'username' => 'operator',
            'email' => 'operator@sibimo.test',
            'password' => Hash::make('password123'),
            'role' => 'Operator',
            'phone_number' => '081234567891',
            'is_active' => true,
        ]);

        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            UserRoleSeeder::class,
            SignerSeeder::class,
            StaffSeeder::class,
            CitizenSeeder::class,
            LetterTypeSeeder::class,
            ServiceSeeder::class,
            VillagePotentialSeeder::class,
            NewsCategorySeeder::class,
            BookCategorySeeder::class,
            BookSeeder::class,
            FeedbackSeeder::class,
            LetterTypeDocumentSeeder::class,
            LetterNumberSequenceSeeder::class,
            HistorySeeder::class,
            VisionMissionSeeder::class,
            GallerySeeder::class,
            AgendaSeeder::class,
            NewsSeeder::class,
            BookLoanSeeder::class,
            LetterRequestSeeder::class,
            ComplaintSeeder::class,
            LetterRequestAttachmentSeeder::class,
            LetterRequestStatusHistorySeeder::class,
            ComplaintAttachmentSeeder::class,
            ComplaintStatusHistorySeeder::class,
        ]);
    }
}
