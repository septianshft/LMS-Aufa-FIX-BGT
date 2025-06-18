<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Recruiter;
use App\Models\Talent;
use App\Models\TalentAdmin;
use App\Models\Project;
use App\Models\ProjectAssignment;
use App\Models\ProjectTimelineEvent;
use Carbon\Carbon;

class ProjectSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test recruiter user
        $recruiterUser = User::firstOrCreate([
            'email' => 'recruiter@test.com'
        ], [
            'name' => 'Test Recruiter',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'available_for_scouting' => false
        ]);

        // Create recruiter profile
        $recruiter = Recruiter::firstOrCreate([
            'user_id' => $recruiterUser->id
        ], [
            'company_name' => 'Tech Solutions Inc.',
            'industry' => 'Technology',
            'company_size' => '50-100',
            'website' => 'https://techsolutions.com',
            'company_description' => 'Leading technology consulting firm',
            'phone' => '+1-555-0123',
            'address' => '123 Tech Street, Silicon Valley',
            'is_active' => true
        ]);

        // Create test talent admin user
        $adminUser = User::firstOrCreate([
            'email' => 'admin@test.com'
        ], [
            'name' => 'Test Admin',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'available_for_scouting' => false
        ]);

        // Create talent admin profile
        $talentAdmin = TalentAdmin::firstOrCreate([
            'user_id' => $adminUser->id
        ], [
            'is_active' => true
        ]);

        // Create test talent user
        $talentUser = User::firstOrCreate([
            'email' => 'talent@test.com'
        ], [
            'name' => 'Test Talent Developer',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'available_for_scouting' => true,
            'talent_skills' => json_encode(['PHP', 'Laravel', 'JavaScript', 'React', 'Node.js']),
            'talent_bio' => 'Experienced full-stack developer with 5+ years in web development',
            'portfolio_url' => 'https://github.com/testtalent',
            'location' => 'Remote',
            'phone' => '+1-555-0456',
            'is_active_talent' => true
        ]);

        // Assign roles to users
        $recruiterUser->assignRole('recruiter');
        $adminUser->assignRole('talent_admin');
        $talentUser->assignRole('talent');

        // Create talent profile
        $talent = Talent::firstOrCreate([
            'user_id' => $talentUser->id
        ], [
            'is_active' => true,
            'scouting_metrics' => [
                'experience_level' => 'mid_level',
                'availability' => 'full_time',
                'skills' => ['PHP', 'Laravel', 'JavaScript', 'React', 'Node.js'],
                'portfolio_url' => 'https://github.com/testtalent',
                'location' => 'Remote',
                'verification_status' => 'verified'
            ]
        ]);

        // Create test projects
        $projects = [
            [
                'title' => 'E-commerce Platform Development',
                'description' => 'Build a modern e-commerce platform with React frontend and Laravel backend. Features include user authentication, product catalog, shopping cart, payment integration, and admin dashboard.',
                'industry' => 'E-commerce',
                'general_requirements' => 'Experience with Laravel, React, payment gateways, and modern web development practices. Strong understanding of security best practices.',
                'overall_budget_min' => 15000,
                'overall_budget_max' => 25000,
                'expected_start_date' => Carbon::now()->addDays(7),
                'expected_end_date' => Carbon::now()->addDays(97), // 90 days project
                'estimated_duration_days' => 90,
                'status' => Project::STATUS_PENDING_APPROVAL,
                'recruiter_id' => $recruiter->id
            ],
            [
                'title' => 'Mobile App Backend API',
                'description' => 'Develop RESTful API backend for a mobile fitness tracking application. Includes user management, workout tracking, progress analytics, and social features.',
                'industry' => 'Health & Fitness',
                'general_requirements' => 'Strong API development skills, database design, and mobile app backend experience. Knowledge of fitness/health domain preferred.',
                'overall_budget_min' => 8000,
                'overall_budget_max' => 15000,
                'expected_start_date' => Carbon::now()->addDays(14),
                'expected_end_date' => Carbon::now()->addDays(74), // 60 days project
                'estimated_duration_days' => 60,
                'status' => Project::STATUS_APPROVED,
                'recruiter_id' => $recruiter->id,
                'admin_approved_by' => $talentAdmin->id,
                'admin_approved_at' => Carbon::now()->subDays(2)
            ]
        ];

        foreach ($projects as $projectData) {
            $project = Project::firstOrCreate([
                'title' => $projectData['title'],
                'recruiter_id' => $projectData['recruiter_id']
            ], $projectData);

            // Create timeline events
            ProjectTimelineEvent::firstOrCreate([
                'project_id' => $project->id,
                'event_type' => 'created'
            ], [
                'event_description' => "Project '{$project->title}' created by recruiter {$recruiterUser->name}",
                'triggered_by' => $recruiterUser->id,
                'event_data' => json_encode(['project_title' => $project->title])
            ]);

            if ($project->status === Project::STATUS_APPROVED) {
                ProjectTimelineEvent::firstOrCreate([
                    'project_id' => $project->id,
                    'event_type' => 'approved'
                ], [
                    'event_description' => "Project '{$project->title}' approved by admin {$adminUser->name}",
                    'triggered_by' => $adminUser->id,
                    'event_data' => json_encode([
                        'admin_name' => $adminUser->name,
                        'project_title' => $project->title
                    ])
                ]);

                // Create sample assignment for approved project
                ProjectAssignment::firstOrCreate([
                    'project_id' => $project->id,
                    'talent_id' => $talent->id
                ], [
                    'specific_role' => 'Full-Stack Developer',
                    'specific_requirements' => 'Lead development of core features, implement responsive UI, develop API endpoints, ensure code quality and testing',
                    'individual_budget' => 6500,
                    'talent_start_date' => $project->expected_start_date,
                    'talent_end_date' => $project->expected_end_date,
                    'working_hours_per_week' => 40,
                    'priority_level' => 'high',
                    'status' => 'assigned'
                ]);
            }
        }

        $this->command->info('Project system test data created successfully!');
        $this->command->info('Recruiter: recruiter@test.com / password');
        $this->command->info('Admin: admin@test.com / password');
        $this->command->info('Talent: talent@test.com / password');
    }
}
