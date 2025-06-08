<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "Checking for talent admin users...\n";

// Check for both email formats
$users1 = User::where('email', 'talentadmin@test.com')->get(['id', 'name', 'email']);
$users2 = User::where('email', 'talent_admin@test.com')->get(['id', 'name', 'email']);

echo "\nUsers with email 'talentadmin@test.com':\n";
foreach($users1 as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
}

echo "\nUsers with email 'talent_admin@test.com':\n";
foreach($users2 as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
}

// Check all users with 'talent' in their email
echo "\nAll users with 'talent' in email:\n";
$allTalentUsers = User::where('email', 'LIKE', '%talent%')->get(['id', 'name', 'email']);
foreach($allTalentUsers as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
}

if($users1->isEmpty() && $users2->isEmpty()) {
    echo "\nNo talent admin users found!\n";
} else {
    echo "\nTalent admin users found successfully!\n";
}
