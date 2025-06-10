<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for manual talent scouting flow testing.
     * Minimal clean data: only essential accounts + some talents with course-based skills.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting minimal seeding for manual flow testing...');

        // Core system setup
        $this->command->info('📋 Creating roles and permissions...');
        $this->call(RolePermissionSeeder::class);

        // LMS system infrastructure (needed for course completion tracking)
        $this->command->info('📚 Setting up LMS infrastructure...');
        $this->call(CourseLevelSeeder::class);
        $this->call(CourseModeSeeder::class);

        // Core system users only - no bulk data
        $this->command->info('👥 Creating essential system users...');
        $this->call(SystemUserSeeder::class);

        $this->command->info('✅ Minimal seeding completed successfully!');
        $this->displaySystemSummary();
    }

    private function displaySystemSummary()
    {
        $this->command->info('');
        $this->command->info('📊 MINIMAL SYSTEM SETUP FOR FLOW TESTING:');
        $this->command->info('═══════════════════════════════════════════════');

        // User statistics
        $userCount = User::count();
        $talentAdminCount = User::whereHas('roles', function($query) {
            $query->where('name', 'talent_admin');
        })->count();
        $talentCount = User::whereHas('roles', function($query) {
            $query->where('name', 'talent');
        })->count();
        $recruiterCount = User::whereHas('roles', function($query) {
            $query->where('name', 'recruiter');
        })->count();

        $this->command->info("👥 TOTAL USERS: {$userCount}");
        $this->command->info("�️ Talent Admins: {$talentAdminCount}");
        $this->command->info("⭐ Talents: {$talentCount}");
        $this->command->info("� Recruiters: {$recruiterCount}");

        $this->command->info('');
        $this->command->info('🔑 TEST CREDENTIALS:');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('🎛️ Talent Admin: talent.admin@scout.test / password123');
        $this->command->info('👔 Recruiter: recruiter@scout.test / password123');
        $this->command->info('⭐ Frontend Talent: sarah.frontend@test.com / password123');
        $this->command->info('⭐ Backend Talent: john.backend@test.com / password123');
        $this->command->info('⭐ Fullstack Talent: alex.fullstack@test.com / password123');

        $this->command->info('');
        $this->command->info('🌐 SYSTEM ACCESS URLS:');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('🔐 Login: http://127.0.0.1:8000/login');
        $this->command->info('�️ Talent Admin: http://127.0.0.1:8000/talent-admin/dashboard');
        $this->command->info('👔 Recruiter: http://127.0.0.1:8000/recruiter/dashboard');
        $this->command->info('⭐ Talent: http://127.0.0.1:8000/talent/dashboard');

        $this->command->info('');
        $this->command->info('🔄 MANUAL FLOW TESTING PATH:');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('1. Login as Recruiter → Find talents → Send talent request');
        $this->command->info('2. Login as Talent → Review request → Accept/Reject');
        $this->command->info('3. Login as Talent Admin → Review request → Approve/Reject');
        $this->command->info('4. Test meeting arrangement → agreement → onboarding flow');

        $this->command->info('');
        $this->command->info('✅ READY FOR MANUAL FLOW TESTING!');
        $this->command->info('');
    }
}
