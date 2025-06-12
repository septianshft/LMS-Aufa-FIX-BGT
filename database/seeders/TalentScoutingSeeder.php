<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Talent;
use App\Models\TalentRequest;
use App\Models\Recruiter;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TalentScoutingSeeder extends Seeder
{
    /**
     * Seed talent scouting system data.
     * NOTE: For trainee-to-talent conversion testing, this seeder is disabled.
     * Only talent admin and recruiter accounts are created via SystemUserSeeder.
     */
    public function run(): void
    {
        $this->command->info('🎯 TalentScoutingSeeder: Skipped for clean testing environment');
        $this->command->info('   ℹ️  Only talent admin and recruiter accounts will be available');
        $this->command->info('   ℹ️  Use TraineeSeeder to test trainee-to-talent conversion flow');

        // All talent seeding methods are commented out for clean testing
        // $this->createAdditionalTalents($faker);
        // $this->createTalentRequests($faker);
        // $this->createAdditionalRecruiters($faker);

        $this->command->info('✅ Talent scouting seeder completed (clean mode)');
    }

    private function createAdditionalTalents($faker)
    {
        $this->command->info('   📋 Creating additional talent profiles...');

        // Define skill categories for realistic talent profiles
        $skillCategories = [
            'Frontend' => ['React', 'Vue.js', 'Angular', 'TypeScript', 'HTML/CSS', 'JavaScript'],
            'Backend' => ['Laravel', 'Node.js', 'Python', 'Django', 'Express.js', 'PHP'],
            'Mobile' => ['Flutter', 'React Native', 'iOS Development', 'Android Development'],
            'Data Science' => ['Python', 'Machine Learning', 'Data Analysis', 'TensorFlow', 'Pandas'],
            'DevOps' => ['Docker', 'AWS', 'Kubernetes', 'CI/CD', 'Linux Administration'],
            'Design' => ['UI/UX Design', 'Figma', 'Adobe Creative Suite', 'Prototyping']
        ];

        $jobTitles = [
            'Frontend Developer',
            'Backend Developer',
            'Full Stack Developer',
            'Mobile App Developer',
            'Data Scientist',
            'DevOps Engineer',
            'UI/UX Designer',
            'Software Engineer',
            'Web Developer',
            'System Administrator'
        ];        // Create 8 additional diverse talent profiles
        for ($i = 1; $i <= 8; $i++) {
            $category = $faker->randomElement(array_keys($skillCategories));
            $skills = $faker->randomElements($skillCategories[$category], rand(2, 4));

            $user = User::create([
                'name' => $faker->name,
                'email' => "talent{$i}@demo.test",
                'pekerjaan' => $faker->randomElement($jobTitles),
                'avatar' => 'images/default-avatar.png',
                'password' => bcrypt('password123'),
                // Talent fields in users table
                'available_for_scouting' => $faker->boolean(85),
                'talent_skills' => json_encode($skills),
                'hourly_rate' => rand(25, 150),
                'talent_bio' => $this->generateTalentBio($faker->randomElement($jobTitles), $skills, $faker),
                'portfolio_url' => $faker->boolean(70) ? "https://github.com/{$faker->userName}" : null,
                'location' => $faker->city . ', ' . $faker->country,
                'experience_level' => $faker->randomElement(['beginner', 'intermediate', 'advanced', 'expert']),
                'is_active_talent' => $faker->boolean(85),
            ]);

            $user->assignRole('talent');

            // Create basic talent relationship record
            Talent::create([
                'user_id' => $user->id,
                'is_active' => $user->is_active_talent ?? true,
            ]);
        }

        $this->command->info('   ✓ Created 8 additional talent profiles');
    }

    private function createTalentRequests($faker)
    {
        $this->command->info('   📝 Creating talent project requests for workflow testing...');

        // Get available recruiters and talents
        $recruiters = User::whereHas('roles', function($query) {
            $query->where('name', 'recruiter');
        })->with('recruiter')->get();

        $talents = User::whereHas('roles', function($query) {
            $query->where('name', 'talent');
        })->with('talent')->get();

        if ($recruiters->isEmpty() || $talents->isEmpty()) {
            $this->command->info('   ⚠️ Skipping talent requests - need recruiters and talents');
            return;
        }

        $projectTitles = [
            'Mobile App Development',
            'E-commerce Website Build',
            'Database Migration Project',
            'API Integration',
            'Frontend Redesign',
            'Automation Script Development'
        ];

        $statuses = ['pending', 'approved', 'meeting_arranged', 'rejected'];
        $urgencyLevels = ['low', 'medium', 'high'];

        $requestCount = 0;
        for ($i = 0; $i < 8; $i++) {
            $recruiter = $recruiters->random();
            $talent = $talents->random();

            // Skip if no recruiter/talent record
            if (!$recruiter->recruiter || !$talent->talent) continue;

            $projectTitle = $faker->randomElement($projectTitles);

            $request = TalentRequest::create([
                'recruiter_id' => $recruiter->recruiter->id,
                'talent_id' => $talent->talent->id,
                'talent_user_id' => $talent->id,
                'project_title' => $projectTitle,
                'project_description' => $this->generateProjectDescription($faker, $projectTitle),
                'requirements' => $this->generateRequirements($faker),
                'budget_range' => $faker->randomElement(['$1,000 - $5,000', '$5,000 - $10,000', '$10,000 - $20,000', 'Negotiable']),
                'project_duration' => $faker->randomElement(['1-2 weeks', '1 month', '2-3 months', '6+ months']),
                'urgency_level' => $faker->randomElement($urgencyLevels),
                'status' => $faker->randomElement($statuses),
                'recruiter_message' => $this->generateRecruiterMessage($faker, $projectTitle),
            ]);

            $requestCount++;
        }

        $this->command->info("   ✓ Created {$requestCount} project requests between recruiters and talents");
    }

    private function createAdditionalRecruiters($faker)
    {
        $this->command->info('   👔 Creating additional recruiter profiles...');

        $companies = [
            ['name' => 'InnovaTech Solutions', 'website' => 'https://innovatech.com'],
            ['name' => 'Digital Dynamics Corp', 'website' => 'https://digitaldynamics.co'],
            ['name' => 'FutureSoft Industries', 'website' => 'https://futuresoft.io'],
        ];

        foreach ($companies as $index => $company) {
            $user = User::create([
                'name' => $faker->name,
                'email' => "recruiter" . ($index + 2) . "@demo.test",
                'pekerjaan' => 'Talent Recruiter at ' . $company['name'],
                'avatar' => 'images/default-avatar.png',
                'password' => bcrypt('password123'),
            ]);

            $user->assignRole('recruiter');

            // Create recruiter record with only valid fields
            Recruiter::create([
                'user_id' => $user->id,
                'is_active' => true,
            ]);
        }

        $this->command->info('   ✓ Created 3 additional recruiter profiles');
    }

    private function generateTalentBio($jobTitle, $skills, $faker)
    {
        $templates = [
            "Experienced {$jobTitle} with expertise in " . implode(', ', $skills) . ". Passionate about creating innovative solutions and delivering high-quality results.",
            "Dedicated {$jobTitle} specializing in " . implode(' and ', $skills) . ". Always eager to take on new challenges and contribute to successful projects.",
            "Skilled {$jobTitle} with strong background in " . implode(', ', $skills) . ". Committed to continuous learning and professional growth.",
        ];

        return $faker->randomElement($templates);
    }

    private function generateProjectDescription($faker, $projectTitle)
    {
        $descriptions = [
            'Mobile App Development' => "We're looking for a skilled developer to create a cross-platform mobile application. The app should have user authentication, real-time notifications, and seamless integration with our existing backend services.",
            'E-commerce Website Build' => "Our company needs a modern e-commerce platform with features like product catalog, shopping cart, payment integration, and administrative dashboard. Experience with modern frameworks is preferred.",
            'Database Migration Project' => "We need assistance migrating our legacy database to a more modern architecture. This includes data mapping, performance optimization, and ensuring zero downtime during the transition.",
            'API Integration' => "Looking for a developer to integrate multiple third-party APIs into our existing system. This includes payment gateways, social media APIs, and analytics services.",
            'Frontend Redesign' => "Our website needs a complete frontend redesign with modern UI/UX principles. Should be responsive, accessible, and optimized for performance.",
            'Automation Script Development' => "We need custom scripts to automate our daily business processes. This includes data processing, report generation, and system monitoring tasks."
        ];

        return $descriptions[$projectTitle] ?? "Exciting project opportunity that requires technical expertise and creative problem-solving skills.";
    }

    private function generateRequirements($faker)
    {
        $requirements = [
            "3+ years of experience in web development\nProficiency in JavaScript and React\nExperience with REST APIs\nStrong problem-solving skills",
            "Experience with mobile app development\nKnowledge of Flutter or React Native\nFamiliarity with app store deployment\nGood communication skills",
            "Database design and optimization experience\nSQL and NoSQL database knowledge\nExperience with data migration tools\nAttention to detail",
            "Backend development experience\nAPI design and development skills\nExperience with authentication systems\nCode documentation practices",
            "Frontend development expertise\nUI/UX design understanding\nResponsive design experience\nCross-browser compatibility knowledge",
            "Scripting and automation experience\nFamiliarity with CI/CD pipelines\nSystem administration knowledge\nExperience with monitoring tools"
        ];

        return $faker->randomElement($requirements);
    }

    private function generateRecruiterMessage($faker, $projectTitle)
    {
        $messages = [
            'Mobile App Development' => "Hi! We've reviewed your profile and think you'd be a great fit for our mobile app project. We're a fast-growing startup looking for talented developers.",
            'E-commerce Website Build' => "Hello! Your skills align perfectly with our e-commerce project requirements. We're looking forward to discussing this opportunity with you.",
            'Database Migration Project' => "Hi there! We need someone with your database expertise for a critical migration project. This is a high-impact project with good compensation.",
            'API Integration' => "Hello! We're impressed by your backend development skills and would love to work with you on our API integration project.",
            'Frontend Redesign' => "Hi! Your frontend expertise is exactly what we need for our website redesign. We value quality work and good collaboration.",
            'Automation Script Development' => "Hello! We think your programming skills would be perfect for our automation project. Looking forward to hearing from you!"
        ];

        return $messages[$projectTitle] ?? "We're excited about the possibility of working with you on this project. Let's discuss the details!";
    }
}
