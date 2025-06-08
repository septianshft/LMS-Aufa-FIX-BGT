<?php

require_once 'vendor/autoload.php';

// Initialize Laravel application
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Str;

echo "=== Talent Request View Feature Test ===\n\n";

// Test users that have requests
$testUsers = [
    ['email' => 'trainee@test.com', 'password' => 'password123'],
    ['email' => 'demo.trainee@test.com', 'password' => 'password123']
];

foreach ($testUsers as $testUser) {
    echo "=== Testing user: {$testUser['email']} ===\n";

    $user = \App\Models\User::where('email', $testUser['email'])->first();

    if (!$user) {
        echo "User not found!\n\n";
        continue;
    }

    // Get all requests like the controller does
    $incomingRequests = \App\Models\TalentRequest::where('talent_user_id', $user->id)
        ->with(['recruiter.user'])
        ->orderBy('created_at', 'desc')
        ->get();

    // Group requests by status
    $pendingRequests = $incomingRequests->where('status', 'pending');
    $activeRequests = $incomingRequests->whereIn('status', ['approved', 'meeting_arranged', 'agreement_reached']);
    $completedRequests = $incomingRequests->whereIn('status', ['onboarded', 'completed']);

    echo "Total requests: {$incomingRequests->count()}\n";
    echo "Pending: {$pendingRequests->count()}\n";
    echo "Active: {$activeRequests->count()}\n";
    echo "Completed: {$completedRequests->count()}\n";

    echo "\nRequest statuses breakdown:\n";
    $statusCounts = $incomingRequests->groupBy('status');
    foreach ($statusCounts as $status => $requests) {
        echo "- {$status}: {$requests->count()}\n";
    }

    // Verify modal generation
    echo "\nModal IDs that should be generated:\n";
    foreach ($incomingRequests as $request) {
        echo "- viewRequestModal{$request->id} (Status: {$request->status})\n";
    }

    echo "\n";
}

echo "=== Test Instructions ===\n";
echo "1. Go to http://localhost:8000/login\n";
echo "2. Login with trainee@test.com / password123\n";
echo "3. You should see sections for:\n";
echo "   - Pending Talent Requests (with Accept/Decline/View buttons)\n";
echo "   - Active Projects (with View Details buttons)\n";
echo "   - Completed Projects (with View Details buttons)\n";
echo "4. Click any 'View' or 'View Details' button\n";
echo "5. A modal should open showing full request information\n";
echo "6. For pending requests: Accept/Decline buttons should be available\n";
echo "7. For non-pending requests: Should show 'This request has been [status]'\n";
echo "\nAll view buttons should now work regardless of request status!\n";

?>
