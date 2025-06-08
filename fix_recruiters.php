<?php
// Fix recruiter data
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Recruiter;
use App\Models\User;

echo "=== FIXING RECRUITER DATA ===" . PHP_EOL;

try {
    $recruiters = Recruiter::all();

    foreach ($recruiters as $recruiter) {
        $updates = [];

        if (empty($recruiter->company_name)) {
            $updates['company_name'] = 'TechCorp Solutions';
        }

        if (empty($recruiter->position)) {
            $updates['position'] = 'Senior Talent Acquisition Manager';
        }

        if (empty($recruiter->company_description)) {
            $updates['company_description'] = 'Leading technology solutions provider specializing in innovative digital transformation.';
        }

        if (empty($recruiter->verification_status)) {
            $updates['verification_status'] = 'verified';
        }

        if (!empty($updates)) {
            $recruiter->update($updates);
            echo "Updated recruiter #" . $recruiter->id . " with: " . implode(', ', array_keys($updates)) . PHP_EOL;
        }
    }

    echo PHP_EOL . "=== RECRUITER DATA UPDATED ===" . PHP_EOL;

    // Display updated recruiter info
    $recruiters = Recruiter::with('user')->get();
    foreach ($recruiters as $recruiter) {
        echo "Recruiter #" . $recruiter->id . ":" . PHP_EOL;
        echo "  Company: " . $recruiter->company_name . PHP_EOL;
        echo "  Position: " . $recruiter->position . PHP_EOL;
        echo "  User: " . ($recruiter->user ? $recruiter->user->name . " (" . $recruiter->user->email . ")" : "No user") . PHP_EOL;
        echo "  Status: " . ($recruiter->is_active ? "Active" : "Inactive") . PHP_EOL;
        echo "  Verification: " . $recruiter->verification_status . PHP_EOL;
        echo "---" . PHP_EOL;
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
