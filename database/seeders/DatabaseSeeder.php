<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for comprehensive system testing.
     * Separates LMS system from Talent Scouting system with proper access control.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting system seeding with proper role separation...');

        // Core system setup
        $this->command->info('📋 Creating roles and permissions...');
        $this->call(RolePermissionSeeder::class);

        // LMS system infrastructure (for admin, trainers, trainees)
        $this->command->info('📚 Setting up LMS infrastructure...');
        $this->call(CourseLevelSeeder::class);
        $this->call(CourseModeSeeder::class);

        // Core system users with proper role separation
        $this->command->info('👥 Creating system users with proper access...');
        $this->call(SystemUserSeeder::class);

        // Talent scouting system data
        $this->command->info('🎯 Setting up talent scouting system...');
        $this->call(TalentScoutingSeeder::class);

        $this->command->info('✅ System seeding completed successfully!');
        $this->displaySystemSummary();
    }

    private function displaySystemSummary()
    {
        $this->command->info('');
        $this->command->info('📊 SYSTEM OVERVIEW:');
        $this->command->info('═══════════════════════════════════════════════');

        // System separation overview
        $this->command->info('🏢 LMS SYSTEM (Learning Management):');
        $this->command->info('   - Admins: Full LMS access only');
        $this->command->info('   - Trainers: Course management');
        $this->command->info('   - Trainees: Course access + talent opt-in');

        $this->command->info('');
        $this->command->info('🎯 TALENT SCOUTING SYSTEM:');
        $this->command->info('   - Talent Admins: Talent management only');
        $this->command->info('   - Talents: Profile & opportunities');
        $this->command->info('   - Recruiters: Talent discovery only');
        $this->command->info('   - Trainees: Can become talents');

        // User statistics
        $userCount = User::count();
        $adminCount = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->count();
        $talentAdminCount = User::whereHas('roles', function($query) {
            $query->where('name', 'talent_admin');
        })->count();
        $trainerCount = User::whereHas('roles', function($query) {
            $query->where('name', 'trainer');
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

        $this->command->info('');
        $this->command->info("👥 TOTAL USERS: {$userCount}");
        $this->command->info('');
        $this->command->info('🏢 LMS SYSTEM USERS:');
        $this->command->info("   🔧 LMS Admins: {$adminCount}");
        $this->command->info("   �‍🏫 Trainers: {$trainerCount}");
        $this->command->info("   📚 Trainees: {$traineeCount}");

        $this->command->info('');
        $this->command->info('🎯 TALENT SCOUTING USERS:');
        $this->command->info("   🎛️ Talent Admins: {$talentAdminCount}");
        $this->command->info("   ⭐ Talents: {$talentCount}");
        $this->command->info("   � Recruiters: {$recruiterCount}");

        // Talent statistics
        if (class_exists(\App\Models\Talent::class)) {
            $activeTalents = \App\Models\Talent::where('is_active', true)->count();
            $this->command->info("   🚀 Active Talent Profiles: {$activeTalents}");
        }

        // Request statistics
        if (class_exists(\App\Models\TalentRequest::class)) {
            $requestCount = \App\Models\TalentRequest::count();
            $pendingCount = \App\Models\TalentRequest::where('status', 'pending')->count();
            $approvedCount = \App\Models\TalentRequest::where('status', 'approved')->count();

            $this->command->info('');
            $this->command->info("📝 TALENT REQUESTS: {$requestCount}");
            $this->command->info("   ⏳ Pending: {$pendingCount}");
            $this->command->info("   ✅ Approved: {$approvedCount}");
        }

        $this->command->info('');
        $this->command->info('🔑 TEST CREDENTIALS:');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('🏢 LMS SYSTEM ACCESS:');
        $this->command->info('   � LMS Admin: admin@lms.test / password123');
        $this->command->info('   👨‍🏫 Trainer: trainer@lms.test / password123');
        $this->command->info('   � Trainee: trainee@lms.test / password123');

        $this->command->info('');
        $this->command->info('🎯 TALENT SCOUTING ACCESS:');
        $this->command->info('   �️ Talent Admin: talent.admin@scout.test / password123');
        $this->command->info('   ⭐ Talent: talent@scout.test / password123');
        $this->command->info('   👔 Recruiter: recruiter@scout.test / password123');

        $this->command->info('');
        $this->command->info('🔄 DUAL ACCESS (LMS + Talent):');
        $this->command->info('   �➡️⭐ Dual Trainee: dual.trainee@test.com / password123');

        $this->command->info('');
        $this->command->info('🌐 SYSTEM ACCESS URLS:');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('🏠 Main LMS: http://127.0.0.1:8000/');
        $this->command->info('🔐 Login: http://127.0.0.1:8000/login');
        $this->command->info('🎯 Talent Dashboard: http://127.0.0.1:8000/talent/dashboard');
        $this->command->info('👔 Recruiter Dashboard: http://127.0.0.1:8000/recruiter/dashboard');
        $this->command->info('🎛️ Talent Admin: http://127.0.0.1:8000/talent-admin/dashboard');

        $this->command->info('');
        $this->command->info('✅ SYSTEMS READY FOR FLOW TESTING!');
        $this->command->info('');
    }
}
