<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Talent;
use App\Models\Recruiter;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

// Test creating a talent user
try {
    $user = User::create([
        'name' => 'Test Talent User',
        'email' => 'talent@test.com',
        'pekerjaan' => 'Developer',
        'avatar' => 'images/avatar-default.png',
        'password' => Hash::make('password123'),
    ]);

    // Assign role
    $role = Role::findByName('talent');
    $user->assignRole($role);

    // Create talent record
    Talent::create([
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    echo "✅ Talent user created successfully!\n";
    echo "User ID: " . $user->id . "\n";
    echo "User Role: " . $user->roles->first()->name . "\n";
    echo "Talent Record Created: " . ($user->talent ? 'Yes' : 'No') . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Test creating a recruiter user
try {
    $user2 = User::create([
        'name' => 'Test Recruiter User',
        'email' => 'recruiter@test.com',
        'pekerjaan' => 'HR Manager',
        'avatar' => 'images/avatar-default.png',
        'password' => Hash::make('password123'),
    ]);

    // Assign role
    $role = Role::findByName('recruiter');
    $user2->assignRole($role);

    // Create recruiter record
    Recruiter::create([
        'user_id' => $user2->id,
        'is_active' => true,
    ]);

    echo "✅ Recruiter user created successfully!\n";
    echo "User ID: " . $user2->id . "\n";
    echo "User Role: " . $user2->roles->first()->name . "\n";
    echo "Recruiter Record Created: " . ($user2->recruiter ? 'Yes' : 'No') . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
