<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Talent;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class AdditionalTalentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Define skill categories and levels
        $skillCategories = [
            'Programming' => [
                'JavaScript Fundamentals',
                'React Development',
                'Vue.js Development',
                'Node.js Backend',
                'Python Programming',
                'Django Framework',
                'Laravel Development',
                'PHP Development',
                'Java Programming',
                'Spring Boot',
                'C# .NET Development',
                'Angular Development',
                'TypeScript',
                'Express.js',
                'FastAPI'
            ],
            'Data Science' => [
                'Python for Data Science',
                'Machine Learning',
                'Deep Learning',
                'Data Visualization',
                'Pandas & NumPy',
                'TensorFlow',
                'PyTorch',
                'SQL Analytics',
                'Power BI',
                'Tableau',
                'R Programming',
                'Statistical Analysis'
            ],
            'Design' => [
                'UI/UX Design',
                'Figma Design',
                'Adobe Photoshop',
                'Adobe Illustrator',
                'Web Design',
                'Mobile App Design',
                'Graphic Design',
                'Brand Design',
                'User Research',
                'Prototyping'
            ],
            'Mobile Development' => [
                'Flutter Development',
                'React Native',
                'iOS Development',
                'Android Development',
                'Kotlin Programming',
                'Swift Programming',
                'Mobile UI Design',
                'App Store Optimization'
            ],
            'DevOps & Cloud' => [
                'AWS Cloud',
                'Docker Containerization',
                'Kubernetes',
                'CI/CD Pipelines',
                'Azure Cloud',
                'Google Cloud Platform',
                'Linux Administration',
                'Infrastructure as Code'
            ],
            'Digital Marketing' => [
                'SEO Optimization',
                'Social Media Marketing',
                'Google Ads',
                'Content Marketing',
                'Email Marketing',
                'Analytics & Reporting',
                'PPC Advertising',
                'Brand Management'
            ]
        ];

        $experienceLevels = ['beginner', 'intermediate', 'advanced'];
        $jobTitles = [
            'Full Stack Developer', 'Frontend Developer', 'Backend Developer',
            'Data Scientist', 'Machine Learning Engineer', 'UI/UX Designer',
            'Mobile App Developer', 'DevOps Engineer', 'Digital Marketing Specialist',
            'Product Manager', 'Software Engineer', 'Web Developer',
            'Graphic Designer', 'Business Analyst', 'Project Manager'
        ];

        echo "🚀 Creating additional talent data...\n";

        // Create 15 additional talented users
        for ($i = 1; $i <= 15; $i++) {
            // Generate user data
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();
            $email = strtolower($firstName . '.' . $lastName . $i . '@talent.test');

            echo "Creating talent {$i}: {$firstName} {$lastName}\n";

            // Create user with talent availability
            $user = User::create([
                'name' => $firstName . ' ' . $lastName,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'avatar' => 'default-avatar.png', // Default avatar
                'phone' => $faker->phoneNumber(),
                'pekerjaan' => $faker->randomElement($jobTitles),

                // Talent-specific fields
                'available_for_scouting' => true,
                'experience_level' => $faker->randomElement($experienceLevels),
                'talent_bio' => $faker->paragraph(3),
                'portfolio_url' => 'https://' . strtolower($firstName . $lastName) . '.dev',
                'talent_skills' => $this->generateSkills($skillCategories, $faker),
                'location' => $faker->city() . ', ' . $faker->state(),
                'hourly_rate' => $faker->numberBetween(25, 150),
                'is_active_talent' => true,
            ]);

            // Assign trainee role (for LMS access)
            $user->assignRole('trainee');

            // Assign talent role (for talent platform access)
            $user->assignRole('talent');            // Create corresponding Talent record
            Talent::create([
                'user_id' => $user->id,
                'is_active' => true,
            ]);
        }

        echo "✅ Successfully created 15 additional talented users!\n";
        echo "📊 Total users with talent availability: " . User::where('available_for_scouting', true)->count() . "\n";
        echo "🎯 Total active talents: " . Talent::where('is_active', true)->count() . "\n";
    }

    /**
     * Generate realistic skills for a talent
     */
    private function generateSkills($skillCategories, $faker)
    {
        $skills = [];
        $experienceLevels = ['beginner', 'intermediate', 'advanced'];

        // Pick 1-3 categories for this talent
        $selectedCategories = $faker->randomElements(array_keys($skillCategories), $faker->numberBetween(1, 3));

        foreach ($selectedCategories as $category) {
            // Pick 2-5 skills from this category
            $categorySkills = $faker->randomElements($skillCategories[$category], $faker->numberBetween(2, 5));

            foreach ($categorySkills as $skill) {
                $skills[] = [
                    'name' => $skill,
                    'level' => $faker->randomElement($experienceLevels),
                    'category' => $category
                ];
            }
        }

        return json_encode($skills);
    }
}
