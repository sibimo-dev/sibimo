<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('roles')
            ->pluck('role_id', 'name');

        DB::table('users')
            ->whereNotNull('role')
            ->get()
            ->each(function ($user) use ($roles) {

                if (isset($roles[$user->role])) {
                    DB::table('users')
                        ->where('user_id', $user->user_id)
                        ->update([
                            'role_id' => $roles[$user->role],
                        ]);
                }
            });
    }
}
