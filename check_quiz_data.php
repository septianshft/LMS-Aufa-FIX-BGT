<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking quiz-related data...\n";

// Check FinalQuiz
$finalQuizzes = \App\Models\FinalQuiz::all();
echo "FinalQuiz records: " . $finalQuizzes->count() . "\n";

// Check QuizAttempts
$quizAttempts = \App\Models\QuizAttempt::all();
echo "QuizAttempt records: " . $quizAttempts->count() . "\n";

// Check the trainee user
$trainee = \App\Models\User::where('email', 'trainee@test.com')->first();
if ($trainee) {
    echo "\nTrainee found: {$trainee->name}\n";

    // Check their quiz attempts
    $attempts = \App\Models\QuizAttempt::where('user_id', $trainee->id)->get();
    echo "Trainee quiz attempts: " . $attempts->count() . "\n";

    if ($attempts->count() > 0) {
        foreach ($attempts as $attempt) {
            echo "- Quiz ID: {$attempt->final_quiz_id}, Score: {$attempt->score}\n";
        }
    } else {
        echo "❌ No quiz attempts found for trainee!\n";
        echo "This is why quiz average is 0.\n";
    }
} else {
    echo "❌ Trainee not found\n";
}

echo "\nDone!\n";
