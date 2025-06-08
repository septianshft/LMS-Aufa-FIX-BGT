<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Talent;
use App\Models\TalentRequest;
use App\Models\Recruiter;
use Illuminate\Foundation\Application;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 COMPREHENSIVE VERIFICATION TEST\n";
echo "=====================================\n\n";

try {
    // Test 1: Verify user roles
    echo "1️⃣ Testing User Roles...\n";
    $adminCount = User::role('admin')->count();
    $talentAdminCount = User::role('talent_admin')->count();
    $talentUserCount = User::whereHas('talent')->count();
    $recruiterUserCount = User::role('recruiter')->count();

    echo "   ✅ Admins: {$adminCount}\n";
    echo "   ✅ Talent Admins: {$talentAdminCount}\n";
    echo "   ✅ Talent Users: {$talentUserCount}\n";
    echo "   ✅ Recruiter Users: {$recruiterUserCount}\n\n";

    // Test 2: Verify talent profiles
    echo "2️⃣ Testing Talent Profiles...\n";
    $talentProfiles = Talent::with('user')->count();
    $activeTalents = Talent::where('is_active', true)->count();
    $talentsWithSkills = Talent::whereHas('user', function($query) {
        $query->whereNotNull('talent_skills');
    })->count();

    echo "   ✅ Total Talent Profiles: {$talentProfiles}\n";
    echo "   ✅ Active Talents: {$activeTalents}\n";
    echo "   ✅ Talents with Skills: {$talentsWithSkills}\n\n";

    // Test 3: Verify talent requests
    echo "3️⃣ Testing Talent Request System...\n";
    $totalRequests = TalentRequest::count();
    $pendingRequests = TalentRequest::where('status', 'pending')->count();
    $approvedRequests = TalentRequest::where('status', 'approved')->count();
    $completedRequests = TalentRequest::where('status', 'completed')->count();
    $requestsWithTalentUserId = TalentRequest::whereNotNull('talent_user_id')->count();

    echo "   ✅ Total Requests: {$totalRequests}\n";
    echo "   ✅ Pending: {$pendingRequests}\n";
    echo "   ✅ Approved: {$approvedRequests}\n";
    echo "   ✅ Completed: {$completedRequests}\n";
    echo "   ✅ With talent_user_id: {$requestsWithTalentUserId}\n\n";

    // Test 4: Verify data relationships
    echo "4️⃣ Testing Data Relationships...\n";
    $requestsWithValidTalents = TalentRequest::whereHas('talent')->count();
    $requestsWithValidRecruiters = TalentRequest::whereHas('recruiter')->count();
    $talentsWithRequests = Talent::whereHas('talentRequests')->count();

    echo "   ✅ Requests with valid talents: {$requestsWithValidTalents}\n";
    echo "   ✅ Requests with valid recruiters: {$requestsWithValidRecruiters}\n";
    echo "   ✅ Talents with requests: {$talentsWithRequests}\n\n";

    // Test 5: Sample talent dashboard data
    echo "5️⃣ Sample Talent Dashboard Data...\n";
    $sampleTalent = Talent::with(['user', 'talentRequests'])->first();
    if ($sampleTalent) {
        echo "   ✅ Sample Talent: {$sampleTalent->user->name}\n";
        echo "   ✅ Email: {$sampleTalent->user->email}\n";
        echo "   ✅ Active: " . ($sampleTalent->is_active ? 'Yes' : 'No') . "\n";
        echo "   ✅ Requests Count: " . $sampleTalent->talentRequests->count() . "\n";

        if ($sampleTalent->talentRequests->count() > 0) {
            $latestRequest = $sampleTalent->talentRequests->first();
            echo "   ✅ Latest Request Status: {$latestRequest->status}\n";
            echo "   ✅ Latest Request Project: {$latestRequest->project_title}\n";
        }
    }
    echo "\n";

    // Test 6: Verify key test accounts
    echo "6️⃣ Testing Key Test Accounts...\n";
    $testAccounts = [
        'admin@admin.com' => 'Admin',
        'talentadmin@test.com' => 'Talent Admin',
        'talent@test.com' => 'Talent User',
        'recruiter@test.com' => 'Recruiter',
        'trainee@test.com' => 'LMS Trainee',
        'demo.trainee@test.com' => 'Demo Trainee'
    ];

    foreach ($testAccounts as $email => $role) {
        $user = User::where('email', $email)->first();
        if ($user) {
            echo "   ✅ {$role}: {$email} (ID: {$user->id})\n";
        } else {
            echo "   ❌ {$role}: {$email} - NOT FOUND\n";
        }
    }

    echo "\n🎯 VERIFICATION COMPLETE!\n";
    echo "=====================================\n";
    echo "🌐 Ready for comprehensive flow testing!\n";
    echo "🔗 Access the application at: http://127.0.0.1:8000\n\n";

    // Recommendations
    echo "📋 TESTING RECOMMENDATIONS:\n";
    echo "1. Test talent dashboard login with: talent@test.com / password123\n";
    echo "2. Test recruiter dashboard with: recruiter@test.com / password123\n";
    echo "3. Test admin panel with: talentadmin@test.com / password123\n";
    echo "4. Verify talent request notifications and status updates\n";
    echo "5. Test trainee-to-talent conversion flow\n";
    echo "6. Verify search and discovery features\n\n";

} catch (Exception $e) {
    echo "❌ Error during verification: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
