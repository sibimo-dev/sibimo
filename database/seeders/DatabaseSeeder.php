<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'full_name' => 'Superadmin SIBIMO',
                'username' => 'superadmin',
                'email' => 'superadmin@sibimo.test',
                'role' => 'Superadmin',
                'phone_number' => '081234567899',
            ],
            [
                'full_name' => 'Administrator SIBIMO',
                'username' => 'admin',
                'email' => 'admin@sibimo.test',
                'role' => 'Admin',
                'phone_number' => '081234567890',
            ],
            [
                'full_name' => 'Operator SIBIMO',
                'username' => 'operator',
                'email' => 'operator@sibimo.test',
                'role' => 'Operator',
                'phone_number' => '081234567891',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['username' => $user['username']],
                [
                    ...$user,
                    'password' => Hash::make('password123'),
                    'is_active' => true,
                ],
            );
        }

        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            UserRoleSeeder::class,
            SignerSeeder::class,
            StaffSeeder::class,
            CitizenSeeder::class,
            RegionSeeder::class,
            LetterTypeSeeder::class,
            ServiceSeeder::class,
            VillagePotentialSeeder::class,
            NewsCategorySeeder::class,
            BookCategorySeeder::class,
            BookSeeder::class,
            FeedbackSeeder::class,
            LetterTypeFieldSeeder::class,
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
