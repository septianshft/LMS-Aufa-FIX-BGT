<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "Testing talent admin login and roles...\n";

$user = User::where('email', 'talentadmin@test.com')->first();

if (!$user) {
    echo "❌ Talent admin user not found!\n";
    exit(1);
}

echo "✅ User found: {$user->name} ({$user->email})\n";

// Check password
if (password_verify('password123', $user->password)) {
    echo "✅ Password verification passed\n";
} else {
    echo "❌ Password verification failed\n";
}

// Check roles
$roles = $user->roles()->pluck('name')->toArray();
echo "User roles: " . implode(', ', $roles) . "\n";

if (in_array('talent_admin', $roles)) {
    echo "✅ User has talent_admin role\n";
} else {
    echo "❌ User does not have talent_admin role\n";
}

// Check TalentAdmin model
$talentAdmin = $user->talentAdmin;
if ($talentAdmin) {
    echo "✅ TalentAdmin record found (ID: {$talentAdmin->id})\n";
    echo "   Active: " . ($talentAdmin->is_active ? 'Yes' : 'No') . "\n";
} else {
    echo "❌ TalentAdmin record not found\n";
}

echo "\nUser details:\n";
echo "ID: {$user->id}\n";
echo "Name: {$user->name}\n";
echo "Email: {$user->email}\n";
echo "Job: {$user->pekerjaan}\n";
echo "Avatar: {$user->avatar}\n";
