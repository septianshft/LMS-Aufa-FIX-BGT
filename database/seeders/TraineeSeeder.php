<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\CourseLevel;
use App\Models\CourseTrainee;
use App\Models\CourseProgress;
use App\Models\Certificate;
use App\Models\Trainer;
use App\Models\FinalQuiz;
use App\Models\QuizAttempt;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TraineeSeeder extends Seeder
{
    /**
     * Seed 3 trainee users with completed LMS courses for testing trainee-to-talent conversion.
     * This creates realistic learning histories that will be used to generate talent skills.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $this->command->info('🎓 Creating 3 trainees with LMS completion data...');

        // ===============================================
        // CREATE SAMPLE COURSES IF NEEDED
        // ===============================================
        $this->createSampleCourses();

        // ===============================================
        // TRAINEE PROFILES DATA
        // ===============================================
        $traineeProfiles = [
            [
                'email' => 'trainee1@test.com',
                'name' => 'Lisa Web Developer',
                'pekerjaan' => 'Junior Frontend Developer',
                'location' => 'Jakarta, Indonesia',
                'courses' => [
                    'Complete Web Development Bootcamp',
                    'Advanced JavaScript Programming',
                    'React.js Frontend Development'
                ]
            ],
            [
                'email' => 'trainee2@test.com',
                'name' => 'Ahmad Backend Dev',
                'pekerjaan' => 'Junior Backend Developer',
                'location' => 'Bandung, Indonesia',
                'courses' => [
                    'Laravel Framework Mastery',
                    'Advanced JavaScript Programming',
                    'Complete Web Development Bootcamp'
                ]
            ],
            [
                'email' => 'trainee3@test.com',
                'name' => 'Maria Fullstack',
                'pekerjaan' => 'Junior Fullstack Developer',
                'location' => 'Surabaya, Indonesia',
                'courses' => [
                    'Complete Web Development Bootcamp',
                    'Advanced JavaScript Programming',
                    'Laravel Framework Mastery',
                    'React.js Frontend Development'
                ]
            ]
        ];

        // ===============================================
        // CREATE EACH TRAINEE
        // ===============================================
        foreach ($traineeProfiles as $index => $profile) {
            $this->command->info("   📚 Creating trainee " . ($index + 1) . ": {$profile['name']}");

            // Create trainee user
            $traineeUser = User::firstOrCreate([
                'email' => $profile['email']
            ], [
                'name' => $profile['name'],
                'pekerjaan' => $profile['pekerjaan'],
                'avatar' => null,
                'password' => bcrypt('password123'),
                'available_for_scouting' => false, // Will be enabled when they opt-in
                'talent_skills' => null, // Will be auto-generated from courses
                'talent_bio' => null,
                'portfolio_url' => null,
                'location' => $profile['location'],
                'is_active_talent' => false, // Will be enabled after conversion
            ]);

            // Assign trainee role if not already assigned
            if (!$traineeUser->hasRole('trainee')) {
                $traineeUser->assignRole('trainee');
            }

            // Get courses for this trainee
            $courses = Course::whereIn('name', $profile['courses'])->get();

            // Complete courses for this trainee
            foreach ($courses as $course) {
                // Create course enrollment (trainee relationship)
                CourseTrainee::firstOrCreate([
                    'user_id' => $traineeUser->id,
                    'course_id' => $course->id,
                ]);

                // Create course progress (100% completed)
                CourseProgress::firstOrCreate([
                    'user_id' => $traineeUser->id,
                    'course_id' => $course->id,
                ], [
                    'completed_videos' => [],
                    'completed_materials' => [],
                    'completed_tasks' => [],
                    'quiz_passed' => true,
                    'progress' => 100.0,
                ]);

                // Generate certificate
                Certificate::firstOrCreate([
                    'user_id' => $traineeUser->id,
                    'course_id' => $course->id,
                ], [
                    'path' => '/certificates/' . $traineeUser->id . '/' . $course->id . '.pdf',
                    'generated_at' => $faker->dateTimeBetween('-3 months', '-1 month'),
                ]);

                // Create final quiz for the course if it doesn't exist
                $finalQuiz = FinalQuiz::firstOrCreate([
                    'course_id' => $course->id,
                ], [
                    'title' => $course->name . ' - Final Assessment',
                    'passing_score' => 70,
                    'is_hidden_from_trainee' => false,
                ]);

                // Create quiz attempt with a realistic score (75-95%)
                $quizScore = $faker->numberBetween(75, 95);
                QuizAttempt::firstOrCreate([
                    'final_quiz_id' => $finalQuiz->id,
                    'user_id' => $traineeUser->id,
                ], [
                    'score' => $quizScore,
                    'is_passed' => $quizScore >= $finalQuiz->passing_score,
                ]);

                // Generate simplified skills from course completion
                $traineeUser->addSkillFromCourse($course);
            }

            // Update user with learning-based profile data
            $traineeUser->update([
                'talent_bio' => $this->generateLearningBasedBio($traineeUser->name, count($courses)),
            ]);

            $skills = $traineeUser->getTalentSkillsArray();
            $skillNames = array_column($skills, 'skill_name');

            $this->command->info("      ✓ Completed {$courses->count()} courses");
            $this->command->info("      ✓ Generated skills: " . implode(', ', array_slice($skillNames, 0, 4)) . (count($skillNames) > 4 ? '...' : ''));
        }

        $this->command->info('✅ Created 3 trainees with LMS completion data successfully!');
        $this->command->info('');
        $this->command->info('🧪 TEST SCENARIO:');
        $this->command->info('   Available trainees for testing:');
        $this->command->info('   • trainee1@test.com / password123 (Frontend focus)');
        $this->command->info('   • trainee2@test.com / password123 (Backend focus)');
        $this->command->info('   • trainee3@test.com / password123 (Fullstack)');
        $this->command->info('   Steps: Login → View completed courses → Opt-in as talent');
    }    /**
     * Create sample courses for testing if they don't exist
     */
    private function createSampleCourses(): void
    {
        $faker = Faker::create();        // Ensure we have a default trainer
        $defaultTrainerUser = User::firstOrCreate([
            'email' => 'trainer@demo.test'
        ], [
            'name' => 'Demo Trainer',
            'pekerjaan' => 'Senior Instructor',
            'avatar' => null,
            'password' => bcrypt('password123'),
        ]);        // Assign trainer role if not already assigned
        if (!$defaultTrainerUser->hasRole('trainer')) {
            $defaultTrainerUser->assignRole('trainer');
        }

        $defaultTrainer = Trainer::firstOrCreate([
            'user_id' => $defaultTrainerUser->id
        ], [
            'is_active' => true
        ]);        // Ensure we have course levels
        $beginnerLevel = CourseLevel::firstOrCreate(['name' => 'beginner']);
        $intermediateLevel = CourseLevel::firstOrCreate(['name' => 'intermediate']);
        $advancedLevel = CourseLevel::firstOrCreate(['name' => 'advanced']);

        // Ensure we have a default course mode (check if CourseMode exists)
        $onlineMode = null;
        if (class_exists('\App\Models\CourseMode')) {
            $onlineMode = \App\Models\CourseMode::firstOrCreate(['name' => 'Online']);
        }// Ensure we have categories
        $webDevCategory = Category::firstOrCreate([
            'name' => 'Web Development'
        ], [
            'slug' => 'web-development',
            'icon' => 'fas fa-code'
        ]);

        $programmingCategory = Category::firstOrCreate([
            'name' => 'Programming'
        ], [
            'slug' => 'programming',
            'icon' => 'fas fa-laptop-code'
        ]);        // Create sample courses
        $courses = [
            [
                'name' => 'Complete Web Development Bootcamp',
                'slug' => 'complete-web-development-bootcamp',
                'about' => 'Master full-stack web development from HTML/CSS to advanced frameworks',
                'category_id' => $webDevCategory->id,
                'course_level_id' => $intermediateLevel->id,
                'price' => 299.99,
                'skills' => ['HTML', 'CSS', 'JavaScript', 'Bootstrap', 'Git', 'Web Design']
            ],
            [
                'name' => 'Advanced JavaScript Programming',
                'slug' => 'advanced-javascript-programming',
                'about' => 'Deep dive into modern JavaScript ES6+, async programming, and design patterns',
                'category_id' => $programmingCategory->id,
                'course_level_id' => $advancedLevel->id,
                'price' => 199.99,
                'skills' => ['JavaScript', 'ES6+', 'Async Programming', 'Design Patterns', 'DOM Manipulation']
            ],
            [
                'name' => 'Laravel Framework Mastery',
                'slug' => 'laravel-framework-mastery',
                'about' => 'Build modern web applications with Laravel PHP framework',
                'category_id' => $webDevCategory->id,
                'course_level_id' => $advancedLevel->id,
                'price' => 249.99,
                'skills' => ['PHP', 'Laravel', 'MVC Architecture', 'Database Design', 'API Development']
            ],
            [
                'name' => 'React.js Frontend Development',
                'slug' => 'react-js-frontend-development',
                'about' => 'Create interactive user interfaces with React.js and modern tooling',
                'category_id' => $webDevCategory->id,
                'course_level_id' => $intermediateLevel->id,
                'price' => 179.99,
                'skills' => ['React.js', 'JSX', 'Component Architecture', 'State Management', 'Frontend Development']
            ]
        ];

        foreach ($courses as $courseData) {
            $createData = [
                'slug' => $courseData['slug'],
                'about' => $courseData['about'],
                'category_id' => $courseData['category_id'],
                'course_level_id' => $courseData['course_level_id'],
                'price' => $courseData['price'],
                'path_trailer' => '/videos/trailers/' . $courseData['slug'] . '.mp4',
                'thumbnail' => '/images/courses/' . $courseData['slug'] . '.jpg',
                'trainer_id' => $defaultTrainer->id,
                'created_at' => $faker->dateTimeBetween('-1 year', '-6 months'),
            ];

            // Add course_mode_id if it exists
            if ($onlineMode) {
                $createData['course_mode_id'] = $onlineMode->id;
            }

            Course::firstOrCreate([
                'name' => $courseData['name']
            ], $createData);
        }

        $this->command->info('   ✓ Sample courses ensured');
    }

    /**
     * Generate a bio based on learning achievements
     */
    private function generateLearningBasedBio(string $name, int $courseCount): string
    {
        return "Junior developer passionate about continuous learning. Recently completed {$courseCount} comprehensive courses in web development and programming. Looking to apply newly acquired skills in real-world projects and grow as a professional developer. Eager to contribute to innovative teams and tackle challenging technical problems.";
    }
}
