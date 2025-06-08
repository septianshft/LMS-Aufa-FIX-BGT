<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Collection;

class TalentMatchingService
{
    /**
     * Discover available talents based on filters
     */
    public function discoverTalents($filters = []): Collection
    {
        $query = User::where('available_for_scouting', true)
            ->where('is_active_talent', true)
            ->whereHas('roles', function($q) {
                $q->whereIn('name', ['trainee', 'talent']);
            });        // Filter by skills if provided
        if (isset($filters['skills']) && !empty($filters['skills'])) {
            $skills = is_array($filters['skills']) ? $filters['skills'] : [$filters['skills']];
            $query->where(function($q) use ($skills) {
                foreach ($skills as $skill) {
                    $q->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(talent_skills, "$[*].name")) LIKE ?', ['%' . $skill . '%']);
                }
            });
        }

        // Filter by skill level if provided
        if (isset($filters['level']) && !empty($filters['level'])) {
            $query->where(function($q) use ($filters) {
                $q->whereJsonContains('talent_skills', [['level' => strtolower($filters['level'])]]);
            });
        }

        // Filter by experience (number of completed courses/skills)
        if (isset($filters['min_experience'])) {
            $query->whereRaw('JSON_LENGTH(talent_skills) >= ?', [$filters['min_experience']]);
        }

        return $query->get()->map(function($user) {
            return $this->buildTalentProfile($user);
        });
    }

    /**
     * Find matching talents for specific project requirements
     */
    public function findMatchingTalents($projectRequirements): Collection
    {
        $requiredSkills = $this->parseSkillRequirements($projectRequirements);

        $talents = User::where('available_for_scouting', true)
            ->where('is_active_talent', true)
            ->whereNotNull('talent_skills')
            ->get();

        $matchedTalents = $talents->map(function($user) use ($requiredSkills) {
            $userSkills = collect($user->talent_skills ?? []);
            $matchScore = $this->calculateMatchScore($userSkills, $requiredSkills);

            if ($matchScore > 0) {
                $profile = $this->buildTalentProfile($user);
                $profile['match_score'] = $matchScore;
                $profile['matching_skills'] = $this->getMatchingSkills($userSkills, $requiredSkills);
                return $profile;
            }

            return null;
        })->filter()->sortByDesc('match_score');

        return $matchedTalents;
    }

    /**
     * Get talent recommendations for a recruiter based on their previous searches
     */
    public function getRecommendations($recruiterId, $limit = 10): Collection
    {
        // For now, return top talents by skill diversity
        // In future, this could use ML algorithms based on recruiter's history

        $talents = User::where('available_for_scouting', true)
            ->where('is_active_talent', true)
            ->whereNotNull('talent_skills')
            ->get();

        return $talents->map(function($user) {
            $profile = $this->buildTalentProfile($user);
            $profile['recommendation_score'] = $this->calculateRecommendationScore($user);
            return $profile;
        })->sortByDesc('recommendation_score')->take($limit);
    }    /**
     * Build a comprehensive talent profile
     */
    private function buildTalentProfile(User $user): array
    {
        $skills = collect($user->talent_skills ?? []);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'bio' => $user->talent_bio,
            'portfolio_url' => $user->portfolio_url,
            'hourly_rate' => $user->hourly_rate,
            'available_for_scouting' => $user->available_for_scouting,
            'is_active_talent' => $user->is_active_talent,
            'skills' => $skills->toArray(),
            'skill_count' => $skills->count(),
            'skill_levels' => $this->getSkillLevelDistribution($skills),
            'specializations' => $this->extractSpecializations($skills),
            'experience_level' => $this->calculateExperienceLevel($skills),
            'last_activity' => $user->updated_at,
        ];
    }

    /**
     * Calculate match score between user skills and required skills
     */
    private function calculateMatchScore($userSkills, $requiredSkills): float
    {
        if (empty($requiredSkills)) {
            return 0;
        }

        $userSkillNames = $userSkills->pluck('name')->map('strtolower');
        $matchedSkills = 0;
        $levelBonuses = 0;

        foreach ($requiredSkills as $required) {
            $skillName = strtolower($required['name']);

            if ($userSkillNames->contains($skillName)) {
                $matchedSkills++;

                // Bonus for matching or exceeding required level
                $userSkill = $userSkills->firstWhere('name', $required['name']);
                if ($userSkill && isset($userSkill['level'])) {
                    $levelBonus = $this->getLevelBonus($userSkill['level'], $required['level'] ?? 'beginner');
                    $levelBonuses += $levelBonus;
                }
            }
        }

        $baseScore = ($matchedSkills / count($requiredSkills)) * 100;
        $bonusScore = $levelBonuses * 10; // 10 points per level bonus

        return min(100, $baseScore + $bonusScore);
    }

    /**
     * Get matching skills between user and requirements
     */
    private function getMatchingSkills($userSkills, $requiredSkills): array
    {
        $matches = [];
        $userSkillNames = $userSkills->pluck('name')->map('strtolower');

        foreach ($requiredSkills as $required) {
            $skillName = strtolower($required['name']);

            if ($userSkillNames->contains($skillName)) {
                $userSkill = $userSkills->firstWhere('name', $required['name']);
                $matches[] = [
                    'skill' => $required['name'],
                    'user_level' => $userSkill['level'] ?? 'unknown',
                    'required_level' => $required['level'] ?? 'beginner',
                    'meets_requirement' => $this->meetsLevelRequirement(
                        $userSkill['level'] ?? 'beginner',
                        $required['level'] ?? 'beginner'
                    ),
                ];
            }
        }

        return $matches;
    }

    /**
     * Parse skill requirements from various formats
     */
    private function parseSkillRequirements($requirements): array
    {
        if (is_string($requirements)) {
            // Simple comma-separated format: "JavaScript, Python, React"
            $skills = explode(',', $requirements);
            return array_map(function($skill) {
                return ['name' => trim($skill), 'level' => 'beginner'];
            }, $skills);
        }

        if (is_array($requirements)) {
            // Already formatted array
            return $requirements;
        }

        return [];
    }

    /**
     * Calculate experience level based on skills
     */
    private function calculateExperienceLevel($skills): string
    {
        $skillCount = $skills->count();
        $advancedSkills = $skills->where('level', 'advanced')->count();
        $intermediateSkills = $skills->where('level', 'intermediate')->count();

        if ($advancedSkills >= 3 || $skillCount >= 10) {
            return 'expert';
        } elseif ($advancedSkills >= 1 || $intermediateSkills >= 3 || $skillCount >= 5) {
            return 'intermediate';
        } else {
            return 'beginner';
        }
    }

    /**
     * Get skill level distribution
     */
    private function getSkillLevelDistribution($skills): array
    {
        return [
            'beginner' => $skills->where('level', 'beginner')->count(),
            'intermediate' => $skills->where('level', 'intermediate')->count(),
            'advanced' => $skills->where('level', 'advanced')->count(),
        ];
    }

    /**
     * Extract specializations from skills
     */
    private function extractSpecializations($skills): array
    {
        // Group skills by common categories
        $categories = [
            'frontend' => ['javascript', 'react', 'vue', 'angular', 'html', 'css'],
            'backend' => ['php', 'python', 'java', 'node.js', 'laravel', 'django'],
            'data' => ['python', 'r', 'sql', 'data science', 'machine learning'],
            'mobile' => ['android', 'ios', 'react native', 'flutter'],
            'design' => ['ui/ux', 'photoshop', 'figma', 'design'],
        ];

        $specializations = [];
        $skillNames = $skills->pluck('name')->map('strtolower');

        foreach ($categories as $category => $keywords) {
            $matches = 0;
            foreach ($keywords as $keyword) {
                if ($skillNames->contains(function($skill) use ($keyword) {
                    return str_contains($skill, $keyword);
                })) {
                    $matches++;
                }
            }

            if ($matches >= 2) {
                $specializations[] = $category;
            }
        }

        return $specializations;
    }

    /**
     * Calculate recommendation score
     */
    private function calculateRecommendationScore($user): float
    {
        $skills = collect($user->talent_skills ?? []);
        $skillCount = $skills->count();
        $levelVariety = count(array_unique($skills->pluck('level')->toArray()));
        $recentActivity = $user->updated_at->diffInDays(now());

        // Score based on skill diversity, level variety, and recent activity
        $skillScore = min(50, $skillCount * 5);
        $varietyScore = $levelVariety * 10;
        $activityScore = max(0, 40 - $recentActivity);

        return $skillScore + $varietyScore + $activityScore;
    }

    /**
     * Get level bonus for matching or exceeding requirements
     */
    private function getLevelBonus($userLevel, $requiredLevel): float
    {
        $levels = ['beginner' => 1, 'intermediate' => 2, 'advanced' => 3];

        $userLevelNum = $levels[strtolower($userLevel)] ?? 1;
        $requiredLevelNum = $levels[strtolower($requiredLevel)] ?? 1;

        return max(0, $userLevelNum - $requiredLevelNum);
    }

    /**
     * Check if user skill level meets requirement
     */
    private function meetsLevelRequirement($userLevel, $requiredLevel): bool
    {
        $levels = ['beginner' => 1, 'intermediate' => 2, 'advanced' => 3];

        $userLevelNum = $levels[strtolower($userLevel)] ?? 1;
        $requiredLevelNum = $levels[strtolower($requiredLevel)] ?? 1;

        return $userLevelNum >= $requiredLevelNum;
    }
}
