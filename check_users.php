<?php
// Simple script to check users and roles
require_once 'bootstrap/app.php';
$app = \Illuminate\Foundation\Application::getInstance();
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== Users and their roles ===\n";
$users = User::with(['roles', 'talent', 'recruiter'])->get();

foreach ($users as $user) {
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Roles: " . $user->roles->pluck('name')->join(', ') . "\n";

    if ($user->talent) {
        echo "Talent Record: Active=" . ($user->talent->is_active ? 'Yes' : 'No') . "\n";
    }

    if ($user->recruiter) {
        echo "Recruiter Record: Active=" . ($user->recruiter->is_active ? 'Yes' : 'No') . "\n";
    }

    echo "---\n";
}
