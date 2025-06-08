<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseLevel;
use App\Models\Category;
use App\Models\FinalQuiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\QuizAttempt;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CourseCompletionTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have a trainer
        $trainerUser = User::firstOrCreate([
            'email' => 'trainer@test.com'
        ], [
            'name' => 'Test Trainer',
            'password' => bcrypt('password123'),
            'avatar' => 'images/default-avatar.png',
            'pekerjaan' => 'Instructor'
        ]);

        if (!$trainerUser->hasRole('trainer')) {
            $trainerUser->assignRole('trainer');
        }

        $trainer = \App\Models\Trainer::firstOrCreate([
            'user_id' => $trainerUser->id
        ], [
            'is_active' => true
        ]);

        // Ensure we have course levels
        $beginnerLevel = CourseLevel::firstOrCreate(['name' => 'Beginner']);
        $intermediateLevel = CourseLevel::firstOrCreate(['name' => 'Intermediate']);
        $advancedLevel = CourseLevel::firstOrCreate(['name' => 'Advanced']);

        // Ensure we have a category
        $category = Category::firstOrCreate([
            'name' => 'Programming',
            'slug' => 'programming'
        ], [
            'icon' => 'fa-code'
        ]);

        // Create test courses with different levels
        $courses = [
            [
                'name' => 'JavaScript Fundamentals',
                'slug' => 'javascript-fundamentals',
                'level' => $beginnerLevel,
                'description' => 'Learn the basics of JavaScript programming'
            ],
            [
                'name' => 'Advanced React Development',
                'slug' => 'advanced-react-development',
                'level' => $advancedLevel,
                'description' => 'Master advanced React concepts and patterns'
            ],
            [
                'name' => 'Python for Data Science',
                'slug' => 'python-for-data-science',
                'level' => $intermediateLevel,
                'description' => 'Use Python for data analysis and machine learning'
            ]
        ];

        foreach ($courses as $courseData) {
            $course = Course::firstOrCreate([
                'slug' => $courseData['slug']
            ], [
                'name' => $courseData['name'],
                'category_id' => $category->id,
                'course_level_id' => $courseData['level']->id,
                'about' => $courseData['description'],
                'path_trailer' => 'videos/default-trailer.mp4',
                'thumbnail' => 'images/default-cover.jpg',
                'trainer_id' => $trainer->id,
            ]);

            // Create quiz for the course if it doesn't exist
            $quiz = FinalQuiz::firstOrCreate([
                'course_id' => $course->id
            ], [
                'title' => "Final Quiz for {$course->name}",
                'description' => "Test your knowledge of {$course->name}",
                'passing_score' => 70,
            ]);

            // Create sample questions if they don't exist
            if ($quiz->questions()->count() === 0) {
                for ($i = 1; $i <= 3; $i++) {
                    $question = QuizQuestion::create([
                        'final_quiz_id' => $quiz->id,
                        'question' => "Sample question {$i} for {$course->name}?",
                    ]);

                    // Create options
                    QuizOption::create([
                        'quiz_question_id' => $question->id,
                        'option_text' => 'Correct answer',
                        'is_correct' => true
                    ]);

                    QuizOption::create([
                        'quiz_question_id' => $question->id,
                        'option_text' => 'Wrong answer 1',
                        'is_correct' => false
                    ]);

                    QuizOption::create([
                        'quiz_question_id' => $question->id,
                        'option_text' => 'Wrong answer 2',
                        'is_correct' => false
                    ]);
                }
            }
        }

        // Get a test trainee user
        $traineeUser = User::where('email', 'trainee@test.com')->first();
        if (!$traineeUser) {
            $traineeUser = User::create([
                'name' => 'Test Trainee',
                'email' => 'trainee@test.com',
                'password' => bcrypt('password123'),
                'avatar' => 'images/default-avatar.png',
                'pekerjaan' => 'Student'
            ]);
            $traineeUser->assignRole('trainee');
        }

        // Simulate course completions by creating passing quiz attempts
        $completedCourses = Course::whereIn('slug', ['javascript-fundamentals', 'python-for-data-science'])->get();

        foreach ($completedCourses as $course) {
            $quiz = $course->finalQuiz;
            if ($quiz) {
                // Check if attempt already exists
                $existingAttempt = QuizAttempt::where('final_quiz_id', $quiz->id)
                    ->where('user_id', $traineeUser->id)
                    ->first();

                if (!$existingAttempt) {
                    // Create a passing attempt
                    QuizAttempt::create([
                        'final_quiz_id' => $quiz->id,
                        'user_id' => $traineeUser->id,
                        'score' => 85, // Above passing score
                        'is_passed' => true,
                    ]);

                    // Manually trigger skill addition (since we're seeding)
                    $traineeUser->addSkillFromCourse($course);
                }
            }
        }

        $this->command->info('Course completion test data created successfully!');
        $this->command->info("Test user: {$traineeUser->email} (password: password123)");
        $this->command->info("Skills added: " . count($traineeUser->fresh()->talent_skills ?? []));
    }
}
