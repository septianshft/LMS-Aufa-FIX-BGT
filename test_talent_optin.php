<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::where('email', 'trainee@test.com')->first();

if ($user) {
    // Opt user into talent scouting
    $user->update([
        'available_for_scouting' => true,
        'talent_bio' => 'Experienced developer with skills in JavaScript and Python'
    ]);

    echo "User updated:\n";
    echo "Name: " . $user->name . "\n";
    echo "Available for scouting: " . ($user->available_for_scouting ? 'Yes' : 'No') . "\n";
    echo "Bio: " . $user->talent_bio . "\n";
    echo "Is available for scouting: " . ($user->isAvailableForScouting() ? 'Yes' : 'No') . "\n";
    echo "Skills count: " . count($user->talent_skills ?? []) . "\n";
} else {
    echo "User not found\n";
}
