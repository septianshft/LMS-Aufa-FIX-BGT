<?php
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->boot();

use App\Models\User;
use App\Models\Course;

// Test skill addition
echo "Testing skill addition...\n";

$user = User::where('email', 'trainee@test.com')->first();
$course = Course::first();

if ($course && $user) {
    echo "User: {$user->name}\n";
    echo "Course: {$course->name}\n";

    echo "Skills before: " . count($user->talent_skills ?? []) . "\n";

    $user->addSkillFromCourse($course);

    echo "Skills after: " . count($user->fresh()->talent_skills ?? []) . "\n";
    echo "Skills: " . json_encode($user->fresh()->talent_skills) . "\n";
} else {
    echo "User or Course not found\n";
}
