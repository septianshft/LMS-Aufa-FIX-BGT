<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\TalentRequest;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🎯 QUICK TEST - WHICH TALENT HAS PENDING REQUESTS?\n";
echo "=================================================\n\n";

// Check Test Trainee (the one with pending requests)
$testTrainee = User::where('email', 'trainee@test.com')->first();

if ($testTrainee) {
    echo "✅ FOUND USER: {$testTrainee->name} ({$testTrainee->email})\n";
    echo "User ID: {$testTrainee->id}\n\n";

    // Get their pending requests
    $pendingRequests = TalentRequest::where('talent_user_id', $testTrainee->id)
        ->where('status', 'pending')
        ->with(['recruiter.user'])
        ->get();

    echo "📨 PENDING REQUESTS: {$pendingRequests->count()}\n\n";

    foreach ($pendingRequests as $request) {
        echo "REQUEST #{$request->id}:\n";
        echo "  From: {$request->recruiter->user->name}\n";
        echo "  Email: {$request->recruiter->user->email}\n";
        echo "  Message: " . substr($request->request_message, 0, 100) . "...\n";
        echo "  Date: {$request->created_at->format('M d, Y H:i')}\n";
        echo "  Status: {$request->status}\n";
        echo "---\n";
    }

    echo "\n🔑 LOGIN CREDENTIALS TO TEST:\n";
    echo "Email: trainee@test.com\n";
    echo "Password: password123\n";
    echo "URL: http://127.0.0.1:8000/login\n\n";

    echo "📋 WHAT YOU SHOULD SEE:\n";
    echo "1. Choose 'Learning Platform' (trainee role)\n";
    echo "2. After login, go to talent dashboard manually: http://127.0.0.1:8000/talent/dashboard\n";
    echo "3. You should see:\n";
    echo "   - Blue alert: '2 new talent requests pending your review!'\n";
    echo "   - Pending Requests card showing '2'\n";
    echo "   - Table with 2 rows of requests\n";
    echo "   - Each row has Accept, Decline, and VIEW buttons\n";
    echo "   - Click VIEW to see the detailed modal\n\n";

} else {
    echo "❌ trainee@test.com not found!\n";
}

echo "🎯 STEPS TO TEST THE VIEW BUTTON:\n";
echo "1. Login with trainee@test.com / password123\n";
echo "2. Go to: http://127.0.0.1:8000/talent/dashboard\n";
echo "3. Look for the 'Pending Talent Requests' table\n";
echo "4. Click the blue 'View' button in any row\n";
echo "5. Modal should open with full offer details\n";
echo "===============================================\n";
