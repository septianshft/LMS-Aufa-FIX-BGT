<?php
// Update existing talent requests to have proper recruiter references
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TalentRequest;
use App\Models\Recruiter;

echo "=== Updating Recruiter References ===" . PHP_EOL;

try {
    // Get the default recruiter
    $recruiter = Recruiter::first();

    if (!$recruiter) {
        echo "No recruiter found. Creating default recruiter..." . PHP_EOL;
        $recruiterUser = \App\Models\User::where('email', 'recruiter@test.com')->first();

        if ($recruiterUser) {
            $recruiter = Recruiter::create([
                'user_id' => $recruiterUser->id,
                'company_name' => 'TechCorp Solutions',
                'position' => 'Senior Talent Acquisition Manager',
                'company_description' => 'Leading technology solutions provider',
                'phone' => '+1-555-0123',
                'linkedin_profile' => 'https://linkedin.com/in/jane-recruiter',
                'verification_status' => 'verified',
                'is_active' => true
            ]);
        }
    }

    if ($recruiter) {
        // Update requests without recruiter
        $requestsWithoutRecruiter = TalentRequest::whereNull('recruiter_id')->orWhere('recruiter_id', 0)->get();

        foreach ($requestsWithoutRecruiter as $request) {
            $request->update(['recruiter_id' => $recruiter->id]);
            echo "Updated request #" . $request->id . " with recruiter: " . $recruiter->company_name . PHP_EOL;
        }

        echo "Updated " . $requestsWithoutRecruiter->count() . " requests with recruiter reference." . PHP_EOL;
    }

    echo PHP_EOL . "=== Testing Updated Relationships ===" . PHP_EOL;
    $requests = TalentRequest::with(['user', 'recruiter'])->limit(5)->get();

    foreach ($requests as $request) {
        echo "Request #" . $request->id . ": " . ($request->project_title ?? 'No title') . PHP_EOL;
        echo "  User: " . ($request->user ? $request->user->name . " (" . $request->user->email . ")" : "No user") . PHP_EOL;
        echo "  Recruiter: " . ($request->recruiter ? $request->recruiter->company_name : "No recruiter") . PHP_EOL;
        echo "  Status: " . $request->status . PHP_EOL;
        echo "---" . PHP_EOL;
    }

    echo PHP_EOL . "Update completed successfully!" . PHP_EOL;

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
