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
     * Seed minimal core system users for manual flow testing.
     * Only essential accounts + few talents with course-based skills.
     */
    public function run(): void
    {
        $this->command->info('👥 Creating essential system users...');

        // ===============================================
        // ESSENTIAL SYSTEM ACCOUNTS
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

        // ===============================================
        // SAMPLE TALENTS WITH COURSE-BASED SKILLS
        // ===============================================

        $this->command->info('🎯 Creating sample talents with course-based skills...');

        // Talent 1: Frontend Developer
        $frontendTalent = User::firstOrCreate([
            'email' => 'sarah.frontend@test.com'
        ], [
            'name' => 'Sarah Frontend',
            'pekerjaan' => 'Frontend Developer',
            'avatar' => null,
            'password' => bcrypt('password123'),
            'available_for_scouting' => true,
            'talent_skills' => json_encode(['HTML/CSS', 'JavaScript', 'React', 'Vue.js', 'Responsive Design']),
            'hourly_rate' => 45.00,
            'talent_bio' => 'Frontend developer with 3+ years experience. Completed courses in React, Vue.js, and modern CSS. Passionate about creating beautiful, responsive user interfaces.',
            'portfolio_url' => 'https://github.com/sarahfrontend',
            'location' => 'Jakarta, Indonesia',
            'experience_level' => 'intermediate',
            'is_active_talent' => true,
        ]);
        $frontendTalent->assignRole('talent');

        Talent::firstOrCreate([
            'user_id' => $frontendTalent->id
        ], [
            'is_active' => true
        ]);

        // Talent 2: Backend Developer
        $backendTalent = User::firstOrCreate([
            'email' => 'john.backend@test.com'
        ], [
            'name' => 'John Backend',
            'pekerjaan' => 'Backend Developer',
            'avatar' => null,
            'password' => bcrypt('password123'),
            'available_for_scouting' => true,
            'talent_skills' => json_encode(['PHP', 'Laravel', 'MySQL', 'API Development', 'Server Management']),
            'hourly_rate' => 60.00,
            'talent_bio' => 'Backend developer specializing in Laravel and PHP. Completed advanced courses in database design, API development, and server management.',
            'portfolio_url' => 'https://github.com/johnbackend',
            'location' => 'Bandung, Indonesia',
            'experience_level' => 'advanced',
            'is_active_talent' => true,
        ]);
        $backendTalent->assignRole('talent');

        Talent::firstOrCreate([
            'user_id' => $backendTalent->id
        ], [
            'is_active' => true
        ]);

        // Talent 3: Full Stack Developer
        $fullstackTalent = User::firstOrCreate([
            'email' => 'alex.fullstack@test.com'
        ], [
            'name' => 'Alex Fullstack',
            'pekerjaan' => 'Full Stack Developer',
            'avatar' => null,
            'password' => bcrypt('password123'),
            'available_for_scouting' => true,
            'talent_skills' => json_encode(['JavaScript', 'Node.js', 'React', 'Express.js', 'MongoDB', 'Git']),
            'hourly_rate' => 75.00,
            'talent_bio' => 'Full-stack developer with comprehensive training in modern web technologies. Completed courses in MERN stack development and agile methodologies.',
            'portfolio_url' => 'https://github.com/alexfullstack',
            'location' => 'Surabaya, Indonesia',
            'experience_level' => 'advanced',
            'is_active_talent' => true,
        ]);
        $fullstackTalent->assignRole('talent');

        Talent::firstOrCreate([
            'user_id' => $fullstackTalent->id
        ], [
            'is_active' => true
        ]);

        $this->command->info('   ✓ Created 3 sample talents with course-based skills');
        $this->command->info('✅ Essential users created successfully!');
    }
}
