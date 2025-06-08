<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Trainer;
use App\Models\CourseLevel;
use App\Models\CourseMode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class TalentIntegrationTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test trainee user with some completed courses
        $trainee = User::firstOrCreate([
            'email' => 'demo.trainee@test.com'
        ], [
            'name' => 'Demo Trainee',
            'pekerjaan' => 'Student',
            'avatar' => 'images/default-avatar.png',
            'password' => bcrypt('password123'),
        ]);

        $traineeRole = Role::where('name', 'trainee')->first();
        $trainee->assignRole($traineeRole);

        // Add some skills manually to simulate course completion
        $skills = [
            [
                'name' => 'Laravel Development',
                'level' => 'Intermediate',
                'acquired_from' => 'course_completion',
                'course_id' => 1,
                'acquired_at' => now()->subDays(30)->toDateString(),
            ],
            [
                'name' => 'JavaScript Fundamentals',
                'level' => 'Beginner',
                'acquired_from' => 'course_completion',
                'course_id' => 2,
                'acquired_at' => now()->subDays(15)->toDateString(),
            ],
            [
                'name' => 'Database Design',
                'level' => 'Advanced',
                'acquired_from' => 'course_completion',
                'course_id' => 3,
                'acquired_at' => now()->subDays(7)->toDateString(),
            ],
        ];

        $trainee->update([
            'talent_skills' => $skills,
            'available_for_scouting' => true,
            'hourly_rate' => 25.00,
            'talent_bio' => 'Passionate developer with experience in web development. Completed multiple courses and looking for exciting projects to work on.',
            'location' => 'Remote',
            'experience_level' => 'intermediate',
            'is_active_talent' => true,
        ]);

        // Assign talent role and create talent record
        if (!$trainee->hasRole('talent')) {
            $trainee->assignRole('talent');
        }

        \App\Models\Talent::firstOrCreate([
            'user_id' => $trainee->id
        ], [
            'is_active' => true
        ]);

        $this->command->info('Demo trainee with talent skills created: demo.trainee@test.com');
    }
}
