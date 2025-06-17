<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SimpleTalentComparisonTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_model_has_get_talent_skills_array_method()
    {
        $user = User::factory()->create();

        // Test that the method exists
        $this->assertTrue(method_exists($user, 'getTalentSkillsArray'));

        // Test that it returns an array
        $result = $user->getTalentSkillsArray();
        $this->assertIsArray($result);
    }

    /** @test */
    public function get_talent_skills_array_handles_json_string()
    {
        $user = User::factory()->create();

        $skills = [
            ['skill_name' => 'PHP', 'proficiency' => 'advanced'],
            ['skill_name' => 'JavaScript', 'proficiency' => 'intermediate']
        ];

        // Test with JSON string
        $user->talent_skills = json_encode($skills);
        $user->save();

        $result = $user->getTalentSkillsArray();
        $this->assertIsArray($result);
        $this->assertEquals($skills, $result);
    }

    /** @test */
    public function get_talent_skills_array_handles_array()
    {
        $user = User::factory()->create();

        $skills = [
            ['skill_name' => 'PHP', 'proficiency' => 'advanced'],
            ['skill_name' => 'JavaScript', 'proficiency' => 'intermediate']
        ];

        // Test with array
        $user->talent_skills = $skills;
        $user->save();

        $result = $user->getTalentSkillsArray();
        $this->assertIsArray($result);
        $this->assertEquals($skills, $result);
    }

    /** @test */
    public function get_talent_skills_array_handles_invalid_data()
    {
        $user = User::factory()->create();

        // Test with invalid JSON
        $user->talent_skills = 'invalid json string';
        $user->save();

        $result = $user->getTalentSkillsArray();
        $this->assertIsArray($result);
        $this->assertEmpty($result);

        // Test with null
        $user->talent_skills = null;
        $user->save();

        $result = $user->getTalentSkillsArray();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
