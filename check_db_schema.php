<?php
// Check users table structure
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Checking Users Table Structure ===" . PHP_EOL;

try {
    $columns = DB::select('SHOW COLUMNS FROM users');

    echo "Columns in users table:" . PHP_EOL;
    foreach ($columns as $column) {
        echo "- " . $column->Field . " (" . $column->Type . ")" . PHP_EOL;
    }

    echo PHP_EOL . "Checking for talent-related columns..." . PHP_EOL;

    $talentColumns = ['available_for_scouting', 'talent_skills', 'talent_bio', 'talent_portfolio_url', 'talent_github_url', 'talent_linkedin_url'];

    foreach ($talentColumns as $columnName) {
        if (Schema::hasColumn('users', $columnName)) {
            echo "✓ Column '$columnName' exists" . PHP_EOL;
        } else {
            echo "✗ Column '$columnName' MISSING" . PHP_EOL;
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
