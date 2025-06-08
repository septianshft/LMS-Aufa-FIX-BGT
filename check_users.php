<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

// Check our test users and their roles
$users = User::with('roles')->whereIn('email', [
    'trainee@test.com',
    'admin@test.com',
    'trainer@test.com',
    'talent@test.com',
    'talent_admin@test.com',
    'recruiter@test.com'
])->get();

echo "=== Test Users Status ===\n\n";

foreach ($users as $user) {
    echo "User: {$user->name} ({$user->email})\n";
    echo "Roles: " . $user->roles->pluck('name')->join(', ') . "\n";

    if ($user->talent_skills) {
        echo "Skills: " . count($user->talent_skills) . " skills\n";
        foreach ($user->talent_skills as $skill) {
            echo "  - {$skill['name']} ({$skill['level']})\n";
        }
    } else {
        echo "Skills: None\n";
    }

    echo "Available for scouting: " . ($user->available_for_scouting ? 'Yes' : 'No') . "\n";
    echo "Active talent: " . ($user->is_active_talent ? 'Yes' : 'No') . "\n";
    echo "---\n";
}

echo "\n=== Login Test Instructions ===\n";
echo "1. Visit: http://127.0.0.1:8000/login\n";
echo "2. Toggle between LMS and Talent platforms\n";
echo "3. Test with these credentials:\n";
echo "   - Trainee: trainee@test.com / password123\n";
echo "   - Admin: admin@test.com / password123\n";
echo "   - Talent Admin: talent_admin@test.com / password123\n";
echo "   - Recruiter: recruiter@test.com / password123\n";
echo "4. Verify routing to correct dashboards\n";
