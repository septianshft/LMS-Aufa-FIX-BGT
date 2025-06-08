<?php

namespace Tests\Feature;

use App\Models\{User, Course, CourseVideo, FinalQuiz, QuizQuestion, QuizOption, Certificate};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_certificate_generated_and_downloadable(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $course = Course::factory()->create();
        $video = CourseVideo::factory()->create(['course_id' => $course->id]);

        $quiz = FinalQuiz::factory()->create(['course_id' => $course->id, 'passing_score' => 50]);
        $question = QuizQuestion::factory()->create(['final_quiz_id' => $quiz->id]);
        $option = QuizOption::factory()->create(['quiz_question_id' => $question->id, 'is_correct' => true]);

        $this->actingAs($user);

        // Complete video
        $this->get(route('front.learning', ['course' => $course->id, 'courseVideoId' => $video->id]));

        // Pass quiz
        $this->post(route('learning.quiz.submit', ['quiz' => $quiz->id]), [
            'answers' => [$question->id => $option->id],
        ]);

        $this->assertDatabaseHas('certificates', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $certificate = Certificate::first();
        $this->assertTrue(Storage::disk('public')->exists($certificate->path));

        $response = $this->get(route('certificate.download', $certificate));
        $response->assertOk();
    }
}
