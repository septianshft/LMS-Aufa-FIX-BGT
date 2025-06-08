<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TALENT DASHBOARD CONTENT DEBUG ===\n\n";

try {
    // Test database connection
    DB::connection()->getPdo();
    echo "✅ Database connection successful\n\n";

    // Check if tables exist
    $tables = ['users', 'talent_requests', 'recruiters'];
    foreach ($tables as $table) {
        if (Schema::hasTable($table)) {
            $count = DB::table($table)->count();
            echo "✅ Table '{$table}' exists with {$count} records\n";
        } else {
            echo "❌ Table '{$table}' does not exist\n";
        }
    }
    echo "\n";

    // Check talent requests structure
    echo "=== TALENT REQUESTS STRUCTURE ===\n";
    $columns = DB::select("DESCRIBE talent_requests");
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    echo "\n";

    // Sample talent request data
    echo "=== SAMPLE TALENT REQUEST DATA ===\n";
    $requests = DB::table('talent_requests')
        ->join('recruiters', 'talent_requests.recruiter_id', '=', 'recruiters.id')
        ->join('users as recruiter_users', 'recruiters.user_id', '=', 'recruiter_users.id')
        ->join('users as talent_users', 'talent_requests.talent_id', '=', 'talent_users.id')
        ->select(
            'talent_requests.id',
            'talent_requests.status',
            'talent_requests.recruiter_message',
            'talent_requests.created_at',
            'talent_requests.updated_at',
            'recruiter_users.name as recruiter_name',
            'recruiter_users.email as recruiter_email',
            'recruiters.company_name',
            'talent_users.name as talent_name'
        )
        ->limit(5)
        ->get();

    if ($requests->count() > 0) {
        foreach ($requests as $request) {
            echo "Request ID: {$request->id}\n";
            echo "Status: {$request->status}\n";
            echo "Recruiter: {$request->recruiter_name} ({$request->recruiter_email})\n";
            echo "Company: " . ($request->company_name ?? 'N/A') . "\n";
            echo "Talent: {$request->talent_name}\n";
            echo "Message: " . substr($request->recruiter_message, 0, 100) . "...\n";
            echo "Created: {$request->created_at}\n";
            echo "Updated: {$request->updated_at}\n";
            echo "---\n";
        }
    } else {
        echo "No talent requests found\n";
    }

    // Check request status distribution
    echo "\n=== REQUEST STATUS DISTRIBUTION ===\n";
    $statusCounts = DB::table('talent_requests')
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get();

    foreach ($statusCounts as $status) {
        echo "- {$status->status}: {$status->count} requests\n";
    }

    // Check for any potential display issues
    echo "\n=== POTENTIAL DISPLAY ISSUES CHECK ===\n";

    // Check for empty or null values
    $emptyMessages = DB::table('talent_requests')
        ->whereNull('recruiter_message')
        ->orWhere('recruiter_message', '')
        ->count();
    echo "Empty/null messages: {$emptyMessages}\n";

    // Check for very long messages that might break layout
    $longMessages = DB::table('talent_requests')
        ->where(DB::raw('LENGTH(recruiter_message)'), '>', 500)
        ->count();
    echo "Very long messages (>500 chars): {$longMessages}\n";

    // Check for missing recruiter relationships
    $missingRecruiters = DB::table('talent_requests')
        ->leftJoin('recruiters', 'talent_requests.recruiter_id', '=', 'recruiters.id')
        ->whereNull('recruiters.id')
        ->count();
    echo "Requests with missing recruiter data: {$missingRecruiters}\n";

    // Check for missing user relationships
    $missingUsers = DB::table('talent_requests')
        ->join('recruiters', 'talent_requests.recruiter_id', '=', 'recruiters.id')
        ->leftJoin('users', 'recruiters.user_id', '=', 'users.id')
        ->whereNull('users.id')
        ->count();
    echo "Requests with missing user data: {$missingUsers}\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
?>
