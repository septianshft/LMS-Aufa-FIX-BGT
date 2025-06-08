<?php
// Test the TalentRequest model with user relationship
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TalentRequest;

echo "Testing TalentRequest with user relationship..." . PHP_EOL;

try {
    // Test the relationship
    $requests = TalentRequest::with(['user'])->limit(3)->get();
    echo "Successfully loaded " . $requests->count() . " talent requests with user relationship." . PHP_EOL;

    foreach ($requests as $request) {
        echo "- Request #" . $request->id . ": ";
        if ($request->user) {
            echo "User: " . $request->user->name . " (" . $request->user->email . ")" . PHP_EOL;
        } else {
            echo "No user found (talent_user_id: " . $request->talent_user_id . ")" . PHP_EOL;
        }
    }

    echo PHP_EOL . "Relationship test completed successfully!" . PHP_EOL;

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . PHP_EOL;
}
