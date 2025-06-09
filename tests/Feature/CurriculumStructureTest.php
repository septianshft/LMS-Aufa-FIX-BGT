<?php

namespace Tests\Feature;

use App\Models\{Course, CourseModule, CourseVideo, CourseMaterial, User, SubscribeTransaction, CourseProgress};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CurriculumStructureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'trainee']);
    }

    public function test_modules_videos_and_materials_can_be_created_and_ordered(): void
    {
        $course = Course::factory()->create();

        $moduleA = CourseModule::factory()->create(['course_id' => $course->id, 'order' => 2]);
        $moduleB = CourseModule::factory()->create(['course_id' => $course->id, 'order' => 1]);

        $video1 = CourseVideo::factory()->create(['course_id' => $course->id]);
        $video2 = CourseVideo::factory()->create(['course_id' => $course->id]);

        $material1 = CourseMaterial::factory()->create(['course_module_id' => $moduleB->id, 'order' => 2]);
        $material2 = CourseMaterial::factory()->create(['course_module_id' => $moduleB->id, 'order' => 1]);

        $orderedModules = CourseModule::where('course_id', $course->id)->orderBy('order')->pluck('id')->toArray();
        $this->assertSame([$moduleB->id, $moduleA->id], $orderedModules);

        $orderedMaterials = $moduleB->materials()->orderBy('order')->pluck('id')->toArray();
        $this->assertSame([$material2->id, $material1->id], $orderedMaterials);
    }

    public function test_learner_access_and_progress_tracking(): void
    {
        $user = User::factory()->create();
        $user->assignRole('trainee');

        $course = Course::factory()->create(['price' => 100]);
        $videos = CourseVideo::factory()->count(2)->create(['course_id' => $course->id]);

        SubscribeTransaction::create([
            'total_amount' => 100,
            'is_paid' => true,
            'user_id' => $user->id,
            'course_id' => $course->id,
            'proof' => 'proof.png',
            'subscription_start_date' => now(),
        ]);

        $this->actingAs($user)->get(route('front.learning', [$course->id, $videos[0]->id]))->assertStatus(200);
        $progress = CourseProgress::where('user_id', $user->id)->where('course_id', $course->id)->first();
        $this->assertNotNull($progress);
        $this->assertEquals(50, $progress->progress);
        $this->assertCount(1, $progress->completed_videos);

        $this->actingAs($user)->get(route('front.learning', [$course->id, $videos[1]->id]))->assertStatus(200);
        $progress->refresh();
        $this->assertEquals(100, $progress->progress);
        $this->assertCount(2, $progress->completed_videos);
    }

    public function test_learner_without_access_is_redirected(): void
    {
        $user = User::factory()->create();
        $user->assignRole('trainee');

        $course = Course::factory()->create(['price' => 100]);
        $video = CourseVideo::factory()->create(['course_id' => $course->id]);

        $response = $this->actingAs($user)->get(route('front.learning', [$course->id, $video->id]));
        $response->assertRedirect(route('front.pricing', ['course' => $course->slug]));
    }
}
