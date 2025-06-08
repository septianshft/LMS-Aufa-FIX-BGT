<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== RECRUITERS TABLE STRUCTURE ===\n";
$columns = DB::select("DESCRIBE recruiters");
foreach ($columns as $column) {
    echo "- {$column->Field} ({$column->Type})\n";
}

echo "\n=== SAMPLE RECRUITER DATA ===\n";
$recruiters = DB::table('recruiters')
    ->join('users', 'recruiters.user_id', '=', 'users.id')
    ->select('recruiters.*', 'users.name', 'users.email')
    ->limit(3)
    ->get();

foreach ($recruiters as $recruiter) {
    echo "ID: {$recruiter->id}\n";
    echo "User: {$recruiter->name} ({$recruiter->email})\n";
    foreach ($recruiter as $key => $value) {
        if (!in_array($key, ['id', 'name', 'email'])) {
            echo "{$key}: {$value}\n";
        }
    }
    echo "---\n";
}
?>
