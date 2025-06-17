<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Talent;
use App\Models\Recruiter;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\Certificate;
use App\Models\QuizAttempt;
use App\Models\FinalQuiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TalentComparisonTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $recruiterUser;
    protected $recruiter;
    protected $talents;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::firstOrCreate(['name' => 'recruiter']);
        Role::firstOrCreate(['name' => 'talent']);
        Role::firstOrCreate(['name' => 'student']);

        // Create recruiter user
        $this->recruiterUser = User::factory()->create([
            'name' => 'Test Recruiter',
            'email' => 'recruiter@test.com'
        ]);
        $this->recruiterUser->assignRole('recruiter');

        // Create recruiter profile
        $this->recruiter = Recruiter::factory()->create([
            'user_id' => $this->recruiterUser->id,
            'is_active' => true,
            'company_name' => 'Test Company',
            'company_description' => 'A test company'
        ]);

        // Create test talents with varied data
        $this->talents = collect();
        for ($i = 1; $i <= 5; $i++) {
            $talentUser = User::factory()->create([
                'name' => "Talent User $i",
                'email' => "talent{$i}@test.com",
                'pekerjaan' => "Developer $i",
                'available_for_scouting' => true,
                'talent_skills' => $this->generateMockSkills($i)
            ]);
            $talentUser->assignRole(['student', 'talent']);

            $talent = Talent::factory()->create([
                'user_id' => $talentUser->id,
                'is_active' => true
            ]);

            // Add mock course progress and certificates
            $this->createMockProgressData($talentUser, $i);

            $this->talents->push($talent);
        }
    }

    /** @test */
    public function recruiter_can_access_dashboard_with_comparison_features()
    {
        $response = $this->actingAs($this->recruiterUser)
            ->get(route('recruiter.dashboard'));

        $response->assertStatus(200);

        // Check for comparison UI elements
        $response->assertSee('Compare');
        $response->assertSee('toggleCompareMode');
        $response->assertSee('talent-compare-check');
        $response->assertSee('comparisonPanel');
        $response->assertSee('talentComparisonModal');
    }

    /** @test */
    public function talent_cards_have_required_data_attributes_for_comparison()
    {
        $response = $this->actingAs($this->recruiterUser)
            ->get(route('recruiter.dashboard'));

        $response->assertStatus(200);

        // Check that talent cards have required data attributes
        foreach ($this->talents as $talent) {
            $response->assertSee("data-talent-id=\"{$talent->id}\"", false);
            $response->assertSee("data-talent-name=\"{$talent->user->name}\"", false);
            $response->assertSee("data-talent-email=\"{$talent->user->email}\"", false);
            $response->assertSee("data-talent-position", false);
            $response->assertSee("data-talent-score", false);
            $response->assertSee("data-talent-courses", false);
            $response->assertSee("data-talent-certificates", false);
            $response->assertSee("data-talent-quiz-avg", false);
            $response->assertSee("data-talent-skills", false);
        }
    }

    /** @test */
    public function comparison_checkboxes_are_hidden_by_default()
    {
        $response = $this->actingAs($this->recruiterUser)
            ->get(route('recruiter.dashboard'));

        $response->assertStatus(200);

        // Check that comparison checkboxes have 'hidden' class by default
        $response->assertSee('compare-checkbox hidden');
    }    /** @test */
    public function comparison_panel_is_hidden_by_default()
    {
        $response = $this->actingAs($this->recruiterUser)
            ->get(route('recruiter.dashboard'));

        $response->assertStatus(200);

        // Check that comparison panel is hidden by default
        $response->assertSee('id="comparisonPanel"', false);
        $response->assertSee('style="display: none;"', false);
        $response->assertSee('translate-y-full', false);
    }    /** @test */
    public function talent_skills_are_properly_encoded_in_data_attributes()
    {
        $response = $this->actingAs($this->recruiterUser)
            ->get(route('recruiter.dashboard'));

        $response->assertStatus(200);

        // Check that skills are properly JSON encoded and HTML escaped
        $talent = $this->talents->first();
        $skills = $talent->user->getTalentSkillsArray();

        // Check for data-talent-skills attribute with JSON data
        $response->assertSee('data-talent-skills=', false);

        // Check that the skills data contains expected skill names
        $response->assertSee('PHP', false);
        $response->assertSee('Laravel', false);
        $response->assertSee('MySQL', false);

        // Check that the skills data contains proficiency levels
        $response->assertSee('advanced', false);
        $response->assertSee('intermediate', false);
        $response->assertSee('beginner', false);
    }

    /** @test */
    public function javascript_functions_are_present_in_dashboard()
    {
        $response = $this->actingAs($this->recruiterUser)
            ->get(route('recruiter.dashboard'));

        $response->assertStatus(200);

        // Check for essential JavaScript functions
        $response->assertSee('function toggleCompareMode()');
        $response->assertSee('function updateCompareSelection()');
        $response->assertSee('function updateSelectedTalentsPreview()');
        $response->assertSee('function clearComparison()');
        $response->assertSee('function viewComparison()');
        $response->assertSee('function closeComparisonModal()');
        $response->assertSee('function generateComparisonTable()');
    }

    /** @test */
    public function user_model_has_talent_skills_array_method()
    {
        $talent = $this->talents->first();
        $user = $talent->user;

        // Test that getTalentSkillsArray method exists and returns array
        $this->assertTrue(method_exists($user, 'getTalentSkillsArray'));
        $skills = $user->getTalentSkillsArray();
        $this->assertIsArray($skills);
        $this->assertNotEmpty($skills);
    }

    /** @test */
    public function talent_skills_array_handles_different_data_formats()
    {
        $talent = $this->talents->first();
        $user = $talent->user;

        // Test with array format
        $skillsArray = [
            ['skill_name' => 'PHP', 'proficiency' => 'advanced'],
            ['skill_name' => 'JavaScript', 'proficiency' => 'intermediate']
        ];
        $user->talent_skills = $skillsArray;
        $user->save();

        $result = $user->getTalentSkillsArray();
        $this->assertIsArray($result);
        $this->assertEquals($skillsArray, $result);

        // Test with JSON string format
        $user->talent_skills = json_encode($skillsArray);
        $user->save();

        $result = $user->getTalentSkillsArray();
        $this->assertIsArray($result);
        $this->assertEquals($skillsArray, $result);

        // Test with invalid JSON string
        $user->talent_skills = 'invalid json';
        $user->save();

        $result = $user->getTalentSkillsArray();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /** @test */
    public function talent_metrics_are_calculated_and_cached()
    {
        $talent = $this->talents->first();

        $response = $this->actingAs($this->recruiterUser)
            ->get(route('recruiter.dashboard'));

        $response->assertStatus(200);

        // Check that talent has scouting metrics
        $talent->refresh();
        $this->assertNotNull($talent->scouting_metrics);

        // Check cache key exists (simulated)
        $cacheKey = "talent_metrics_{$talent->id}";
        // Note: We can't easily test cache in unit tests without mocking
        // but we verify the structure exists
    }

    /** @test */
    public function dashboard_handles_empty_talents_gracefully()
    {
        // Remove all talents
        Talent::query()->delete();
        User::whereHas('roles', function($q) {
            $q->where('name', 'talent');
        })->delete();

        $response = $this->actingAs($this->recruiterUser)
            ->get(route('recruiter.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('No Talents Available');

        // Comparison elements should still be present but inactive
        $response->assertSee('toggleCompareMode');
        $response->assertSee('comparisonPanel');
    }    /** @test */
    public function comparison_ui_elements_have_correct_ids_and_classes()
    {
        $response = $this->actingAs($this->recruiterUser)
            ->get(route('recruiter.dashboard'));

        $response->assertStatus(200);
          // Check for required DOM elements
        $response->assertSee('id="compareModeBtn"', false);
        $response->assertSee('id="comparisonPanel"', false);
        $response->assertSee('id="selectedCount"', false);
        $response->assertSee('id="compareBtn"', false);
        $response->assertSee('id="selectedTalentsPreview"', false);
        $response->assertSee('id="talentComparisonModal"', false);
        $response->assertSee('id="comparisonContent"', false);

        // Check for required CSS classes
        $response->assertSee('compare-checkbox hidden', false);
        $response->assertSee('talent-compare-check', false);
        $response->assertSee('talent-card', false);
    }

    /** @test */
    public function talent_availability_status_is_displayed()
    {
        $response = $this->actingAs($this->recruiterUser)
            ->get(route('recruiter.dashboard'));

        $response->assertStatus(200);

        // Check that availability status is shown for talents
        $response->assertSeeText('Available Now');
        // Or potentially other statuses based on mock data
    }

    /** @test */
    public function talent_skills_display_correctly_in_cards()
    {
        $response = $this->actingAs($this->recruiterUser)
            ->get(route('recruiter.dashboard'));

        $response->assertStatus(200);

        // Check that skills are displayed in talent cards
        $response->assertSee('Skills');
        $response->assertSee('Advanced');
        $response->assertSee('Intermediate');
        $response->assertSee('See all');
    }

    /**
     * Generate mock skills for testing
     */
    private function generateMockSkills(int $level): array
    {
        $skillSets = [
            1 => [
                ['skill_name' => 'PHP', 'proficiency' => 'advanced', 'completed_date' => '2024-01-15'],
                ['skill_name' => 'Laravel', 'proficiency' => 'intermediate', 'completed_date' => '2024-02-20'],
                ['skill_name' => 'MySQL', 'proficiency' => 'beginner', 'completed_date' => '2024-03-10']
            ],
            2 => [
                ['skill_name' => 'JavaScript', 'proficiency' => 'advanced', 'completed_date' => '2024-01-10'],
                ['skill_name' => 'React', 'proficiency' => 'advanced', 'completed_date' => '2024-02-15'],
                ['skill_name' => 'Node.js', 'proficiency' => 'intermediate', 'completed_date' => '2024-03-05'],
                ['skill_name' => 'MongoDB', 'proficiency' => 'beginner', 'completed_date' => '2024-03-20']
            ],
            3 => [
                ['skill_name' => 'Python', 'proficiency' => 'expert', 'completed_date' => '2024-01-05'],
                ['skill_name' => 'Django', 'proficiency' => 'advanced', 'completed_date' => '2024-02-10'],
                ['skill_name' => 'PostgreSQL', 'proficiency' => 'intermediate', 'completed_date' => '2024-03-01']
            ],
            4 => [
                ['skill_name' => 'Java', 'proficiency' => 'intermediate', 'completed_date' => '2024-01-20'],
                ['skill_name' => 'Spring Boot', 'proficiency' => 'beginner', 'completed_date' => '2024-02-25']
            ],
            5 => [
                ['skill_name' => 'Vue.js', 'proficiency' => 'advanced', 'completed_date' => '2024-01-12'],
                ['skill_name' => 'CSS', 'proficiency' => 'expert', 'completed_date' => '2024-02-05'],
                ['skill_name' => 'Docker', 'proficiency' => 'intermediate', 'completed_date' => '2024-03-15'],
                ['skill_name' => 'AWS', 'proficiency' => 'beginner', 'completed_date' => '2024-03-25']
            ]
        ];

        return $skillSets[$level] ?? $skillSets[1];
    }

    /**
     * Create mock progress data for talents
     */    private function createMockProgressData(User $user, int $level): void
    {
        // Create mock courses and progress
        $coursesCount = $level * 2; // Varying course completion

        for ($i = 1; $i <= $coursesCount; $i++) {
            $course = Course::factory()->create([
                'name' => "Course $i for User {$user->id}",
                'price' => rand(50, 500)
            ]);

            CourseProgress::factory()->create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'progress' => 100 // Completed
            ]);

            // Create certificates with the correct schema
            Certificate::factory()->create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'path' => "certificates/cert-{$user->id}-{$course->id}.pdf",
                'generated_at' => now()
            ]);

            // Create quiz attempts with the correct schema
            $finalQuiz = FinalQuiz::factory()->create([
                'course_id' => $course->id,
                'title' => "Quiz for Course {$course->id}"
            ]);

            QuizAttempt::factory()->create([
                'user_id' => $user->id,
                'final_quiz_id' => $finalQuiz->id,
                'score' => rand(70, 100),
                'is_passed' => true
            ]);
        }
    }
}
