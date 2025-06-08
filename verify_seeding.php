<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "USERS BY ROLE:\n";
echo "==============\n";
$users = \App\Models\User::with('roles')->get();
foreach($users as $user) {
    $roles = $user->roles->pluck('name')->join(', ');
    echo sprintf("%-30s | %-20s | %s\n", $user->email, $roles, $user->name);
}
echo "\n";

echo "TALENT PROFILES:\n";
echo "================\n";
$talents = \App\Models\Talent::with('user')->get();
foreach($talents as $talent) {
    echo sprintf("%-30s | Active: %s | %s\n", $talent->user->email, $talent->is_active ? 'Yes' : 'No', $talent->user->name);
}
echo "\n";

echo "RECRUITER PROFILES:\n";
echo "===================\n";
$recruiters = \App\Models\Recruiter::with('user')->get();
foreach($recruiters as $recruiter) {
    echo sprintf("%-30s | Active: %s | %s\n", $recruiter->user->email, $recruiter->is_active ? 'Yes' : 'No', $recruiter->user->name);
}
echo "\n";

echo "TALENT REQUESTS:\n";
echo "================\n";
$requests = \App\Models\TalentRequest::with(['recruiter.user', 'talent.user'])->get();
foreach($requests as $request) {
    echo sprintf("%-25s | %-25s | %-10s | %s\n", $request->recruiter->user->email, $request->talent->user->email, $request->status, $request->project_title);
}
echo "\n";

echo "ROLE-BASED ACCESS VERIFICATION:\n";
echo "================================\n";

// Test role-based access for specific users
$testUsers = [
    'admin@lms.test' => ['expected_roles' => ['admin'], 'system' => 'LMS'],
    'talent.admin@scout.test' => ['expected_roles' => ['talent_admin'], 'system' => 'Talent Scouting'],
    'trainee@lms.test' => ['expected_roles' => ['trainee'], 'system' => 'LMS'],
    'recruiter@scout.test' => ['expected_roles' => ['recruiter'], 'system' => 'Talent Scouting'],
    'dual.trainee@test.com' => ['expected_roles' => ['trainee', 'talent'], 'system' => 'Both']
];

foreach($testUsers as $email => $expected) {
    $user = \App\Models\User::where('email', $email)->with('roles')->first();
    if ($user) {
        $actualRoles = $user->roles->pluck('name')->toArray();
        $rolesMatch = count(array_diff($expected['expected_roles'], $actualRoles)) === 0;
        $status = $rolesMatch ? '✅ PASS' : '❌ FAIL';
        echo sprintf("%-30s | %-15s | %s | Roles: %s\n",
            $email,
            $expected['system'],
            $status,
            implode(', ', $actualRoles)
        );
    } else {
        echo sprintf("%-30s | %-15s | ❌ USER NOT FOUND\n", $email, $expected['system']);
    }
}

echo "\nSEEDING VERIFICATION COMPLETE!\n";
