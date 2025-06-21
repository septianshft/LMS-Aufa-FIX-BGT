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
     * Seed 3 trainee users with completed LMS courses for testing.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $this->command->info('🎓 Creating trainees with LMS completion data...');

        // ===============================================
        // CREATE SAMPLE COURSES IF NEEDED
        // ===============================================
        $this->createSampleCourses();

        // ===============================================
        // TRAINEE PROFILES
        // ===============================================
        $traineeProfiles = [
            [
                'name' => 'Alex Student',
                'email' => 'trainee@test.com',
                'pekerjaan' => 'Computer Science Student',
                'courses' => ['Complete Web Development Bootcamp', 'Advanced JavaScript Programming'],
                'available_for_scouting' => true,
            ],
            [
                'name' => 'Brenda Learner',
                'email' => 'trainee2@test.com',
                'pekerjaan' => 'Aspiring Data Analyst',
                'courses' => ['Laravel Framework Mastery', 'React.js Frontend Development'],
                'available_for_scouting' => true,
            ],
            [
                'name' => 'Charles Coder',
                'email' => 'trainee3@test.com',
                'pekerjaan' => 'Software Engineering Intern',
                'courses' => ['Complete Web Development Bootcamp', 'React.js Frontend Development'],
                'available_for_scouting' => false, // Example of one not yet available
            ]
        ];

        foreach ($traineeProfiles as $traineeProfile) {
            $this->command->info("   📚 Creating trainee: {$traineeProfile['name']}");

            // Create trainee user
            $traineeUser = User::firstOrCreate([
                'email' => $traineeProfile['email']
            ], [
                'name' => $traineeProfile['name'],
                'pekerjaan' => $traineeProfile['pekerjaan'],
                'avatar' => null,
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
                'available_for_scouting' => $traineeProfile['available_for_scouting'],
                'is_active_talent' => false, // Will be enabled after conversion
            ]);

            // Assign trainee role if not already assigned
            if (!$traineeUser->hasRole('trainee')) {
                $traineeUser->assignRole('trainee');
            }

            // Get courses for this trainee
            $courses = Course::whereIn('name', $traineeProfile['courses'])->get();

            // Complete courses for this trainee
            foreach ($courses as $course) {
                // Enroll trainee
                $enrollment = CourseTrainee::firstOrCreate([
                    'user_id' => $traineeUser->id,
                    'course_id' => $course->id
                ]);

                // Mark course as completed
                CourseProgress::firstOrCreate([
                    'user_id' => $traineeUser->id,
                    'course_id' => $course->id
                ], [
                    'progress' => 100,
                    'quiz_passed' => true,
                    'completed_videos' => [] // Empty array since no specific videos defined
                ]);

                // Create certificate
                Certificate::firstOrCreate([
                    'user_id' => $traineeUser->id,
                    'course_id' => $course->id
                ], [
                    'path' => '/certificates/' . uniqid() . '.pdf',
                    'generated_at' => $faker->dateTimeBetween('-3 months', '-1 month')
                ]);

                // Create final quiz attempt if quiz exists
                $finalQuiz = FinalQuiz::where('course_id', $course->id)->first();
                if ($finalQuiz) {
                    QuizAttempt::firstOrCreate([
                        'user_id' => $traineeUser->id,
                        'final_quiz_id' => $finalQuiz->id
                    ], [
                        'score' => $faker->numberBetween(80, 100),
                        'is_passed' => true
                    ]);
                }

                // Manually trigger the skill generation after a course is "completed"
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

        $this->command->info('✅ Created all trainees with LMS completion data successfully!');
        $this->command->info('');
        $this->command->info('🧪 TEST SCENARIOS:');
        $this->command->info('   - Login with any of the 3 trainee accounts (password: password123)');
        $this->command->info('   - Two are available for scouting, one is not.');
        $this->command->info('   - All have completed courses and generated skills.');
    }

    /**
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

            $course = Course::firstOrCreate([
                'name' => $courseData['name']
            ], $createData);

            // Create a FinalQuiz for each course to enable quiz attempts
            if ($course && !$course->finalQuiz) {
                FinalQuiz::create([
                    'course_id' => $course->id,
                    'title' => 'Final Quiz: ' . $course->name,
                    'description' => 'Test your knowledge with the final quiz for ' . $course->name . '.',
                ]);
            }
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
