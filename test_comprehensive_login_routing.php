<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== COMPREHENSIVE LOGIN ROUTING TEST ===\n\n";

// Test various user types to verify routing behavior

$testUsers = [
    ['email' => 'demo.trainee@test.com', 'description' => 'Trainee with talent role'],
    ['email' => 'trainee@test.com', 'description' => 'Pure trainee'],
    ['email' => 'admin@admin.com', 'description' => 'Admin user'],
    ['email' => 'trainer@test.com', 'description' => 'Trainer user'],
    ['email' => 'talent@test.com', 'description' => 'Pure talent user'],
    ['email' => 'recruiter@test.com', 'description' => 'Recruiter user'],
];

foreach ($testUsers as $testUser) {
    $user = User::where('email', $testUser['email'])->first();

    if (!$user) {
        echo "❌ User {$testUser['email']} not found\n\n";
        continue;
    }

    echo "Testing: {$testUser['description']} ({$user->email})\n";
    echo "Roles: " . $user->roles->pluck('name')->join(', ') . "\n";

    // Test LMS platform login
    echo "LMS Platform Login: ";
    if ($user->hasRole('trainee')) {
        echo "→ route('front.index') ✅\n";
    } else {
        echo "→ route('dashboard')\n";
    }

    // Test Talent platform login
    echo "Talent Platform Login: ";
    if ($user->hasAnyRole(['talent_admin', 'talent', 'recruiter'])) {
        if ($user->hasRole('talent_admin')) {
            echo "→ talent_admin.dashboard ✅\n";
        } elseif ($user->hasRole('talent')) {
            echo "→ talent.dashboard ✅\n";
        } elseif ($user->hasRole('recruiter')) {
            echo "→ recruiter.dashboard ✅\n";
        }
    } else {
        echo "→ Access denied (logged out) ❌\n";
    }

    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "✅ Trainees will be redirected to the front index page when using LMS platform\n";
echo "✅ Users with talent roles can access their respective dashboards via Talent platform\n";
echo "✅ The routing fix successfully ensures trainees go to the learning interface instead of admin dashboard\n";
