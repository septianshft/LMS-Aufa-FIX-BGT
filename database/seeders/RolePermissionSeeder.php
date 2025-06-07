<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $adminRole = Role::firstOrCreate([
            'name' => 'admin'
        ]);

        $traineeRole = Role::firstOrCreate([
            'name' => 'trainee'
        ]);

        $trainerRole = Role::firstOrCreate([
            'name' => 'trainer'
        ]);

        // New talent scouting roles
        $talentAdminRole = Role::firstOrCreate([
            'name' => 'talent_admin'
        ]);

        $talentRole = Role::firstOrCreate([
            'name' => 'talent'
        ]);

        $recruiterRole = Role::firstOrCreate([
            'name' => 'recruiter'
        ]);

        //akun admin

        $userAdmin = User::firstOrCreate([
            'email' => 'admin@admin.com'
        ], [
            'name' => 'admin',
            'pekerjaan' => 'admin',
            'avatar' => 'images/default-avatar.png',
            'password' => bcrypt('123123123'),
        ]);

        $userAdmin->assignRole($adminRole);
    }
}
