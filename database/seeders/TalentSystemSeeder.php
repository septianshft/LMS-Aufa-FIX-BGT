<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TalentAdmin;
use App\Models\Talent;
use App\Models\Recruiter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class TalentSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users for each new role

        // Talent Admin
        $talentAdminUser = User::firstOrCreate([
            'email' => 'talentadmin@test.com'
        ], [
            'name' => 'Talent Admin',
            'pekerjaan' => 'Talent Administrator',
            'avatar' => 'images/default-avatar.png',
            'password' => bcrypt('password123'),
        ]);

        $talentAdminRole = Role::where('name', 'talent_admin')->first();
        $talentAdminUser->assignRole($talentAdminRole);

        TalentAdmin::firstOrCreate([
            'user_id' => $talentAdminUser->id
        ], [
            'is_active' => true
        ]);

        // Talent
        $talentUser = User::firstOrCreate([
            'email' => 'talent@test.com'
        ], [
            'name' => 'John Talent',
            'pekerjaan' => 'Software Developer',
            'avatar' => 'images/default-avatar.png',
            'password' => bcrypt('password123'),
        ]);

        $talentRole = Role::where('name', 'talent')->first();
        $talentUser->assignRole($talentRole);

        Talent::firstOrCreate([
            'user_id' => $talentUser->id
        ], [
            'is_active' => true
        ]);

        // Recruiter
        $recruiterUser = User::firstOrCreate([
            'email' => 'recruiter@test.com'
        ], [
            'name' => 'Jane Recruiter',
            'pekerjaan' => 'HR Recruiter',
            'avatar' => 'images/default-avatar.png',
            'password' => bcrypt('password123'),
        ]);

        $recruiterRole = Role::where('name', 'recruiter')->first();
        $recruiterUser->assignRole($recruiterRole);

        Recruiter::firstOrCreate([
            'user_id' => $recruiterUser->id
        ], [
            'is_active' => true
        ]);
    }
}
