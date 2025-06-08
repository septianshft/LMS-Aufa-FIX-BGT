<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING TALENT REQUEST RELATIONSHIPS ===\n";

// Test the query that the controller uses
$talentRequests = \App\Models\TalentRequest::with(['recruiter.user'])
    ->limit(3)
    ->get();

echo "Found " . $talentRequests->count() . " talent requests\n\n";

foreach ($talentRequests as $request) {
    echo "Request ID: {$request->id}\n";
    echo "Status: {$request->status}\n";
    echo "Message: " . substr($request->recruiter_message, 0, 100) . "...\n";
    echo "Created: {$request->created_at}\n";

    if ($request->recruiter) {
        echo "Recruiter ID: {$request->recruiter->id}\n";
        if ($request->recruiter->user) {
            echo "Recruiter Name: {$request->recruiter->user->name}\n";
            echo "Recruiter Email: {$request->recruiter->user->email}\n";
        } else {
            echo "❌ Recruiter user relationship missing\n";
        }
    } else {
        echo "❌ Recruiter relationship missing\n";
    }
    echo "---\n";
}

// Check for missing data
echo "\n=== MISSING DATA CHECK ===\n";

$requestsWithoutRecruiter = \App\Models\TalentRequest::whereDoesntHave('recruiter')->count();
echo "Requests without recruiter: {$requestsWithoutRecruiter}\n";

$requestsWithoutRecruiterUser = \App\Models\TalentRequest::whereHas('recruiter', function($query) {
    $query->whereDoesntHave('user');
})->count();
echo "Requests with recruiter but no user: {$requestsWithoutRecruiterUser}\n";

$requestsWithNullMessage = \App\Models\TalentRequest::whereNull('recruiter_message')->count();
echo "Requests with null message: {$requestsWithNullMessage}\n";

$requestsWithEmptyMessage = \App\Models\TalentRequest::where('recruiter_message', '')->count();
echo "Requests with empty message: {$requestsWithEmptyMessage}\n";

echo "\n=== DONE ===\n";
?>
