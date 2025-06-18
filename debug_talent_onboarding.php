<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\Project;
use App\Models\TalentRequest;
use App\Models\ProjectAssignment;

// Setup database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== TALENT ONBOARDING DEBUG ===\n\n";

// Check recent talent requests with 'onboarded' status
echo "1. Recent onboarded talent requests:\n";
$onboardedRequests = Capsule::table('talent_requests')
    ->where('status', 'onboarded')
    ->whereNotNull('project_id')
    ->orderBy('updated_at', 'desc')
    ->limit(5)
    ->get(['id', 'project_id', 'talent_id', 'talent_user_id', 'project_title', 'status', 'updated_at']);

foreach ($onboardedRequests as $request) {
    echo "  Request ID: {$request->id}, Project ID: {$request->project_id}, Talent ID: {$request->talent_id}, User ID: {$request->talent_user_id}\n";
    echo "  Title: {$request->project_title}, Status: {$request->status}, Updated: {$request->updated_at}\n";

    // Check corresponding project assignments
    $assignments = Capsule::table('project_assignments')
        ->where('project_id', $request->project_id)
        ->where('talent_id', $request->talent_id)
        ->get(['id', 'status', 'talent_accepted_at', 'assignment_notes']);

    echo "  Corresponding assignments: " . count($assignments) . "\n";
    foreach ($assignments as $assignment) {
        echo "    Assignment ID: {$assignment->id}, Status: {$assignment->status}, Accepted: {$assignment->talent_accepted_at}\n";
        echo "    Notes: " . substr($assignment->assignment_notes ?? 'None', 0, 50) . "\n";
    }
    echo "\n";
}

// Check projects with active status
echo "\n2. Active projects with their assignments:\n";
$activeProjects = Capsule::table('projects')
    ->where('status', 'active')
    ->orderBy('updated_at', 'desc')
    ->limit(3)
    ->get(['id', 'title', 'status', 'updated_at']);

foreach ($activeProjects as $project) {
    echo "  Project ID: {$project->id}, Title: {$project->title}, Status: {$project->status}\n";

    $assignments = Capsule::table('project_assignments')
        ->where('project_id', $project->id)
        ->get(['id', 'talent_id', 'status']);

    $talentRequests = Capsule::table('talent_requests')
        ->where('project_id', $project->id)
        ->get(['id', 'talent_id', 'status']);

    echo "    Assignments: " . count($assignments) . ", Talent Requests: " . count($talentRequests) . "\n";
    echo "\n";
}

// Check for specific inconsistencies
echo "\n3. Inconsistency check - Projects with onboarded talent requests but no accepted assignments:\n";
$inconsistentProjects = Capsule::select(
    "SELECT DISTINCT p.id as project_id, p.title,
     COUNT(tr.id) as onboarded_requests,
     COUNT(pa.id) as accepted_assignments
     FROM projects p
     LEFT JOIN talent_requests tr ON p.id = tr.project_id AND tr.status = 'onboarded'
     LEFT JOIN project_assignments pa ON p.id = pa.project_id AND pa.status = 'accepted'
     WHERE p.status IN ('approved', 'active')
     GROUP BY p.id, p.title
     HAVING onboarded_requests > 0 AND accepted_assignments = 0"
);

foreach ($inconsistentProjects as $project) {
    echo "  Project ID: {$project->project_id}, Title: {$project->title}\n";
    echo "  Onboarded Requests: {$project->onboarded_requests}, Accepted Assignments: {$project->accepted_assignments}\n";

    // Get detailed talent request info for this project
    $requests = Capsule::table('talent_requests')
        ->where('project_id', $project->project_id)
        ->where('status', 'onboarded')
        ->get(['id', 'talent_id', 'talent_user_id', 'onboarded_at']);

    foreach ($requests as $request) {
        echo "    Request ID: {$request->id}, Talent ID: {$request->talent_id}, User ID: {$request->talent_user_id}, Onboarded: {$request->onboarded_at}\n";
    }
    echo "\n";
}

echo "=== END DEBUG ===\n";
