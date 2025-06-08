<?php
// Check the actual recruiter_id values in talent_requests
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TalentRequest;
use App\Models\Recruiter;
use Illuminate\Support\Facades\DB;

echo "=== Checking Recruiter Data ===" . PHP_EOL;

try {
    // Check recruiters table
    $recruiters = Recruiter::all();
    echo "Total recruiters: " . $recruiters->count() . PHP_EOL;

    foreach ($recruiters as $recruiter) {
        echo "Recruiter #" . $recruiter->id . ": " . $recruiter->company_name . " (User ID: " . $recruiter->user_id . ")" . PHP_EOL;
    }

    echo PHP_EOL . "=== Checking Talent Requests ===" . PHP_EOL;

    // Check talent requests directly from database
    $requests = DB::table('talent_requests')->select('id', 'recruiter_id', 'project_title')->limit(10)->get();

    foreach ($requests as $request) {
        echo "Request #" . $request->id . ": " . ($request->project_title ?? 'No title') . " (recruiter_id: " . $request->recruiter_id . ")" . PHP_EOL;
    }

    // Check for null or 0 recruiter_id
    $nullRecruiterIds = DB::table('talent_requests')->whereNull('recruiter_id')->count();
    $zeroRecruiterIds = DB::table('talent_requests')->where('recruiter_id', 0)->count();

    echo PHP_EOL . "Requests with NULL recruiter_id: " . $nullRecruiterIds . PHP_EOL;
    echo "Requests with 0 recruiter_id: " . $zeroRecruiterIds . PHP_EOL;

    // Try to update one request manually
    if ($recruiters->isNotEmpty()) {
        $firstRecruiter = $recruiters->first();
        $affected = DB::table('talent_requests')
            ->where('id', 1)
            ->update(['recruiter_id' => $firstRecruiter->id]);

        echo PHP_EOL . "Manual update test - affected rows: " . $affected . PHP_EOL;
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
