<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for trainee-to-talent conversion testing.
     * Clean environment: only essential accounts + trainee with course completion data.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting seeding for trainee-to-talent conversion testing...');

        // Core system setup
        $this->command->info('📋 Creating roles and permissions...');
        $this->call(RolePermissionSeeder::class);

        // LMS system infrastructure (needed for course completion tracking)
        $this->command->info('📚 Setting up LMS infrastructure...');
        $this->call(CourseLevelSeeder::class);
        $this->call(CourseModeSeeder::class);

        // Essential system users only - no sample talents
        $this->command->info('👥 Creating essential system users...');
        $this->call(SystemUserSeeder::class);

        // Talent scouting system (disabled for clean testing)
        $this->command->info('🎯 Setting up talent scouting system...');
        $this->call(TalentScoutingSeeder::class);

        // Trainee with course completion data for conversion testing
        $this->command->info('🎓 Creating trainee with LMS completion data...');
        $this->call(TraineeSeeder::class);

        $this->command->info('✅ Seeding completed successfully!');
        $this->displaySystemSummary();
    }

    private function displaySystemSummary()
    {
        $this->command->info('');
        $this->command->info('📊 CLEAN SYSTEM SETUP FOR CONVERSION TESTING:');
        $this->command->info('═══════════════════════════════════════════════');

        // User statistics
        $userCount = User::count();
        $talentAdminCount = User::whereHas('roles', function($query) {
            $query->where('name', 'talent_admin');
        })->count();
        $traineeCount = User::whereHas('roles', function($query) {
            $query->where('name', 'trainee');
        })->count();
        $talentCount = User::whereHas('roles', function($query) {
            $query->where('name', 'talent');
        })->count();
        $recruiterCount = User::whereHas('roles', function($query) {
            $query->where('name', 'recruiter');
        })->count();

        $this->command->info("👥 TOTAL USERS: {$userCount}");
        $this->command->info("🛠️ Talent Admins: {$talentAdminCount}");
        $this->command->info("🎓 Trainees: {$traineeCount}");
        $this->command->info("⭐ Talents: {$talentCount}");
        $this->command->info("👔 Recruiters: {$recruiterCount}");

        $this->command->info('');
        $this->command->info('🔑 TEST CREDENTIALS:');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('🛠️ Talent Admin: talent.admin@scout.test / password123');
        $this->command->info('👔 Recruiter: recruiter@scout.test / password123');
        $this->command->info('🎓 Trainee: trainee@test.com / password123');

        $this->command->info('');
        $this->command->info('🌐 SYSTEM ACCESS URLS:');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('🔐 Login: http://127.0.0.1:8000/login');
        $this->command->info('🛠️ Talent Admin: http://127.0.0.1:8000/talent-admin/dashboard');
        $this->command->info('👔 Recruiter: http://127.0.0.1:8000/recruiter/dashboard');
        $this->command->info('⭐ Talent: http://127.0.0.1:8000/talent/dashboard');

        $this->command->info('');
        $this->command->info('🔄 TRAINEE-TO-TALENT CONVERSION TEST:');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('1. Login as trainee@test.com → View completed courses');
        $this->command->info('2. Navigate to Profile → Opt-in as talent');
        $this->command->info('3. Logout and login again with same credentials');
        $this->command->info('4. Verify talent role access and auto-generated skills');
        $this->command->info('5. Test recruiter can find and contact the new talent');

        $this->command->info('');
        $this->command->info('✅ READY FOR CONVERSION TESTING!');
        $this->command->info('');
    }
}
