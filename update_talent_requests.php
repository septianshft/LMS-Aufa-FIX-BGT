<?php
// Update existing talent requests and create test data
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TalentRequest;
use App\Models\User;
use App\Models\Recruiter;

echo "=== Updating Talent Requests ===" . PHP_EOL;

try {    // Get users with talent data
    $talentUsers = User::where('available_for_scouting', true)->get();
    $recruiterUser = User::where('email', 'recruiter@test.com')->first();

    echo "Found " . $talentUsers->count() . " talent users and " . ($recruiterUser ? "1" : "0") . " recruiter user." . PHP_EOL;

    // Get or create recruiter record
    $recruiter = null;
    if ($recruiterUser) {
        $recruiter = Recruiter::where('user_id', $recruiterUser->id)->first();
        if (!$recruiter) {
            $recruiter = Recruiter::create([
                'user_id' => $recruiterUser->id,
                'company_name' => 'TechCorp Solutions',
                'position' => 'Senior Talent Acquisition Manager',
                'company_description' => 'Leading technology solutions provider',
                'phone' => '+1-555-0123',
                'linkedin_profile' => 'https://linkedin.com/in/jane-recruiter',
                'verification_status' => 'verified',
                'is_active' => true
            ]);
            echo "Created recruiter record for " . $recruiterUser->name . PHP_EOL;
        }
    }

    // Update existing talent requests
    $existingRequests = TalentRequest::whereNull('talent_user_id')->get();
    $updatedCount = 0;

    foreach ($existingRequests as $request) {
        if ($talentUsers->isNotEmpty()) {
            $randomTalent = $talentUsers->random();
            $request->update(['talent_user_id' => $randomTalent->id]);
            $updatedCount++;
            echo "Updated request #" . $request->id . " with user: " . $randomTalent->name . PHP_EOL;
        }
    }

    echo "Updated $updatedCount existing requests." . PHP_EOL . PHP_EOL;
      // Create some new test talent requests if we have both recruiter and talent users
    if ($recruiter && $talentUsers->isNotEmpty()) {
        // First, let's create or get some talent records for the users
        foreach ($talentUsers as $user) {
            $talent = \App\Models\Talent::where('user_id', $user->id)->first();
            if (!$talent) {
                $talent = \App\Models\Talent::create([
                    'user_id' => $user->id,
                    'skills' => json_encode($user->talent_skills ?? []),
                    'hourly_rate' => $user->hourly_rate ?? 50,
                    'bio' => $user->talent_bio ?? 'Experienced professional with diverse skills.',
                    'portfolio_url' => $user->portfolio_url ?? '',
                    'location' => $user->location ?? 'Remote',
                    'phone' => $user->phone ?? '',
                    'experience_level' => $user->experience_level ?? 'intermediate',
                    'availability_status' => 'available',
                    'is_active' => true
                ]);
                echo "Created talent record for " . $user->name . PHP_EOL;
            }
        }

        $testRequests = [
            [
                'project_title' => 'Full-Stack Web Developer for E-commerce Platform',
                'project_description' => 'Seeking an experienced full-stack developer to build a modern e-commerce platform using Laravel and Vue.js.',
                'requirements' => 'PHP, Laravel, Vue.js, MySQL, 3+ years experience',
                'budget_range' => '$5000-8000',
                'project_duration' => '3-4 months',
                'urgency_level' => 'medium',
                'status' => 'pending',
                'recruiter_message' => 'We are looking for a talented developer to join our team on this exciting project.'
            ],
            [
                'project_title' => 'Python Data Scientist for AI Project',
                'project_description' => 'Looking for a data scientist to develop machine learning models for predictive analytics.',
                'requirements' => 'Python, TensorFlow, pandas, scikit-learn, 2+ years experience',
                'budget_range' => '$6000-10000',
                'project_duration' => '4-6 months',
                'urgency_level' => 'high',
                'status' => 'approved',
                'recruiter_message' => 'Exciting opportunity to work on cutting-edge AI technology.',
                'admin_notes' => 'Approved - good match for our Python talents'
            ],
            [
                'project_title' => 'Mobile App Developer for Health Platform',
                'project_description' => 'Develop a cross-platform mobile application for health monitoring and fitness tracking.',
                'requirements' => 'React Native, JavaScript, mobile app development, UI/UX design',
                'budget_range' => '$4000-7000',
                'project_duration' => '2-3 months',
                'urgency_level' => 'low',
                'status' => 'meeting_arranged',
                'recruiter_message' => 'Health tech startup looking for innovative mobile developer.',
                'meeting_arranged_at' => now()
            ]
        ];

        foreach ($testRequests as $index => $requestData) {
            $randomTalent = $talentUsers->random();
            $talent = \App\Models\Talent::where('user_id', $randomTalent->id)->first();

            $request = TalentRequest::create(array_merge($requestData, [
                'recruiter_id' => $recruiter->id,
                'talent_id' => $talent->id,
                'talent_user_id' => $randomTalent->id
            ]));

            echo "Created request #" . $request->id . ": " . $requestData['project_title'] .
                 " (Talent: " . $randomTalent->name . ", Status: " . $requestData['status'] . ")" . PHP_EOL;
        }
    }

    echo PHP_EOL . "=== Final Status ===" . PHP_EOL;
    $totalRequests = TalentRequest::count();
    $requestsWithUsers = TalentRequest::whereNotNull('talent_user_id')->count();

    echo "Total talent requests: $totalRequests" . PHP_EOL;
    echo "Requests with user references: $requestsWithUsers" . PHP_EOL;

    // Test the relationship again
    echo PHP_EOL . "=== Testing Relationships ===" . PHP_EOL;
    $requests = TalentRequest::with(['user', 'recruiter'])->limit(5)->get();

    foreach ($requests as $request) {
        echo "Request #" . $request->id . ": " . ($request->project_title ?? 'No title') . PHP_EOL;
        echo "  User: " . ($request->user ? $request->user->name . " (" . $request->user->email . ")" : "No user") . PHP_EOL;
        echo "  Recruiter: " . ($request->recruiter ? $request->recruiter->company_name : "No recruiter") . PHP_EOL;
        echo "  Status: " . $request->status . PHP_EOL;
        echo "---" . PHP_EOL;
    }

    echo PHP_EOL . "Update completed successfully!" . PHP_EOL;

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . PHP_EOL;
}
