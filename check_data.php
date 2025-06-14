<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Data Structure ===\n\n";

// Check if there are any talent requests with data
$talentRequest = \App\Models\TalentRequest::with(['recruiter.user', 'talent.user'])->first();

if ($talentRequest) {
    echo "Found Talent Request ID: " . $talentRequest->id . "\n\n";

    echo "=== RECRUITER DATA ===\n";
    if ($talentRequest->recruiter) {
        echo "Recruiter ID: " . $talentRequest->recruiter->id . "\n";
        echo "Recruiter phone (direct): " . ($talentRequest->recruiter->phone ?? 'NULL') . "\n";

        if ($talentRequest->recruiter->user) {
            echo "Recruiter User ID: " . $talentRequest->recruiter->user->id . "\n";
            echo "Recruiter User Name: " . $talentRequest->recruiter->user->name . "\n";
            echo "Recruiter User Phone: " . ($talentRequest->recruiter->user->phone ?? 'NULL') . "\n";
            echo "Recruiter User Email: " . $talentRequest->recruiter->user->email . "\n";
        }
    }

    echo "\n=== TALENT DATA ===\n";
    if ($talentRequest->talent) {
        echo "Talent ID: " . $talentRequest->talent->id . "\n";

        if ($talentRequest->talent->user) {
            echo "Talent User ID: " . $talentRequest->talent->user->id . "\n";
            echo "Talent User Name: " . $talentRequest->talent->user->name . "\n";
            echo "Talent User Phone: " . ($talentRequest->talent->user->phone ?? 'NULL') . "\n";
            echo "Talent User Experience: " . ($talentRequest->talent->user->experience_level ?? 'NULL') . "\n";
            echo "Talent User Skills: " . ($talentRequest->talent->user->talent_skills ?? 'NULL') . "\n";
            echo "Talent User Email: " . $talentRequest->talent->user->email . "\n";
        }
    }
} else {
    echo "No talent requests found.\n";
}

echo "\n=== Checking Sample Users ===\n";
$users = \App\Models\User::take(3)->get();
foreach ($users as $user) {
    echo "User {$user->id}: {$user->name}\n";
    echo "  Phone: " . ($user->phone ?? 'NULL') . "\n";
    echo "  Experience: " . ($user->experience_level ?? 'NULL') . "\n";
    echo "  Skills: " . ($user->talent_skills ?? 'NULL') . "\n\n";
}
