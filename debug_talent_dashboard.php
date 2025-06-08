<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\TalentRequest;
use App\Models\Recruiter;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 TALENT DASHBOARD TESTING - PENDING REQUESTS CHECK\n";
echo "=====================================================\n\n";

try {
    // Find talent users
    $talents = User::whereHas('roles', function($query) {
        $query->where('name', 'talent');
    })->get();

    echo "📋 AVAILABLE TALENT USERS:\n";
    foreach ($talents as $talent) {
        echo "- {$talent->name} ({$talent->email}) - ID: {$talent->id}\n";

        // Check pending requests for this talent
        $pendingRequests = TalentRequest::where('talent_user_id', $talent->id)
            ->where('status', 'pending')
            ->with(['recruiter.user'])
            ->get();

        echo "  Pending Requests: {$pendingRequests->count()}\n";

        if ($pendingRequests->count() > 0) {
            foreach ($pendingRequests as $request) {
                echo "    - Request #{$request->id} from {$request->recruiter->user->name}\n";
            }
        }
        echo "\n";
    }

    // Let's focus on the main talent user for testing
    $testTalent = User::where('email', 'talent@test.com')->first();

    if (!$testTalent) {
        echo "❌ No talent@test.com user found. Let's check what users exist:\n";
        $allTalents = User::whereHas('roles', function($query) {
            $query->where('name', 'talent');
        })->take(5)->get();

        foreach ($allTalents as $talent) {
            echo "- {$talent->name} ({$talent->email})\n";
        }

        // Use the first available talent
        $testTalent = $allTalents->first();
    }

    if ($testTalent) {
        echo "🎯 TESTING WITH TALENT: {$testTalent->name} ({$testTalent->email})\n";
        echo "========================================\n\n";

        // Check current pending requests
        $currentPending = TalentRequest::where('talent_user_id', $testTalent->id)
            ->where('status', 'pending')
            ->with(['recruiter.user'])
            ->get();

        echo "Current Pending Requests: {$currentPending->count()}\n\n";

        if ($currentPending->count() == 0) {
            echo "⚠️  NO PENDING REQUESTS FOUND! This is why you don't see the View button.\n";
            echo "Let's create a test request...\n\n";

            // Find or create a recruiter
            $recruiter = Recruiter::with('user')->first();
            if (!$recruiter) {
                echo "Creating test recruiter...\n";
                $recruiterUser = User::create([
                    'name' => 'Test Recruiter',
                    'email' => 'test.recruiter.demo@test.com',
                    'password' => bcrypt('password123')
                ]);
                $recruiterUser->assignRole('recruiter');

                $recruiter = Recruiter::create([
                    'user_id' => $recruiterUser->id,
                    'company_name' => 'Demo Tech Company',
                    'company_description' => 'Leading technology company looking for talents'
                ]);
            }

            // Create test talent request
            $testRequest = TalentRequest::create([
                'recruiter_id' => $recruiter->id,
                'talent_user_id' => $testTalent->id,
                'talent_id' => $testTalent->id, // Add this if the table requires it
                'request_message' => 'We are very interested in hiring you for a senior developer position at our company. Your skills in Laravel and PHP development are exactly what we need for our upcoming project. We offer competitive salary, flexible working hours, and great benefits. Would you be interested in discussing this opportunity further?',
                'status' => 'pending'
            ]);

            echo "✅ Created test talent request (ID: {$testRequest->id})\n";
            echo "From: {$recruiter->user->name} ({$recruiter->company_name})\n";
            echo "To: {$testTalent->name}\n";
            echo "Message: " . substr($testRequest->request_message, 0, 100) . "...\n\n";

        } else {
            echo "✅ PENDING REQUESTS FOUND:\n";
            foreach ($currentPending as $request) {
                echo "Request #{$request->id}:\n";
                echo "  From: {$request->recruiter->user->name}\n";
                echo "  Message: " . substr($request->request_message, 0, 100) . "...\n";
                echo "  Date: {$request->created_at->format('M d, Y')}\n\n";
            }
        }

        echo "🌐 ACCESS INFORMATION:\n";
        echo "Login URL: http://127.0.0.1:8000/login\n";
        echo "Talent Email: {$testTalent->email}\n";
        echo "Password: password123\n";
        echo "Dashboard URL: http://127.0.0.1:8000/talent/dashboard\n\n";

        echo "🎯 WHAT YOU SHOULD SEE AFTER LOGIN:\n";
        echo "1. Blue notification alert: 'X new talent requests pending your review!'\n";
        echo "2. Pending Requests card showing count > 0\n";
        echo "3. 'Pending Talent Requests' table with requests\n";
        echo "4. Each row should have Accept, Decline, and VIEW buttons\n";
        echo "5. Click VIEW to see detailed modal with full offer\n\n";

    } else {
        echo "❌ No talent users found in the system!\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    if (strpos($e->getMessage(), 'talent_id') !== false) {
        echo "\n💡 Database schema issue detected. Let me fix the talent request creation...\n";

        // Try without talent_id field
        try {
            $testRequest = TalentRequest::create([
                'recruiter_id' => $recruiter->id,
                'talent_user_id' => $testTalent->id,
                'request_message' => 'We are interested in hiring you for a developer position. Your skills match our requirements perfectly!',
                'status' => 'pending'
            ]);
            echo "✅ Created talent request without talent_id field (ID: {$testRequest->id})\n";
        } catch (Exception $e2) {
            echo "❌ Still failed: " . $e2->getMessage() . "\n";
        }
    }
}

echo "\n🚀 SOLUTION:\n";
echo "If you still don't see the View button, it means there are no pending requests.\n";
echo "Run this script to create test data, then refresh the talent dashboard.\n";
echo "===================================================================\n";
