<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Concerns\HasUniqueStringIds;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'avatar',
        'pekerjaan',
        'email',
        'password',
        // Talent scouting fields
        'available_for_scouting',
        'talent_skills',
        'hourly_rate',
        'talent_bio',
        'portfolio_url',
        'location',
        'phone',
        'experience_level',
        'is_active_talent',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'talent_skills' => 'array',
            'available_for_scouting' => 'boolean',
            'is_active_talent' => 'boolean',
            'hourly_rate' => 'decimal:2',
        ];
    }

    public function courses(){
        return $this->belongsToMany(Course::class, 'course_trainees');
    }

    public function subscribe_transaction(){
        return $this->hasMany(SubscribeTransaction::class);
    }

    public function hasActiveSubscription(?Course $course = null)
    {
        $query = SubscribeTransaction::where('user_id', $this->id)
            ->where('is_paid', true);

        if ($course) {
            $query->where('course_id', $course->id); // hanya boleh akses kelas yang dibayarkan
        }

        return $query->exists();
    }

    public function trainer()
    {
        return $this->hasOne(Trainer::class);
    }

    public function recruiter()
    {
        return $this->hasOne(Recruiter::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    // TALENT SCOUTING INTEGRATION METHODS

    /**
     * Add skill from course completion with enhanced categorization
     */
    public function addSkillFromCourse($course)
    {
        $existingSkills = $this->talent_skills ?? [];

        // Enhanced skill categorization
        $category = $this->categorizeSkill($course);
        $level = $this->calculateSkillLevel($course);

        $skillName = $course->name;

        // Check if skill already exists
        $existingSkillIndex = collect($existingSkills)->search(function ($skill) use ($skillName) {
            return $skill['name'] === $skillName;
        });

        if ($existingSkillIndex !== false) {
            // Update existing skill with higher level if applicable
            $existingLevel = $existingSkills[$existingSkillIndex]['level'];
            if ($this->getSkillLevelNumber($level) > $this->getSkillLevelNumber($existingLevel)) {
                $existingSkills[$existingSkillIndex]['level'] = $level;
                $existingSkills[$existingSkillIndex]['updated_at'] = now()->toDateString();
            }
        } else {
            // Add new skill with enhanced metadata
            $existingSkills[] = [
                'name' => $skillName,
                'level' => $level,
                'category' => $category,
                'acquired_from' => 'Course Completion',
                'course_id' => $course->id,
                'acquired_at' => now()->toDateString(),
                'updated_at' => now()->toDateString(),
                'market_demand' => $this->getMarketDemand($category),
                'verified' => true
            ];
        }

        $this->update(['talent_skills' => $existingSkills]);

        // Trigger conversion suggestion if conditions are met
        $this->checkConversionSuggestion();
    }

    /**
     * Enhanced skill categorization
     */
    private function categorizeSkill($course)
    {
        $skillCategories = [
            'web development' => 'Frontend Development',
            'backend' => 'Backend Development',
            'database' => 'Database Management',
            'mobile' => 'Mobile Development',
            'ui/ux' => 'UI/UX Design',
            'data' => 'Data Science',
            'digital marketing' => 'Digital Marketing',
            'project management' => 'Project Management',
            'cybersecurity' => 'Cybersecurity',
            'cloud' => 'Cloud Computing'
        ];

        $courseName = strtolower($course->name);
        $courseCategory = $course->category ? strtolower($course->category->name) : '';

        foreach ($skillCategories as $keyword => $category) {
            if (str_contains($courseName, $keyword) || str_contains($courseCategory, $keyword)) {
                return $category;
            }
        }

        return 'General Technology';
    }

    /**
     * Calculate skill level based on course difficulty and completion
     */
    private function calculateSkillLevel($course)
    {
        if ($course->level) {
            $levelMap = [
                'beginner' => 'Beginner',
                'intermediate' => 'Intermediate',
                'advanced' => 'Advanced',
                'expert' => 'Expert'
            ];
            return $levelMap[strtolower($course->level->name)] ?? 'Intermediate';
        }

        return 'Intermediate';
    }

    /**
     * Get skill level as number for comparison
     */
    private function getSkillLevelNumber($level)
    {
        $levels = ['Beginner' => 1, 'Intermediate' => 2, 'Advanced' => 3, 'Expert' => 4];
        return $levels[$level] ?? 2;
    }

    /**
     * Get market demand indicator for skill category
     */
    private function getMarketDemand($category)
    {
        $demandMap = [
            'Frontend Development' => 'High',
            'Backend Development' => 'Very High',
            'Mobile Development' => 'High',
            'Data Science' => 'Very High',
            'UI/UX Design' => 'High',
            'Cybersecurity' => 'Very High',
            'Cloud Computing' => 'Very High',
            'Digital Marketing' => 'Medium',
            'Project Management' => 'High',
            'Database Management' => 'High'
        ];

        return $demandMap[$category] ?? 'Medium';
    }

    /**
     * Check if user should be suggested for talent conversion
     */
    private function checkConversionSuggestion()
    {
        if ($this->available_for_scouting) {
            return; // Already opted in
        }

        $skillCount = count($this->talent_skills ?? []);
        $courseCount = $this->completedCourses()->count();

        // Enhanced conversion criteria
        $shouldSuggest = $skillCount >= 3 ||
                        $courseCount >= 5 ||
                        $this->hasHighDemandSkills();

        if ($shouldSuggest) {
            session()->flash('smart_talent_suggestion', [
                'message' => 'Great progress! You\'ve gained valuable skills that are in high demand. Consider joining our talent platform to connect with recruiters.',
                'action_url' => route('profile.edit') . '#talent-settings',
                'skill_count' => $skillCount,
                'reason' => $this->getConversionReason($skillCount, $courseCount)
            ]);
        }
    }

    /**
     * Check if user has high-demand skills
     */
    private function hasHighDemandSkills()
    {
        $skills = $this->talent_skills ?? [];
        foreach ($skills as $skill) {
            if (isset($skill['market_demand']) && in_array($skill['market_demand'], ['High', 'Very High'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get personalized conversion reason
     */
    private function getConversionReason($skillCount, $courseCount)
    {
        if ($skillCount >= 5) {
            return 'You have ' . $skillCount . ' verified skills - perfect for attracting recruiters!';
        }
        if ($courseCount >= 5) {
            return 'You\'ve completed ' . $courseCount . ' courses - show your dedication to employers!';
        }
        if ($this->hasHighDemandSkills()) {
            return 'Your skills are in high market demand - great earning potential!';
        }
        return 'Your learning progress is impressive - time to monetize your skills!';
    }

    /**
     * Get completed courses relationship
     */
    public function completedCourses()
    {
        return $this->belongsToMany(Course::class, 'course_trainees')
                   ->whereExists(function($query) {
                       $query->select(DB::raw(1))
                             ->from('course_progresses')
                             ->whereColumn('course_progresses.course_id', 'courses.id')
                             ->where('course_progresses.user_id', $this->id)
                             ->where('course_progresses.progress', 100);
                   });
    }

    /**
     * Get course progress relationship
     */
    public function courseProgress()
    {
        return $this->hasMany(CourseProgress::class);
    }

    /**
     * Enable talent scouting with enhanced onboarding
     */
    public function enableTalentScouting($additionalData = [])
    {
        $updateData = array_merge([
            'available_for_scouting' => true,
            'is_active_talent' => true,
        ], $additionalData);

        $this->update($updateData);

        // Assign talent role if not already assigned
        if (!$this->hasRole('talent')) {
            $this->assignRole('talent');
        }

        // Create or update talent record
        if (!$this->talent) {
            $this->talent()->create(['is_active' => true]);
        } else {
            $this->talent->update(['is_active' => true]);
        }
    }

    /**
     * Disable talent scouting
     */
    public function disableTalentScouting()
    {
        $this->update([
            'available_for_scouting' => false,
            'is_active_talent' => false,
        ]);

        if ($this->talent) {
            $this->talent->update(['is_active' => false]);
        }
    }

    /**
     * Check if user is available for talent scouting
     */
    public function isAvailableForScouting()
    {
        return $this->available_for_scouting && $this->is_active_talent;
    }

    /**
     * Get talent relationship
     */
    public function talent()
    {
        return $this->hasOne(Talent::class);
    }

    /**
     * Get skills organized by category
     */
    public function getSkillsByCategory()
    {
        $skills = $this->talent_skills ?? [];
        $categorized = [];

        foreach ($skills as $skill) {
            $category = $skill['category'] ?? 'General Technology';
            if (!isset($categorized[$category])) {
                $categorized[$category] = [];
            }
            $categorized[$category][] = $skill;
        }

        return $categorized;
    }

    /**
     * Calculate talent readiness score
     */
    public function getTalentReadinessScore()
    {
        $score = 0;
        $skillCount = count($this->talent_skills ?? []);
        $completedCourses = $this->completedCourses()->count();

        // Skills contribute 40% of score
        $score += min(($skillCount * 8), 40);

        // Course completions contribute 30% of score
        $score += min(($completedCourses * 6), 30);

        // High-demand skills contribute 20% of score
        $highDemandCount = 0;
        foreach ($this->talent_skills ?? [] as $skill) {
            if (($skill['market_demand'] ?? '') === 'Very High') {
                $highDemandCount++;
            }
        }
        $score += min(($highDemandCount * 10), 20);

        // Recent activity contributes 10% of score
        $recentSkills = array_filter($this->talent_skills ?? [], function($skill) {
            $acquiredDate = \Carbon\Carbon::parse($skill['acquired_at'] ?? now());
            return $acquiredDate->gte(\Carbon\Carbon::now()->subMonths(3));
        });
        $score += min((count($recentSkills) * 5), 10);

        return min($score, 100);
    }

    /**
     * Get learning velocity (skills per month)
     */
    public function getLearningVelocity()
    {
        $skills = $this->talent_skills ?? [];
        if (count($skills) < 2) return 0;

        $dates = array_map(function($skill) {
            return \Carbon\Carbon::parse($skill['acquired_at'] ?? now());
        }, $skills);

        sort($dates);
        $monthsDiff = $dates[0]->diffInMonths(end($dates));
        if ($monthsDiff == 0) $monthsDiff = 1;

        return round(count($skills) / $monthsDiff, 2);
    }

    /**
     * Get skill progress analytics
     */
    public function getSkillAnalytics()
    {
        $skills = $this->talent_skills ?? [];
        $categories = collect($skills)->groupBy('category');

        return [
            'total_skills' => count($skills),
            'categories_count' => $categories->count(),
            'high_demand_skills' => collect($skills)->where('market_demand', 'Very High')->count(),
            'skill_levels' => collect($skills)->groupBy('level')->map->count(),
            'recent_skills' => collect($skills)->where('acquired_at', '>=', now()->subDays(30)->toDateString())->count()
        ];
    }

    /**
     * Get user's primary skill category for analytics
     * Phase 1 Enhancement Method
     */
    public function getSkillCategory(): string
    {
        $skills = $this->talent_skills ?? [];
        if (empty($skills)) {
            return 'General';
        }

        // Count categories
        $categories = [];
        foreach ($skills as $skill) {
            $category = $skill['category'] ?? 'General Technology';
            $categories[$category] = ($categories[$category] ?? 0) + 1;
        }

        // Return the most common category
        return array_keys($categories)[0] ?? 'General';
    }

    /**
     * Calculate readiness score for talent conversion
     * Phase 1 Enhancement Method
     */
    public function calculateReadinessScore(): float
    {
        $score = 0;

        // Course completion factor (40% weight)
        $completedCourses = $this->courseProgress()->where('progress', 100)->count();
        $totalCourses = $this->courseProgress()->count();
        if ($totalCourses > 0) {
            $completionRate = $completedCourses / $totalCourses;
            $score += $completionRate * 40;
        }

        // Quiz performance factor (30% weight)
        $quizAverage = $this->getQuizAverage();
        $score += ($quizAverage / 100) * 30;

        // Skills factor (20% weight)
        $skillCount = count($this->talent_skills ?? []);
        $score += min($skillCount * 4, 20); // Cap at 20 points

        // Recent activity factor (10% weight)
        $recentActivity = $this->courseProgress()
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();
        $score += min($recentActivity * 2, 10); // Cap at 10 points

        return round($score, 2);
    }

    /**
     * Get user's average quiz score
     * Phase 1 Enhancement Method
     */
    private function getQuizAverage(): float
    {
        $quizAttempts = $this->quizAttempts()
            ->where('is_passed', true)
            ->get();

        if ($quizAttempts->isEmpty()) {
            return 0;
        }

        $totalScore = $quizAttempts->sum('score');
        return round($totalScore / $quizAttempts->count(), 2);
    }

    /**
     * Get quiz attempts relationship
     */
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // PHASE 1: ENHANCED ANALYTICS COMPATIBILITY METHODS

    /**
     * Get conversion readiness score (alias for calculateReadinessScore)
     */
    public function getConversionReadinessScore(): float
    {
        return $this->calculateReadinessScore();
    }

    /**
     * Get skill categories for analytics
     */
    public function getSkillCategories(): array
    {
        $skills = $this->talent_skills ?? [];
        $categories = [];

        foreach ($skills as $skill) {
            $category = $skill['category'] ?? 'General Technology';
            if (!in_array($category, $categories)) {
                $categories[] = $category;
            }
        }

        return $categories;
    }



    /**
     * Get conversion suggestion status
     */
    public function shouldSuggestTalentConversion(): bool
    {
        // Don't suggest if already a talent
        if ($this->hasRole('talent')) {
            return false;
        }

        // Don't suggest if available for scouting
        if ($this->available_for_scouting) {
            return false;
        }

        // Suggest if high readiness score and multiple completed courses
        $readinessScore = $this->calculateReadinessScore();
        $completedCourses = $this->courseProgress()->where('progress', 100)->count();

        return $readinessScore >= 70 && $completedCourses >= 2;
    }

    /**
     * Get readiness level label
     */
    public function getReadinessLevel(): string
    {
        $score = $this->calculateReadinessScore();

        if ($score >= 85) return 'Excellent';
        if ($score >= 70) return 'High';
        if ($score >= 55) return 'Medium';
        if ($score >= 40) return 'Low';
        return 'Very Low';
    }

    /**
     * Get talent conversion metrics for analytics
     */
    public function getConversionMetrics(): array
    {
        return [
            'readiness_score' => $this->calculateReadinessScore(),
            'readiness_level' => $this->getReadinessLevel(),
            'completed_courses' => $this->courseProgress()->where('progress', 100)->count(),
            'skill_count' => count($this->talent_skills ?? []),
            'skill_categories' => $this->getSkillCategories(),
            'quiz_average' => $this->getQuizAverage(),
            'learning_velocity' => $this->getLearningVelocity(),
            'conversion_suggested' => $this->shouldSuggestTalentConversion(),
            'is_talent' => $this->hasRole('talent'),
            'available_for_scouting' => $this->available_for_scouting
        ];
    }
}
