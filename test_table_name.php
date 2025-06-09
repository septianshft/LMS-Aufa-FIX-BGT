<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $model = new App\Models\CourseProgress();
    echo "CourseProgress model table name: " . $model->getTable() . "\n";

    // Check if tables exist
    echo "course_progresses exists: " . (Illuminate\Support\Facades\Schema::hasTable('course_progresses') ? 'YES' : 'NO') . "\n";
    echo "course_progress exists: " . (Illuminate\Support\Facades\Schema::hasTable('course_progress') ? 'YES' : 'NO') . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
