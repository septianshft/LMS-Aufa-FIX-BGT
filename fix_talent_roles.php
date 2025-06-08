<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "🔧 Fixing talent roles...\n\n";

// Get users available for scouting who don't have talent role
$users = User::where('available_for_scouting', true)
    ->whereDoesntHave('roles', function($q) {
        $q->where('name', 'talent');
    })
    ->get();

echo "Found " . $users->count() . " users who need talent role assignment\n\n";

foreach ($users as $user) {
    $user->assignRole('talent');
    echo "✅ Assigned talent role to: {$user->name} ({$user->email})\n";
}

echo "\n📊 Final stats:\n";
echo "Users available for scouting: " . User::where('available_for_scouting', true)->count() . "\n";
echo "Users with talent role: " . User::role('talent')->count() . "\n";
echo "\n🎉 Role assignment complete!\n";
