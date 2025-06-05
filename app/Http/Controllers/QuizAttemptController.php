<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\FinalQuiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class QuizAttemptController extends Controller
{
    public function show(Course $course) // Laravel akan otomatis mengambil objek Course berdasarkan ID dari URL
    {
        $quiz = FinalQuiz::where('course_id', $course->id)
                         ->with('questions.options') // Pastikan relasi 'options' ada di model QuizQuestion
                         ->firstOrFail(); // Ambil kuis pertama yang terkait dengan kursus ini

        // Mengirim data ke view 'front.quiz.blade.php'
        return view('front.quiz', compact('quiz', 'course'));
    }

    public function submit(Request $request, $quizId) // $quizId akan diisi dengan nilai dari {quiz} di URL
    {
        $quiz = FinalQuiz::with('questions.options')->findOrFail($quizId);
        $correct = 0;
        $total = count($quiz->questions);

        foreach ($quiz->questions as $question) {
            $userAnswer = $request->answers[$question->id] ?? null;
            if ($question->options && $question->options->where('is_correct', true)->first() && $question->options->where('is_correct', true)->first()->id == $userAnswer) {
                $correct++;
            }
        }

        $score = ($total > 0) ? round(($correct / $total) * 100) : 0;
        $passed = $score >= $quiz->passing_score;

        QuizAttempt::create([
            'final_quiz_id' => $quiz->id,
            'user_id' => auth()->user()->id,
            'score' => $score,
            'is_passed' => $passed,
        ]);
        
        // Redirect kembali dengan membawa hasil
        return redirect()->back()->with('result', compact('score', 'passed', 'quiz'));
    }
}