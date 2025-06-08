<?php
// Comprehensive system test after fixing the talent admin dashboard
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TalentRequest;
use App\Models\User;
use App\Models\Recruiter;
use App\Models\Talent;

echo "=== COMPREHENSIVE SYSTEM TEST ===" . PHP_EOL;
echo "Testing after fixing talent admin dashboard..." . PHP_EOL . PHP_EOL;

try {
    // 1. Test User System
    echo "1. TESTING USER SYSTEM" . PHP_EOL;
    echo "=====================" . PHP_EOL;

    $users = User::with('roles')->get();
    $talentUsers = User::where('available_for_scouting', true)->get();
    $adminUsers = User::role('talent_admin')->get();

    echo "Total users: " . $users->count() . PHP_EOL;
    echo "Users available for scouting: " . $talentUsers->count() . PHP_EOL;
    echo "Talent admin users: " . $adminUsers->count() . PHP_EOL;

    foreach ($adminUsers as $admin) {
        echo "  - " . $admin->name . " (" . $admin->email . ")" . PHP_EOL;
    }
    echo PHP_EOL;

    // 2. Test Talent Request System
    echo "2. TESTING TALENT REQUEST SYSTEM" . PHP_EOL;
    echo "================================" . PHP_EOL;

    $totalRequests = TalentRequest::count();
    $requestsWithUsers = TalentRequest::whereNotNull('talent_user_id')->count();
    $requestsWithRecruiters = TalentRequest::whereNotNull('recruiter_id')->count();
    $pendingRequests = TalentRequest::where('status', 'pending')->count();
    $approvedRequests = TalentRequest::where('status', 'approved')->count();

    echo "Total talent requests: $totalRequests" . PHP_EOL;
    echo "Requests with user references: $requestsWithUsers" . PHP_EOL;
    echo "Requests with recruiter references: $requestsWithRecruiters" . PHP_EOL;
    echo "Pending requests: $pendingRequests" . PHP_EOL;
    echo "Approved requests: $approvedRequests" . PHP_EOL;
    echo PHP_EOL;

    // 3. Test Relationships
    echo "3. TESTING RELATIONSHIPS" . PHP_EOL;
    echo "========================" . PHP_EOL;

    $sampleRequests = TalentRequest::with(['user', 'recruiter', 'talent'])
        ->limit(3)
        ->get();

    foreach ($sampleRequests as $request) {
        echo "Request #" . $request->id . ": " . ($request->project_title ?? 'No title') . PHP_EOL;
        echo "  User: " . ($request->user ? $request->user->name . " (" . $request->user->email . ")" : "❌ No user") . PHP_EOL;
        echo "  Recruiter: " . ($request->recruiter ? $request->recruiter->company_name : "❌ No recruiter") . PHP_EOL;
        echo "  Talent: " . ($request->talent ? "✓ Linked" : "❌ No talent record") . PHP_EOL;
        echo "  Status: " . $request->status . PHP_EOL;
        echo "---" . PHP_EOL;
    }

    // 4. Test Skills System
    echo "4. TESTING SKILLS SYSTEM" . PHP_EOL;
    echo "========================" . PHP_EOL;

    $usersWithSkills = User::whereNotNull('talent_skills')
        ->where('talent_skills', '!=', '[]')
        ->where('talent_skills', '!=', 'null')
        ->get();

    echo "Users with skills: " . $usersWithSkills->count() . PHP_EOL;
      foreach ($usersWithSkills as $user) {
        $skills = is_string($user->talent_skills) ? json_decode($user->talent_skills, true) : $user->talent_skills;
        $skills = $skills ?? [];
        echo "  - " . $user->name . ": " . count($skills) . " skills" . PHP_EOL;
        foreach ($skills as $skill) {
            if (is_array($skill) && isset($skill['name'])) {
                echo "    * " . $skill['name'] . " (" . ($skill['level'] ?? 'unknown') . ")" . PHP_EOL;
            }
        }
    }
    echo PHP_EOL;

    // 5. Test Recruiter System
    echo "5. TESTING RECRUITER SYSTEM" . PHP_EOL;
    echo "===========================" . PHP_EOL;

    $recruiters = Recruiter::with('user')->get();
    echo "Total recruiters: " . $recruiters->count() . PHP_EOL;

    foreach ($recruiters as $recruiter) {
        echo "  - " . $recruiter->company_name . " (" . $recruiter->position . ")" . PHP_EOL;
        echo "    User: " . ($recruiter->user ? $recruiter->user->name : "No user") . PHP_EOL;
        echo "    Status: " . ($recruiter->is_active ? "Active" : "Inactive") . PHP_EOL;
        echo "    Verification: " . $recruiter->verification_status . PHP_EOL;
    }
    echo PHP_EOL;

    // 6. Test Dashboard Data
    echo "6. TESTING DASHBOARD DATA" . PHP_EOL;
    echo "=========================" . PHP_EOL;

    // Simulate the dashboard controller logic
    $activeTalents = User::where('available_for_scouting', true)
        ->where('is_active_talent', true)
        ->count();

    $availableTalents = User::where('available_for_scouting', true)
        ->whereNotIn('id', function($query) {
            $query->select('talent_user_id')
                  ->from('talent_requests')
                  ->whereIn('status', ['approved', 'meeting_arranged', 'onboarded'])
                  ->whereNotNull('talent_user_id');
        })->count();

    $activeRecruiters = Recruiter::where('is_active', true)->count();

    echo "Active talents: $activeTalents" . PHP_EOL;
    echo "Available talents: $availableTalents" . PHP_EOL;
    echo "Active recruiters: $activeRecruiters" . PHP_EOL;
    echo PHP_EOL;

    // 7. Test Recent Activity
    echo "7. TESTING RECENT ACTIVITY" . PHP_EOL;
    echo "==========================" . PHP_EOL;

    $recentRequests = TalentRequest::with(['user', 'recruiter'])
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();

    echo "Recent talent requests:" . PHP_EOL;
    foreach ($recentRequests as $request) {
        echo "  - " . ($request->project_title ?? 'No title') . " (" . $request->status . ")" . PHP_EOL;
        echo "    Created: " . $request->created_at->format('M j, Y H:i') . PHP_EOL;
    }
    echo PHP_EOL;

    // 8. System Health Check
    echo "8. SYSTEM HEALTH CHECK" . PHP_EOL;
    echo "======================" . PHP_EOL;

    $issues = [];

    // Check for requests without users
    $requestsWithoutUsers = TalentRequest::whereNull('talent_user_id')->count();
    if ($requestsWithoutUsers > 0) {
        $issues[] = "$requestsWithoutUsers talent requests without user references";
    }

    // Check for requests without recruiters
    $requestsWithoutRecruiters = TalentRequest::whereNull('recruiter_id')->count();
    if ($requestsWithoutRecruiters > 0) {
        $issues[] = "$requestsWithoutRecruiters talent requests without recruiter references";
    }

    // Check for users without proper talent data
    $usersAvailableButNoSkills = User::where('available_for_scouting', true)
        ->where(function($query) {
            $query->whereNull('talent_skills')
                  ->orWhere('talent_skills', '[]')
                  ->orWhere('talent_skills', 'null');
        })->count();

    if ($usersAvailableButNoSkills > 0) {
        $issues[] = "$usersAvailableButNoSkills users available for scouting but without skills";
    }

    if (empty($issues)) {
        echo "✅ System is healthy! No issues detected." . PHP_EOL;
    } else {
        echo "⚠️ Issues detected:" . PHP_EOL;
        foreach ($issues as $issue) {
            echo "  - $issue" . PHP_EOL;
        }
    }
    echo PHP_EOL;

    // 9. Login Test URLs
    echo "9. LOGIN TEST INFORMATION" . PHP_EOL;
    echo "=========================" . PHP_EOL;
    echo "🌐 Development server should be running at: http://127.0.0.1:8000" . PHP_EOL;
    echo PHP_EOL;
    echo "Test accounts:" . PHP_EOL;
    echo "1. Talent Admin: talent_admin@test.com / password123" . PHP_EOL;
    echo "   - Should access: /talent-admin/dashboard" . PHP_EOL;
    echo PHP_EOL;
    echo "2. Recruiter: recruiter@test.com / password123" . PHP_EOL;
    echo "   - Should access: /talent/discovery" . PHP_EOL;
    echo PHP_EOL;
    echo "3. Trainee (with skills): trainee@test.com / password123" . PHP_EOL;
    echo "   - Should access: /dashboard" . PHP_EOL;
    echo "   - Can toggle talent availability in profile" . PHP_EOL;
    echo PHP_EOL;

    echo "=== TEST COMPLETED SUCCESSFULLY ===" . PHP_EOL;
    echo "The system is ready for comprehensive testing!" . PHP_EOL;

} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . PHP_EOL;
}
