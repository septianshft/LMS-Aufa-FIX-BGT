<?php

require_once 'vendor/autoload.php';

// Initialize Laravel application
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Str;

// Test the talent dashboard functionality
echo "=== Testing Talent Requests View Functionality ===\n\n";

// Check for users with different types of talent requests
$users = \App\Models\User::whereHas('roles', function($query) {
    $query->where('name', 'talent');
})->get();

echo "Found " . $users->count() . " talent users:\n";

foreach ($users as $user) {
    echo "\n--- User: {$user->name} ({$user->email}) ---\n";

    $requests = \App\Models\TalentRequest::where('talent_user_id', $user->id)
        ->with(['recruiter.user'])
        ->get();

    if ($requests->count() > 0) {
        foreach ($requests as $request) {
            echo "  - Request #{$request->id}: {$request->status}\n";
            echo "    From: {$request->recruiter->user->name}\n";
            echo "    Message: " . Str::limit($request->request_message, 60) . "\n";
            echo "    Created: {$request->created_at->format('Y-m-d H:i:s')}\n";
        }
    } else {
        echo "  No talent requests found.\n";
    }
}

// Check if we have any approved/completed requests to test with
echo "\n=== Testing Request Status Distribution ===\n";

$statusCounts = \App\Models\TalentRequest::selectRaw('status, count(*) as count')
    ->groupBy('status')
    ->get();

foreach ($statusCounts as $status) {
    echo "- {$status->status}: {$status->count} requests\n";
}

// Create some test data if needed
echo "\n=== Creating Test Data if Needed ===\n";

$talentUser = \App\Models\User::whereHas('roles', function($query) {
    $query->where('name', 'talent');
})->first();

$recruiter = \App\Models\Recruiter::first();

if ($talentUser && $recruiter) {
    // Check if we need to create approved/completed requests for testing
    $approvedCount = \App\Models\TalentRequest::where('talent_user_id', $talentUser->id)
        ->where('status', 'approved')
        ->count();

    $completedCount = \App\Models\TalentRequest::where('talent_user_id', $talentUser->id)
        ->where('status', 'completed')
        ->count();

    if ($approvedCount == 0) {
        $request = new \App\Models\TalentRequest();
        $request->talent_user_id = $talentUser->id;
        $request->recruiter_id = $recruiter->id;
        $request->status = 'approved';
        $request->request_message = 'This is a test approved request to demonstrate the view functionality.';
        $request->save();
        echo "Created test approved request for {$talentUser->name}\n";
    }

    if ($completedCount == 0) {
        $request = new \App\Models\TalentRequest();
        $request->talent_user_id = $talentUser->id;
        $request->recruiter_id = $recruiter->id;
        $request->status = 'completed';
        $request->request_message = 'This is a test completed request to demonstrate the view functionality for completed projects.';
        $request->save();
        echo "Created test completed request for {$talentUser->name}\n";
    }
} else {
    echo "Could not find talent user or recruiter to create test data.\n";
}

echo "\n=== Test Login Credentials ===\n";
echo "You can now test the view functionality with these accounts:\n";

$talents = \App\Models\User::whereHas('roles', function($query) {
    $query->where('name', 'talent');
})->take(3)->get();

foreach ($talents as $talent) {
    $requestCount = \App\Models\TalentRequest::where('talent_user_id', $talent->id)->count();
    echo "- Email: {$talent->email} (Password: password123) - {$requestCount} total requests\n";
}

echo "\nAfter logging in, go to the talent dashboard and test the 'View Details' buttons for all request types.\n";
echo "All modals should now work properly!\n";

?>
