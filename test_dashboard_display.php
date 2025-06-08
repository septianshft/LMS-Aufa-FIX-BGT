<?php

require_once 'vendor/autoload.php';

// Test dashboard display content
echo "=== TESTING DASHBOARD CONTENT DISPLAY ===\n\n";

// Read the current dashboard file
$dashboardFile = 'resources/views/admin/talent/dashboard.blade.php';
$content = file_get_contents($dashboardFile);

echo "Dashboard file size: " . strlen($content) . " characters\n";
echo "Dashboard file lines: " . substr_count($content, "\n") . " lines\n\n";

// Check for key sections
$sections = [
    'Enhanced Success/Error Messages' => 'Enhanced Success/Error Messages',
    'Enhanced Welcome Card' => 'Enhanced Welcome Card',
    'Enhanced Request Summary Cards' => 'Enhanced Request Summary Cards',
    'Enhanced Pending Requests Table' => 'Enhanced Pending Requests Table',
    'Enhanced Active Projects Table' => 'Enhanced Active Projects Table',
    'Enhanced Completed Projects Table' => 'Enhanced Completed Projects Table',
    'Enhanced Request Detail Modals' => 'Enhanced Request Detail Modals',
    'CSS Styles' => '<style>',
    'JavaScript Functions' => '<script>'
];

echo "=== CHECKING DASHBOARD SECTIONS ===\n";
foreach ($sections as $name => $marker) {
    $found = strpos($content, $marker) !== false;
    echo "✓ $name: " . ($found ? 'FOUND' : 'MISSING') . "\n";
}

// Check for specific table content patterns
echo "\n=== CHECKING TABLE CONTENT PATTERNS ===\n";
$patterns = [
    'Pending Requests Loop' => '@foreach($pendingRequests as $request)',
    'Active Requests Loop' => '@foreach($activeRequests as $request)',
    'Completed Requests Loop' => '@foreach($completedRequests as $request)',
    'Modal Loop' => '@foreach($incomingRequests as $request)',
    'Recruiter Name Display' => '{{ $request->recruiter->user->name }}',
    'Request Message Display' => '{{ $request->recruiter_message }}',
    'Action Buttons' => 'respondToRequest',
    'View Modal Button' => 'data-toggle="modal"'
];

foreach ($patterns as $name => $pattern) {
    $count = substr_count($content, $pattern);
    echo "✓ $name: $count occurrence(s)\n";
}

// Check for potential truncation issues
echo "\n=== CHECKING FOR POTENTIAL ISSUES ===\n";
$issues = [
    'Empty TD tags' => '<td class="px-4 py-4"></td>',
    'Empty DIV tags' => '<div class="d-flex align-items-center"></div>',
    'Incomplete table rows' => '<tr class="border-left-',
    'Missing closing tags' => '</tbody>'
];

foreach ($issues as $name => $pattern) {
    $count = substr_count($content, $pattern);
    if ($count > 0) {
        echo "⚠ $name: $count occurrence(s) - POTENTIAL ISSUE\n";
    } else {
        echo "✓ $name: No issues found\n";
    }
}

echo "\n=== CONTENT VALIDATION COMPLETE ===\n";

// Test with sample data to see if the display works
echo "\n=== TESTING WITH BOOTSTRAP ACCESS ===\n";

// Initialize Laravel environment for testing
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Get a talent user for testing
    $user = \App\Models\User::whereHas('roles', function($query) {
        $query->where('name', 'talent');
    })->first();

    if ($user) {
        echo "✓ Test talent user found: {$user->name} (ID: {$user->id})\n";

        // Get talent requests for this user
        $incomingRequests = \App\Models\TalentRequest::where('talent_user_id', $user->id)->get();
        $pendingRequests = $incomingRequests->where('status', 'pending');
        $activeRequests = $incomingRequests->whereIn('status', ['approved', 'in_progress']);
        $completedRequests = $incomingRequests->where('status', 'completed');

        echo "✓ Total requests: " . $incomingRequests->count() . "\n";
        echo "✓ Pending requests: " . $pendingRequests->count() . "\n";
        echo "✓ Active requests: " . $activeRequests->count() . "\n";
        echo "✓ Completed requests: " . $completedRequests->count() . "\n";

        // Test if requests have required relationships
        foreach ($incomingRequests as $request) {
            if ($request->recruiter && $request->recruiter->user) {
                echo "✓ Request {$request->id}: Has recruiter - {$request->recruiter->user->name}\n";
            } else {
                echo "⚠ Request {$request->id}: Missing recruiter relationship\n";
            }
        }

    } else {
        echo "⚠ No talent user found for testing\n";
    }

} catch (Exception $e) {
    echo "⚠ Error during testing: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
