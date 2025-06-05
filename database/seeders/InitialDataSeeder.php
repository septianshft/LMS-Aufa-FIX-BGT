<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds for Academy LMS.
     */
    public function run(): void
    {
        // Create admin user if not exists
        if (!DB::table('users')->where('email', 'admin@academylms.com')->exists()) {
            DB::table('users')->insert([
                'name' => 'Admin User',
                'email' => 'admin@academylms.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 1,
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // Create instructor user if not exists
        if (!DB::table('users')->where('email', 'instructor@academylms.com')->exists()) {
            DB::table('users')->insert([
                'name' => 'John Instructor',
                'email' => 'instructor@academylms.com',
                'password' => Hash::make('password123'),
                'role' => 'instructor',
                'status' => 1,
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // Create student user if not exists
        if (!DB::table('users')->where('email', 'student@academylms.com')->exists()) {
            DB::table('users')->insert([
                'name' => 'Jane Student',
                'email' => 'student@academylms.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'status' => 1,
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // Basic system settings
        $settings = [
            ['type' => 'system_name', 'description' => 'Academy LMS'],
            ['type' => 'system_title', 'description' => 'Academy LMS - Learning Management System'],
            ['type' => 'system_email', 'description' => 'admin@academylms.com'],
        ];

        foreach ($settings as $setting) {
            if (!DB::table('settings')->where('type', $setting['type'])->exists()) {
                DB::table('settings')->insert(array_merge($setting, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]));
            }
        }

        // Frontend settings
        $frontend_settings = [
            ['key' => 'theme', 'value' => 'default'],
            ['key' => 'banner_title', 'value' => 'Welcome to Academy LMS'],
            ['key' => 'banner_sub_title', 'value' => 'Start learning with our comprehensive course catalog'],
            ['key' => 'website_description', 'value' => 'Academy LMS is a comprehensive learning management system designed to help educators create and manage online courses effectively.'],
            ['key' => 'website_keywords', 'value' => 'LMS, Learning Management System, Online Courses, Education, E-learning'],
        ];

        foreach ($frontend_settings as $setting) {
            if (!DB::table('frontend_settings')->where('key', $setting['key'])->exists()) {
                DB::table('frontend_settings')->insert(array_merge($setting, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]));
            }
        }

        // Create sample categories
        $categories = [
            ['parent_id' => 0, 'title' => 'Web Development', 'slug' => 'web-development', 'status' => 1, 'sort' => 1],
            ['parent_id' => 0, 'title' => 'Mobile Development', 'slug' => 'mobile-development', 'status' => 1, 'sort' => 2],
            ['parent_id' => 0, 'title' => 'Data Science', 'slug' => 'data-science', 'status' => 1, 'sort' => 3],
            ['parent_id' => 0, 'title' => 'Design', 'slug' => 'design', 'status' => 1, 'sort' => 4],
        ];

        foreach ($categories as $category) {
            if (!DB::table('categories')->where('slug', $category['slug'])->exists()) {
                DB::table('categories')->insert(array_merge($category, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]));
            }
        }

        // Create default page builder entry
        if (!DB::table('builder_pages')->where('identifier', 'default')->exists()) {
            DB::table('builder_pages')->insert([
                'name' => 'Default Homepage',
                'identifier' => 'default',
                'status' => 1,
                'is_permanent' => 0,
                'html' => json_encode([]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // Create sample blog entries
        $blogs = [
            [
                'user_id' => 1,
                'title' => 'Welcome to Academy LMS',
                'slug' => 'welcome-to-academy-lms',
                'description' => 'Learn about the features and capabilities of Academy LMS.',
                'blog_content' => '<p>Welcome to Academy LMS, your comprehensive learning management system. Our platform offers a wide range of features to help educators create engaging online courses and manage their student communities effectively.</p><p>Whether you are an instructor looking to share your knowledge or a student eager to learn new skills, Academy LMS provides all the tools you need for a successful online learning experience.</p>',
                'banner' => '',
                'keywords' => 'Academy LMS, Learning Management System, Online Education',
                'is_popular' => 1,
                'status' => 1,
            ],
            [
                'user_id' => 1,
                'title' => 'Getting Started with Online Learning',
                'slug' => 'getting-started-with-online-learning',
                'description' => 'Tips and best practices for effective online learning.',
                'blog_content' => '<p>Online learning has become increasingly important in today\'s educational landscape. Here are some tips to help you get the most out of your online learning experience:</p><ul><li>Set up a dedicated study space</li><li>Create a consistent schedule</li><li>Actively participate in discussions</li><li>Take advantage of multimedia resources</li><li>Connect with fellow learners</li></ul>',
                'banner' => '',
                'keywords' => 'Online Learning, Study Tips, E-learning',
                'is_popular' => 0,
                'status' => 1,
            ],
            [
                'user_id' => 1,
                'title' => 'The Future of Digital Education',
                'slug' => 'future-of-digital-education',
                'description' => 'Exploring trends and innovations in digital learning.',
                'blog_content' => '<p>Digital education continues to evolve with new technologies and methodologies. From AI-powered personalized learning to virtual reality classrooms, the future of education is exciting and full of possibilities.</p><p>Academy LMS stays at the forefront of these innovations, providing cutting-edge tools for modern education.</p>',
                'banner' => '',
                'keywords' => 'Digital Education, Future Learning, Educational Technology',
                'is_popular' => 1,
                'status' => 1,
            ],
        ];

        foreach ($blogs as $blog) {
            if (!DB::table('blogs')->where('slug', $blog['slug'])->exists()) {
                DB::table('blogs')->insert(array_merge($blog, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]));
            }
        }

        echo "✅ Initial data seeded successfully!\n";
        echo "   - Admin user: admin@academylms.com (password: password123)\n";
        echo "   - Instructor user: instructor@academylms.com (password: password123)\n";
        echo "   - Student user: student@academylms.com (password: password123)\n";
        echo "   - Basic system settings configured\n";
        echo "   - Sample categories created\n";
        echo "   - Sample blog posts added\n";
    }
}
