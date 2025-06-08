<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

echo "=== Creating Talent Admin User ===\n\n";

// Create or get talent_admin role
$talentAdminRole = Role::firstOrCreate(['name' => 'talent_admin']);

// Create talent admin user
$talentAdmin = User::firstOrCreate([
    'email' => 'talentadmin@test.com'
], [
    'name' => 'Talent Admin',
    'password' => bcrypt('password123'),
    'avatar' => 'images/default-avatar.png',
    'pekerjaan' => 'Talent Administrator',
    'is_active_talent' => true,
    'available_for_scouting' => false,
]);

// Assign role
if (!$talentAdmin->hasRole('talent_admin')) {
    $talentAdmin->assignRole('talent_admin');
    echo "✅ Assigned talent_admin role\n";
}

// Also assign admin role for full access
if (!$talentAdmin->hasRole('admin')) {
    $talentAdmin->assignRole('admin');
    echo "✅ Assigned admin role for full access\n";
}

echo "\n🎯 Talent Admin Created Successfully!\n";
echo "Email: talentadmin@test.com\n";
echo "Password: password123\n";
echo "Roles: " . implode(', ', $talentAdmin->getRoleNames()->toArray()) . "\n";
echo "\nYou can now access the talent discovery system at:\n";
echo "- Recruiter view: /recruiter/discovery\n";
echo "- Admin view: /admin/discovery\n";
