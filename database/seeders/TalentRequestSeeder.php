<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TalentRequest;
use App\Models\Recruiter;
use App\Models\Talent;
use Carbon\Carbon;

class TalentRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */    public function run(): void
    {
        $recruiters = Recruiter::all();
        $talents = Talent::all();

        if ($recruiters->isEmpty() || $talents->isEmpty()) {
            $this->command->warn('No recruiters or talents found. Please run TalentSystemSeeder first.');
            return;
        }

        $statuses = ['pending', 'approved', 'meeting_arranged', 'agreement_reached', 'onboarded', 'rejected', 'completed'];
        $urgencyLevels = ['low', 'medium', 'high'];

        $projectTypes = [
            'Web Development', 'Mobile App Development', 'Data Science', 'Machine Learning',
            'UI/UX Design', 'DevOps Engineering', 'Cloud Architecture', 'Cybersecurity',
            'Software Testing', 'Product Management', 'Digital Marketing', 'E-commerce',
            'Blockchain Development', 'Game Development', 'AI Research'
        ];

        $projectDescriptions = [
            'Looking for experienced developer to build a modern e-commerce platform with advanced features',
            'Seeking talented designer for a complete mobile app redesign project',
            'Need data scientist for predictive analytics and machine learning implementation',
            'Looking for senior backend developer to scale our microservices architecture',
            'Seeking creative UI/UX designer for SaaS platform modernization',
            'Need DevOps engineer to implement CI/CD pipeline and cloud infrastructure',
            'Looking for cybersecurity expert to conduct security audit and implementation',
            'Seeking full-stack developer for fintech application development',
            'Need mobile app developer for cross-platform React Native project',
            'Looking for AI/ML engineer for computer vision project implementation'
        ];

        // Create 25 talent requests with various statuses and realistic data
        for ($i = 0; $i < 25; $i++) {
            $recruiter = $recruiters->random();
            $talent = $talents->random();
            $status = $statuses[array_rand($statuses)];

            // Adjust created_at based on status for realistic workflow
            $createdAt = match($status) {
                'pending' => Carbon::now()->subDays(rand(1, 3)),
                'approved' => Carbon::now()->subDays(rand(2, 7)),
                'meeting_arranged' => Carbon::now()->subDays(rand(5, 14)),
                'agreement_reached' => Carbon::now()->subDays(rand(7, 21)),
                'onboarded' => Carbon::now()->subDays(rand(14, 30)),
                'rejected' => Carbon::now()->subDays(rand(3, 10)),
                'completed' => Carbon::now()->subDays(rand(30, 90)),
            };

            $updatedAt = match($status) {
                'pending' => $createdAt,
                'approved' => $createdAt->copy()->addDays(rand(1, 2)),
                'meeting_arranged' => $createdAt->copy()->addDays(rand(2, 5)),
                'agreement_reached' => $createdAt->copy()->addDays(rand(3, 7)),
                'onboarded' => $createdAt->copy()->addDays(rand(5, 10)),
                'rejected' => $createdAt->copy()->addDays(rand(1, 3)),
                'completed' => $createdAt->copy()->addDays(rand(30, 60)),
            };

            $requestData = [
                'recruiter_id' => $recruiter->id,
                'talent_id' => $talent->id,
                'project_title' => $projectTypes[array_rand($projectTypes)] . ' Project',
                'project_description' => $projectDescriptions[array_rand($projectDescriptions)],
                'requirements' => $this->generateRequirements(),
                'budget_range' => $this->generateBudgetRange(),
                'project_duration' => $this->generateProjectDuration(),
                'urgency_level' => $urgencyLevels[array_rand($urgencyLevels)],
                'status' => $status,
                'recruiter_message' => $this->generateRecruiterMessage(),
                'admin_notes' => $this->generateAdminNotes($status),
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];

            // Add status-specific timestamps
            if ($status === 'approved' || in_array($status, ['meeting_arranged', 'agreement_reached', 'onboarded', 'completed'])) {
                $requestData['approved_at'] = $createdAt->copy()->addDays(rand(1, 3));
            }

            if (in_array($status, ['meeting_arranged', 'agreement_reached', 'onboarded', 'completed'])) {
                $requestData['meeting_arranged_at'] = $createdAt->copy()->addDays(rand(3, 7));
            }

            if (in_array($status, ['onboarded', 'completed'])) {
                $requestData['onboarded_at'] = $createdAt->copy()->addDays(rand(10, 21));
            }

            TalentRequest::create($requestData);
        }

        $this->command->info('Successfully created 25 talent requests with realistic workflow data.');
    }    private function generateRequirements(): string
    {
        $skills = [
            'PHP', 'Laravel', 'JavaScript', 'React', 'Vue.js', 'Node.js', 'Python', 'Django',
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'Docker', 'AWS', 'Azure', 'Git',
            'HTML5', 'CSS3', 'Bootstrap', 'Tailwind CSS', 'REST API', 'GraphQL', 'TypeScript',
            'Java', 'Spring Boot', 'C#', '.NET', 'Flutter', 'React Native', 'Swift', 'Kotlin'
        ];

        $selectedSkills = array_rand(array_flip($skills), rand(3, 6));
        $skillsText = 'Required Skills: ' . implode(', ', $selectedSkills);

        $additionalRequirements = [
            'Previous experience in similar projects',
            'Strong problem-solving abilities',
            'Good communication skills',
            'Ability to work independently',
            'Team collaboration experience',
            'Portfolio of previous work'
        ];

        $additionalReq = $additionalRequirements[array_rand($additionalRequirements)];

        return $skillsText . "\n\nAdditional Requirements: " . $additionalReq;
    }

    private function generateBudgetRange(): string
    {
        $ranges = [
            'Rp 50,000,000 - Rp 100,000,000',
            'Rp 100,000,000 - Rp 200,000,000',
            'Rp 200,000,000 - Rp 500,000,000',
            'Rp 25,000,000 - Rp 50,000,000',
            'Negotiable',
            'Rp 75,000,000 - Rp 150,000,000'
        ];

        return $ranges[array_rand($ranges)];
    }

    private function generateProjectDuration(): string
    {
        $durations = [
            '3-6 months', '6-12 months', '1-3 months', '12+ months',
            '2-4 months', '4-8 months', 'Flexible timeline'
        ];

        return $durations[array_rand($durations)];
    }

    private function generateRecruiterMessage(): string
    {
        $messages = [
            'Looking for a dedicated professional who can deliver high-quality work.',
            'We value creativity, innovation, and attention to detail.',
            'This is an exciting opportunity to work with cutting-edge technology.',
            'Join our dynamic team and make a significant impact.',
            'We offer competitive compensation and flexible working arrangements.',
            'Looking for someone passionate about technology and innovation.',
            'Great opportunity for professional growth and development.',
            'We need someone who can hit the ground running.'
        ];

        return $messages[array_rand($messages)];
    }

    private function generateAdminNotes(string $status): ?string
    {
        return match($status) {
            'pending' => 'New request submitted. Awaiting initial review.',
            'approved' => 'Request approved after thorough evaluation. Both parties notified.',
            'meeting_arranged' => 'Meeting scheduled between recruiter and talent.',
            'agreement_reached' => 'Terms agreed upon. Proceeding to onboarding.',
            'onboarded' => 'Talent successfully onboarded. Project started.',
            'rejected' => 'Request rejected due to skill mismatch or unavailability.',
            'completed' => 'Project completed successfully. Positive feedback received.',
            default => null,
        };
    }
}
