<?php

namespace Tests\Feature;

use App\Models\{Course, CourseModule, CourseVideo, CourseMaterial, ModuleTask, User, SubscribeTransaction, CourseProgress};
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
        $module = CourseModule::factory()->create(['course_id' => $course->id]);
        $videos = CourseVideo::factory()->count(1)->create(['course_id' => $course->id, 'course_module_id' => $module->id]);
        $material = CourseMaterial::factory()->create(['course_module_id' => $module->id]);
        $task = ModuleTask::factory()->create(['course_module_id' => $module->id]);

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
        $this->assertEquals(0, $progress->progress);
        $this->assertCount(0, $progress->completed_videos);

        $this->post(route('learning.item.complete', [$course->id, $videos[0]->id]), ['type' => 'video']);
        $progress->refresh();
        $this->assertEquals(33, $progress->progress);
        $this->assertCount(1, $progress->completed_videos);

        $this->post(route('learning.item.complete', [$course->id, $material->id]), ['type' => 'material']);
        $progress->refresh();
        $this->assertEquals(66, $progress->progress);
        $this->assertCount(1, $progress->completed_materials);

        $this->post(route('task.submit.store', $task->id), ['answer' => 'done']);
        $progress->refresh();
        $this->assertEquals(100, $progress->progress);
        $this->assertCount(1, $progress->completed_tasks);
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
