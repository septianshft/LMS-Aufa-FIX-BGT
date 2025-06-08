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
     * Seed core system users with proper role separation.
     *
     * LMS System:
     * - Admin: LMS management only
     * - Trainer: Course management
     * - Trainee: Course access + talent opt-in capability
     *
     * Talent Scouting System:
     * - Talent Admin: Talent system management only
     * - Talent: Profile management & opportunities
     * - Recruiter: Talent discovery only
     */
    public function run(): void
    {
        $this->command->info('👥 Creating core system users...');

        // ===============================================
        // LMS SYSTEM USERS
        // ===============================================

        $this->command->info('🏢 Creating LMS system users...');

        // LMS Admin - Full LMS access only
        $adminUser = User::firstOrCreate([
            'email' => 'admin@lms.test'
        ], [
            'name' => 'LMS Administrator',
            'pekerjaan' => 'System Administrator',
            'avatar' => 'images/default-avatar.png',
            'password' => bcrypt('password123'),
        ]);
        $adminUser->assignRole('admin');
        $this->command->info('   ✓ LMS Admin created: admin@lms.test');

        // Trainer - Course management
        $trainerUser = User::firstOrCreate([
            'email' => 'trainer@lms.test'
        ], [
            'name' => 'John Trainer',
            'pekerjaan' => 'Course Instructor',
            'avatar' => 'images/default-avatar.png',
            'password' => bcrypt('password123'),
        ]);
        $trainerUser->assignRole('trainer');
        $this->command->info('   ✓ Trainer created: trainer@lms.test');

        // Trainee - LMS access only (can later opt into talent system)
        $traineeUser = User::firstOrCreate([
            'email' => 'trainee@lms.test'
        ], [
            'name' => 'Sarah Student',
            'pekerjaan' => 'Software Engineering Student',
            'avatar' => 'images/default-avatar.png',
            'password' => bcrypt('password123'),
        ]);
        $traineeUser->assignRole('trainee');
        $this->command->info('   ✓ Trainee created: trainee@lms.test');

        // ===============================================
        // TALENT SCOUTING SYSTEM USERS
        // ===============================================

        $this->command->info('🎯 Creating talent scouting system users...');

        // Talent Admin - Talent system management only
        $talentAdminUser = User::firstOrCreate([
            'email' => 'talent.admin@scout.test'
        ], [
            'name' => 'Emma Talent Admin',
            'pekerjaan' => 'Talent System Administrator',
            'avatar' => 'images/default-avatar.png',
            'password' => bcrypt('password123'),
        ]);
        $talentAdminUser->assignRole('talent_admin');

        // Create talent admin profile
        TalentAdmin::firstOrCreate([
            'user_id' => $talentAdminUser->id
        ], [
            'is_active' => true
        ]);
        $this->command->info('   ✓ Talent Admin created: talent.admin@scout.test');        // Talent - Profile management & opportunities
        $talentUser = User::firstOrCreate([
            'email' => 'talent@scout.test'
        ], [
            'name' => 'Alex Developer',
            'pekerjaan' => 'Full Stack Developer',
            'avatar' => 'images/default-avatar.png',
            'password' => bcrypt('password123'),
            'available_for_scouting' => true,
            'talent_skills' => json_encode(['Laravel', 'Vue.js', 'React', 'JavaScript', 'PHP']),
            'hourly_rate' => 75.00,
            'talent_bio' => 'Experienced full-stack developer with expertise in Laravel, Vue.js, and React. Passionate about creating scalable web applications.',
            'portfolio_url' => 'https://github.com/alexdev',
            'location' => 'Jakarta, Indonesia',
            'experience_level' => 'advanced',
            'is_active_talent' => true,
        ]);
        $talentUser->assignRole('talent');

        // Create talent relationship record
        Talent::firstOrCreate([
            'user_id' => $talentUser->id
        ], [
            'is_active' => true
        ]);
        $this->command->info('   ✓ Talent created: talent@scout.test');

        // Recruiter - Talent discovery only
        $recruiterUser = User::firstOrCreate([
            'email' => 'recruiter@scout.test'
        ], [
            'name' => 'Michael Recruiter',
            'pekerjaan' => 'Senior Talent Recruiter',
            'avatar' => 'images/default-avatar.png',
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

        // ===============================================
        // DUAL-ACCESS USER (LMS + TALENT)
        // ===============================================

        $this->command->info('🔄 Creating dual-access user...');        // Trainee who has opted into talent system
        $dualUser = User::firstOrCreate([
            'email' => 'dual.trainee@test.com'
        ], [
            'name' => 'Jessica Dual',
            'pekerjaan' => 'Frontend Developer & Student',
            'avatar' => 'images/default-avatar.png',
            'password' => bcrypt('password123'),
            'available_for_scouting' => true,
            'talent_skills' => json_encode(['React', 'JavaScript', 'HTML/CSS', 'TypeScript', 'Vue.js']),
            'hourly_rate' => 45.00,
            'talent_bio' => 'Frontend developer and continuous learner. Currently studying advanced web technologies while building modern user interfaces.',
            'portfolio_url' => 'https://github.com/jessicadual',
            'location' => 'Bandung, Indonesia',
            'experience_level' => 'intermediate',
            'is_active_talent' => true,
        ]);
        $dualUser->assignRole(['trainee', 'talent']);

        // Create talent relationship record for the dual user
        Talent::firstOrCreate([
            'user_id' => $dualUser->id
        ], [
            'is_active' => true
        ]);
        $this->command->info('   ✓ Dual user created: dual.trainee@test.com (LMS + Talent access)');

        $this->command->info('✅ System users created successfully!');
    }
}
