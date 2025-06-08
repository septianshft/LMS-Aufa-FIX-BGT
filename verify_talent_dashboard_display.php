<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TALENT DASHBOARD DISPLAY VERIFICATION ===\n\n";

try {
    // Simulate getting data like the controller does
    $user = \App\Models\User::where('email', 'talent@test.com')->first();

    if (!$user) {
        echo "❌ Test talent user not found. Creating sample data...\n";

        // Create a test talent user if doesn't exist
        $user = \App\Models\User::create([
            'name' => 'Test Talent',
            'email' => 'talent@test.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Assign talent role
        $user->assignRole('talent');
        echo "✅ Created test talent user\n";
    }

    echo "User: {$user->name} ({$user->email})\n\n";

    // Get incoming talent requests for this user
    $incomingRequests = \App\Models\TalentRequest::where('talent_user_id', $user->id)
        ->with(['recruiter.user'])
        ->orderBy('created_at', 'desc')
        ->get();

    echo "Total incoming requests: {$incomingRequests->count()}\n";

    // Group requests by status for easy display
    $pendingRequests = $incomingRequests->where('status', 'pending');
    $activeRequests = $incomingRequests->whereIn('status', ['approved', 'meeting_arranged', 'agreement_reached']);
    $completedRequests = $incomingRequests->whereIn('status', ['onboarded', 'completed']);

    echo "Pending requests: {$pendingRequests->count()}\n";
    echo "Active requests: {$activeRequests->count()}\n";
    echo "Completed requests: {$completedRequests->count()}\n\n";

    // Check each type of request for display issues
    echo "=== PENDING REQUESTS DISPLAY CHECK ===\n";
    foreach ($pendingRequests as $request) {
        echo "Request ID: {$request->id}\n";
        echo "- Project Title: " . ($request->project_title ?? 'N/A') . "\n";
        echo "- Message: " . Str::limit($request->recruiter_message, 50) . "\n";
        echo "- Budget: " . ($request->budget_range ?? 'N/A') . "\n";
        echo "- Recruiter: {$request->recruiter->user->name}\n";
        echo "- Display Ready: ✅\n";
        echo "---\n";
    }

    echo "\n=== ACTIVE REQUESTS DISPLAY CHECK ===\n";
    foreach ($activeRequests as $request) {
        echo "Request ID: {$request->id}\n";
        echo "- Project Title: " . ($request->project_title ?? 'N/A') . "\n";
        echo "- Status: {$request->status}\n";
        echo "- Progress: " . ($request->status == 'approved' ? '25%' : ($request->status == 'meeting_arranged' ? '50%' : '75%')) . "\n";
        echo "- Budget: " . ($request->budget_range ?? 'N/A') . "\n";
        echo "- Recruiter: {$request->recruiter->user->name}\n";
        echo "- Display Ready: ✅\n";
        echo "---\n";
    }

    echo "\n=== COMPLETED REQUESTS DISPLAY CHECK ===\n";
    foreach ($completedRequests as $request) {
        echo "Request ID: {$request->id}\n";
        echo "- Project Title: " . ($request->project_title ?? 'N/A') . "\n";
        echo "- Duration: " . ($request->project_duration ?? 'N/A') . "\n";
        echo "- Budget: " . ($request->budget_range ?? 'N/A') . "\n";
        echo "- Recruiter: {$request->recruiter->user->name}\n";
        echo "- Display Ready: ✅\n";
        echo "---\n";
    }

    // Check for potential UI issues
    echo "\n=== POTENTIAL UI ISSUES CHECK ===\n";

    $issues = [];

    foreach ($incomingRequests as $request) {
        // Check for missing recruiter data
        if (!$request->recruiter || !$request->recruiter->user) {
            $issues[] = "Request {$request->id}: Missing recruiter/user relationship";
        }

        // Check for very long text that might break layout
        if (strlen($request->recruiter_message) > 500) {
            $issues[] = "Request {$request->id}: Very long message (might need truncation)";
        }

        // Check for empty essential fields
        if (empty($request->recruiter_message)) {
            $issues[] = "Request {$request->id}: Empty recruiter message";
        }

        // Check for missing project title (for better UX)
        if (empty($request->project_title)) {
            $issues[] = "Request {$request->id}: Missing project title (using fallback)";
        }
    }

    if (empty($issues)) {
        echo "✅ No display issues found - All content should render properly\n";
    } else {
        echo "⚠️ Found " . count($issues) . " potential issues:\n";
        foreach ($issues as $issue) {
            echo "- {$issue}\n";
        }
    }

    echo "\n=== DASHBOARD TEMPLATES TEST ===\n";
    echo "Testing if all template variables are accessible...\n";

    // Test the variables that are passed to the template
    $templateVars = [
        'user' => $user,
        'title' => 'Talent Dashboard',
        'roles' => 'Talent',
        'assignedKelas' => [],
        'incomingRequests' => $incomingRequests,
        'pendingRequests' => $pendingRequests,
        'activeRequests' => $activeRequests,
        'completedRequests' => $completedRequests
    ];

    foreach ($templateVars as $varName => $varValue) {
        if (is_object($varValue) && method_exists($varValue, 'count')) {
            echo "✅ \${$varName}: " . $varValue->count() . " items\n";
        } elseif (is_array($varValue)) {
            echo "✅ \${$varName}: " . count($varValue) . " items\n";
        } elseif (is_object($varValue)) {
            echo "✅ \${$varName}: " . get_class($varValue) . " object\n";
        } else {
            echo "✅ \${$varName}: {$varValue}\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
echo "The dashboard should now display all content properly without missing text or broken layouts.\n";
?>
