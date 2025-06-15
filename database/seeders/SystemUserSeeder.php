<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TalentAdmin;
use App\Models\Talent;
use App\Models\Recruiter;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SystemUserSeeder extends Seeder
{
    /**
     * Seed minimal core system users for trainee-to-talent conversion testing.
     * Only essential system accounts - no sample talents.
     */
    public function run(): void
    {
        $this->command->info('👥 Creating essential system users...');

        // ===============================================
        // ESSENTIAL SYSTEM ACCOUNTS ONLY
        // ===============================================

        // LMS Admin - Full LMS access only
        $adminUser = User::firstOrCreate([
            'email' => 'admin@lms.test'
        ], [
            'name' => 'LMS Administrator',
            'pekerjaan' => 'System Administrator',
            'avatar' => null,
            'password' => bcrypt('password123'),
        ]);
        $adminUser->assignRole('admin');
        $this->command->info('   ✓ LMS Admin created: admin@lms.test');

        // Talent Admin - Talent system management only
        $talentAdminUser = User::firstOrCreate([
            'email' => 'talent.admin@scout.test'
        ], [
            'name' => 'Emma Talent Admin',
            'pekerjaan' => 'Talent System Administrator',
            'avatar' => null,
            'password' => bcrypt('password123'),
        ]);
        $talentAdminUser->assignRole('talent_admin');

        // Create talent admin profile
        TalentAdmin::firstOrCreate([
            'user_id' => $talentAdminUser->id
        ], [
            'is_active' => true
        ]);
        $this->command->info('   ✓ Talent Admin created: talent.admin@scout.test');

        // Recruiter - Talent discovery only
        $recruiterUser = User::firstOrCreate([
            'email' => 'recruiter@scout.test'
        ], [
            'name' => 'Michael Recruiter',
            'pekerjaan' => 'Senior Talent Recruiter',
            'avatar' => null,
            'password' => bcrypt('password123'),
        ]);
        $recruiterUser->assignRole('recruiter');

        // Create recruiter profile
        Recruiter::firstOrCreate([
            'user_id' => $recruiterUser->id
        ], [
            'is_active' => true
        ]);
        $this->command->info('   ✓ Recruiter created: recruiter@scout.test');

        $this->command->info('ℹ️  Note: No sample talents created - use TraineeSeeder for conversion testing');
        $this->command->info('✅ Essential system users created successfully!');
    }
}
