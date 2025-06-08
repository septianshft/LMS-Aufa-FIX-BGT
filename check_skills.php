<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::where('email', 'trainee@test.com')->first();

if ($user) {
    echo "User: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Available for scouting: " . ($user->available_for_scouting ? 'Yes' : 'No') . "\n";
    echo "Is active talent: " . ($user->is_active_talent ? 'Yes' : 'No') . "\n";
    echo "Skills: " . json_encode($user->talent_skills, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "User not found\n";
}
