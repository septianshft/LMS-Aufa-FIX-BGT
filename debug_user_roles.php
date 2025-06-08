<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::where('email', 'demo.trainee@test.com')->first();
if ($user) {
    echo "=== USER ROLES DEBUG ===\n";
    echo "User: {$user->name} ({$user->email})\n";
    echo "Role ID (legacy): {$user->roles_id}\n";
    echo "Spatie Roles: " . $user->roles->pluck('name')->join(', ') . "\n";
    echo "Has talent role: " . ($user->hasRole('talent') ? 'Yes' : 'No') . "\n";
    echo "Has trainee role: " . ($user->hasRole('trainee') ? 'Yes' : 'No') . "\n";
    echo "Available for scouting: " . ($user->available_for_scouting ? 'Yes' : 'No') . "\n";
    echo "Is active talent: " . ($user->is_active_talent ? 'Yes' : 'No') . "\n";
      echo "\n=== DASHBOARD LOGIC CHECK ===\n";
    if ($user->roles_id == 1) {
        echo "Would go to: Admin logic\n";
    } elseif ($user->roles_id == 2) {
        echo "Would go to: Trainer logic\n";
    } elseif ($user->roles_id == 3 || $user->hasRole('trainee')) {
        echo "Would go to: Trainee logic (LMS dashboard)\n";
    } elseif ($user->hasRole('talent_admin')) {
        echo "Would go to: talent_admin.dashboard\n";
    } elseif ($user->hasRole('recruiter')) {
        echo "Would go to: recruiter.dashboard\n";
    } elseif ($user->hasRole('talent')) {
        echo "Would go to: talent.dashboard (only if no trainee role)\n";
    } else {
        echo "Would go to: Default dashboard\n";
    }
} else {
    echo "User not found\n";
}
