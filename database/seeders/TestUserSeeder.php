<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test admin user for LMS testing
        $adminUser = User::firstOrCreate([
            'email' => 'admin@test.com'
        ], [
            'name' => 'Test Admin',
            'pekerjaan' => 'Administrator',
            'avatar' => 'images/default-avatar.png',
            'password' => bcrypt('password123'),
        ]);

        $adminRole = Role::where('name', 'admin')->first();
        $adminUser->assignRole($adminRole);

        // Create test trainee user for LMS testing
        $traineeUser = User::firstOrCreate([
            'email' => 'trainee@test.com'
        ], [
            'name' => 'Test Trainee',
            'pekerjaan' => 'Student',
            'avatar' => 'images/default-avatar.png',
            'password' => bcrypt('password123'),
        ]);

        $traineeRole = Role::where('name', 'trainee')->first();
        $traineeUser->assignRole($traineeRole);

        // Create test trainer user for LMS testing
        $trainerUser = User::firstOrCreate([
            'email' => 'trainer@test.com'
        ], [
            'name' => 'Test Trainer',
            'pekerjaan' => 'Instructor',
            'avatar' => 'images/default-avatar.png',
            'password' => bcrypt('password123'),
        ]);

        $trainerRole = Role::where('name', 'trainer')->first();
        $trainerUser->assignRole($trainerRole);
    }
}
