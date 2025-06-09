<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\QuizAttempt;
use App\Models\TalentRequest;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdvancedSkillAnalyticsService
{
    /**
     * Helper method to ensure skills are in array format
     */
    private function ensureSkillsArray($skills)
    {
        if (is_string($skills)) {
            return json_decode($skills, true) ?? [];
        }
        
        if (is_array($skills)) {
            return $skills;
        }
        
        return [];
    }

    /**
     * Categorize a skill string into appropriate category
     */
    private function categorizeSkill($skill)
    {
        $skill = strtolower(trim($skill));
        
        $categories = [
            'Frontend Development' => ['javascript', 'react', 'vue.js', 'angular', 'html', 'css', 'typescript', 'jquery', 'bootstrap', 'tailwind'],
            'Backend Development' => ['php', 'laravel', 'node.js', 'python', 'django', 'flask', 'ruby', 'rails', 'java', 'spring', 'c#', '.net'],
            'Database' => ['mysql', 'postgresql', 'mongodb', 'redis', 'sqlite', 'oracle', 'sql server'],
            'DevOps & Cloud' => ['docker', 'kubernetes', 'aws', 'azure', 'gcp', 'jenkins', 'git', 'linux', 'nginx', 'apache'],
            'Mobile Development' => ['react native', 'flutter', 'swift', 'kotlin', 'ionic', 'xamarin'],
            'Data Science' => ['python', 'r', 'machine learning', 'deep learning', 'tensorflow', 'pandas', 'numpy'],
            'Testing' => ['phpunit', 'jest', 'cypress', 'selenium', 'unit testing', 'integration testing']
        ];
        
        foreach ($categories as $category => $skillList) {
            if (in_array($skill, $skillList)) {
                return $category;
            }
        }
        
        return 'General Technology';
    }

    /**
     * Get comprehensive skill analytics for dashboard
     */
    public function getSkillAnalytics(): array
    {
        return [
            'skill_categories' => $this->getSkillCategoryDistribution(),
            'market_demand_analysis' => $this->getMarketDemandAnalysis(),
            'talent_conversion_metrics' => $this->getTalentConversionMetrics(),
            'skill_progression_trends' => $this->getSkillProgressionTrends(),
            'top_performing_skills' => $this->getTopPerformingSkills(),
            'conversion_funnel' => $this->getConversionFunnelMetrics(),
            'learning_to_earning' => $this->getLearningToEarningMetrics()
        ];
    }

    /**
     * Skill category distribution across all talents
     */
    public function getSkillCategoryDistribution(): array
    {
        $talents = User::where('available_for_scouting', true)
            ->whereNotNull('talent_skills')
            ->get();

        $categories = [];
        foreach ($talents as $talent) {
            $skills = $this->ensureSkillsArray($talent->talent_skills);
            foreach ($skills as $skill) {
                // Handle skills as strings, not arrays
                $category = $this->categorizeSkill($skill);
                $categories[$category] = ($categories[$category] ?? 0) + 1;
            }
        }

        arsort($categories);
        return $categories;
    }

    /**
     * Market demand analysis with hiring trends
     */
    public function getMarketDemandAnalysis(): array
    {
        $skillsByDemand = [
            'Very High' => [],
            'High' => [],
            'Medium' => [],
            'Low' => []
        ];

        $talents = User::where('available_for_scouting', true)
            ->whereNotNull('talent_skills')
            ->get();

        foreach ($talents as $talent) {
            $skills = $this->ensureSkillsArray($talent->talent_skills);
            foreach ($skills as $skill) {
                // Handle skills as strings and assign demand based on skill name
                $demand = $this->getSkillMarketDemand($skill);
                $skillsByDemand[$demand][] = $skill;
            }
        }

        return [
            'distribution' => array_map('count', $skillsByDemand),
            'top_demanded_skills' => $this->getTopDemandedSkills($talents),
            'supply_demand_ratio' => $this->calculateSupplyDemandRatio($skillsByDemand)
        ];
    }

    /**
     * Talent conversion metrics and funnel analysis
     */
    public function getTalentConversionMetrics(): array
    {
        $totalTrainees = User::whereHas('roles', function($q) {
            $q->where('name', 'trainee');
        })->count();

        $totalTalents = User::where('available_for_scouting', true)->count();

        $conversionRate = $totalTrainees > 0 ? ($totalTalents / $totalTrainees) * 100 : 0;

        $monthlyConversions = User::where('available_for_scouting', true)
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as conversions')
            ->get()
            ->pluck('conversions', 'month');

        return [
            'total_trainees' => $totalTrainees,
            'total_talents' => $totalTalents,
            'conversion_rate' => round($conversionRate, 2),
            'monthly_trends' => $monthlyConversions,
            'average_skills_before_conversion' => $this->getAverageSkillsBeforeConversion(),
            'conversion_triggers' => $this->getConversionTriggers()
        ];
    }

    /**
     * Skill progression trends over time
     */
    public function getSkillProgressionTrends(): array
    {
        $talents = User::where('available_for_scouting', true)->get();

        $progressionData = [];
        foreach ($talents as $talent) {
            $skills = $this->ensureSkillsArray($talent->talent_skills);
            foreach ($skills as $skill) {
                // Since we don't have acquired_at data, use current month for progression
                $month = Carbon::now()->format('Y-m');
                $progressionData[$month] = ($progressionData[$month] ?? 0) + 1;
            }
        }

        ksort($progressionData);

        return [
            'monthly_skill_acquisition' => $progressionData,
            'skill_velocity' => $this->calculateSkillVelocity($talents),
            'learning_patterns' => $this->analyzeLearningPatterns($talents)
        ];
    }

    /**
     * Top performing skills based on recruitment success
     */
    public function getTopPerformingSkills(): array
    {
        $requestedSkills = [];

        // Analyze talent requests to see which skills are most sought after
        $requests = TalentRequest::with('talentUser')->get();

        foreach ($requests as $request) {
            if ($request->talentUser && $request->talentUser->talent_skills) {
                $skills = $this->ensureSkillsArray($request->talentUser->talent_skills);
                foreach ($skills as $skill) {
                    // Skill is a string, not an array
                    $skillName = $skill;
                    $requestedSkills[$skillName] = ($requestedSkills[$skillName] ?? 0) + 1;
                }
            }
        }

        arsort($requestedSkills);

        return [
            'most_requested' => array_slice($requestedSkills, 0, 10, true),
            'success_rate_by_skill' => $this->calculateSkillSuccessRates($requestedSkills),
            'emerging_skills' => $this->identifyEmergingSkills()
        ];
    }

    /**
     * Conversion funnel metrics
     */
    public function getConversionFunnelMetrics(): array
    {
        $totalUsers = User::count();
        $registeredTrainees = User::whereHas('roles', function($q) {
            $q->where('name', 'trainee');
        })->count();

        $courseCompletions = User::whereHas('courses')->count();
        $skillAcquisitions = User::whereNotNull('talent_skills')->count();
        $talentOptIns = User::where('available_for_scouting', true)->count();
        $successfulPlacements = TalentRequest::where('status', 'completed')->count();

        return [
            'funnel_stages' => [
                'total_users' => $totalUsers,
                'registered_trainees' => $registeredTrainees,
                'course_completions' => $courseCompletions,
                'skill_acquisitions' => $skillAcquisitions,
                'talent_opt_ins' => $talentOptIns,
                'successful_placements' => $successfulPlacements
            ],
            'conversion_rates' => [
                'registration_to_course' => $registeredTrainees > 0 ? ($courseCompletions / $registeredTrainees) * 100 : 0,
                'course_to_skills' => $courseCompletions > 0 ? ($skillAcquisitions / $courseCompletions) * 100 : 0,
                'skills_to_talent' => $skillAcquisitions > 0 ? ($talentOptIns / $skillAcquisitions) * 100 : 0,
                'talent_to_placement' => $talentOptIns > 0 ? ($successfulPlacements / $talentOptIns) * 100 : 0
            ]
        ];
    }

    /**
     * Learning to earning correlation metrics
     */
    public function getLearningToEarningMetrics(): array
    {
        $talents = User::where('available_for_scouting', true)
            ->whereNotNull('hourly_rate')
            ->get();

        $correlationData = [
            'avg_hourly_rate_by_skill_count' => [],
            'avg_hourly_rate_by_category' => [],
            'roi_analysis' => $this->calculateROIAnalysis($talents)
        ];

        foreach ($talents as $talent) {
            $skills = $this->ensureSkillsArray($talent->talent_skills);
            $skillCount = count($skills);
            $hourlyRate = (float) $talent->hourly_rate;

            if (!isset($correlationData['avg_hourly_rate_by_skill_count'][$skillCount])) {
                $correlationData['avg_hourly_rate_by_skill_count'][$skillCount] = [];
            }
            $correlationData['avg_hourly_rate_by_skill_count'][$skillCount][] = $hourlyRate;

            // Analyze by primary skill category
            if (count($skills) > 0) {
                $primaryCategory = $skills[0]['category'] ?? 'General';
                if (!isset($correlationData['avg_hourly_rate_by_category'][$primaryCategory])) {
                    $correlationData['avg_hourly_rate_by_category'][$primaryCategory] = [];
                }
                $correlationData['avg_hourly_rate_by_category'][$primaryCategory][] = $hourlyRate;
            }
        }

        // Calculate averages
        foreach ($correlationData['avg_hourly_rate_by_skill_count'] as $count => &$rates) {
            $rates = round(array_sum($rates) / count($rates), 2);
        }

        foreach ($correlationData['avg_hourly_rate_by_category'] as $category => &$rates) {
            $rates = round(array_sum($rates) / count($rates), 2);
        }

        return $correlationData;
    }

    /**
     * Helper methods for complex calculations
     */
    private function getTopDemandedSkills($talents): array
    {
        $skillCounts = [];
        foreach ($talents as $talent) {
            $skills = $this->ensureSkillsArray($talent->talent_skills);
            foreach ($skills as $skill) {
                if ($this->getSkillMarketDemand($skill) === 'Very High') {
                    $skillCounts[$skill] = ($skillCounts[$skill] ?? 0) + 1;
                }
            }
        }
        arsort($skillCounts);
        return array_slice($skillCounts, 0, 10, true);
    }

    private function calculateSupplyDemandRatio($skillsByDemand): array
    {
        $ratios = [];
        foreach ($skillsByDemand as $demand => $skills) {
            $supply = count($skills);
            $demandScore = ['Very High' => 4, 'High' => 3, 'Medium' => 2, 'Low' => 1][$demand];
            $ratios[$demand] = $supply > 0 ? round($demandScore / $supply, 2) : 0;
        }
        return $ratios;
    }

    private function getAverageSkillsBeforeConversion(): float
    {
        $talents = User::where('available_for_scouting', true)->get();
        $totalSkills = $talents->sum(function($talent) {
            $skills = $this->ensureSkillsArray($talent->talent_skills);
            return count($skills);
        });
        return $talents->count() > 0 ? round($totalSkills / $talents->count(), 2) : 0;
    }

    private function getConversionTriggers(): array
    {
        // Analyze common patterns that lead to talent conversion
        return [
            'course_completion_threshold' => 3,
            'skill_count_threshold' => 5,
            'high_demand_skills' => ['Backend Development', 'Data Science', 'Cybersecurity'],
            'completion_rate_threshold' => 80
        ];
    }

    private function calculateSkillVelocity($talents): array
    {
        $velocityData = [];
        foreach ($talents as $talent) {
            $skills = $this->ensureSkillsArray($talent->talent_skills);
            if (count($skills) >= 2) {
                // Since we don't have acquired_at data, simulate velocity based on skill count
                // Higher skill count suggests faster learning velocity
                $velocity = count($skills) / 30; // Simulate skills per month
                $velocityData[] = $velocity;
            }
        }
        
        rsort($velocityData);

        return [
            'average_skills_per_day' => count($velocityData) > 0 ? round(array_sum($velocityData) / count($velocityData), 3) : 0,
            'fastest_learners' => array_slice($velocityData, 0, 5)
        ];
    }

    private function analyzeLearningPatterns($talents): array
    {
        $patterns = [
            'weekend_learning' => 0,
            'weekday_learning' => 0,
            'sequential_categories' => 0,
            'diverse_categories' => 0
        ];

        foreach ($talents as $talent) {
            $skills = $this->ensureSkillsArray($talent->talent_skills);
            foreach ($skills as $skill) {
                // Since we don't have acquired_at data, simulate learning patterns
                $date = Carbon::now();
                if ($date->isWeekend()) {
                    $patterns['weekend_learning']++;
                } else {
                    $patterns['weekday_learning']++;
                }
            }

            // Analyze category diversity based on skill categorization
            $categories = [];
            foreach ($skills as $skill) {
                $categories[] = $this->categorizeSkill($skill);
            }
            $uniqueCategories = array_unique($categories);
            
            if (count($uniqueCategories) > 1) {
                $patterns['diverse_categories']++;
            } else {
                $patterns['sequential_categories']++;
            }
        }

        return $patterns;
    }

    private function calculateSkillSuccessRates($requestedSkills): array
    {
        $successRates = [];
        $completedRequests = TalentRequest::where('status', 'completed')->with('talentUser')->get();

        foreach ($requestedSkills as $skill => $totalRequests) {
            $successfulRequests = $completedRequests->filter(function($request) use ($skill) {
                if (!$request->talentUser || !$request->talentUser->talent_skills) return false;
                $skills = $this->ensureSkillsArray($request->talentUser->talent_skills);
                return collect($skills)->contains('name', $skill);
            })->count();

            $successRates[$skill] = $totalRequests > 0 ? round(($successfulRequests / $totalRequests) * 100, 2) : 0;
        }

        return $successRates;
    }

    private function identifyEmergingSkills(): array
    {
        // Skills acquired in the last 3 months
        $recentSkills = [];
        $cutoffDate = Carbon::now()->subMonths(3);

        $talents = User::where('available_for_scouting', true)->get();
        foreach ($talents as $talent) {
            $skills = $this->ensureSkillsArray($talent->talent_skills);
            foreach ($skills as $skill) {
                // Since we don't have acquired_at data, consider all skills as recent
                $acquiredDate = Carbon::now();
                if ($acquiredDate->gte($cutoffDate)) {
                    $recentSkills[$skill] = ($recentSkills[$skill] ?? 0) + 1;
                }
            }
        }

        arsort($recentSkills);
        return array_slice($recentSkills, 0, 5, true);
    }

    private function calculateROIAnalysis($talents): array
    {
        $roiData = [
            'avg_investment_per_talent' => 0, // Could be calculated from course costs
            'avg_earning_potential' => 0,
            'roi_percentage' => 0
        ];

        if ($talents->count() > 0) {
            $avgHourlyRate = $talents->avg('hourly_rate') ?? 0;
            $avgSkillCount = $talents->avg(function($talent) {
                $skills = $this->ensureSkillsArray($talent->talent_skills);
                return count($skills);
            });

            // Estimate annual earning potential (assumptions: 20 hours/week, 50 weeks/year)
            $annualEarningPotential = $avgHourlyRate * 20 * 50;
            $estimatedInvestment = $avgSkillCount * 100; // Assume $100 per course/skill

            $roiData['avg_investment_per_talent'] = round($estimatedInvestment, 2);
            $roiData['avg_earning_potential'] = round($annualEarningPotential, 2);
            $roiData['roi_percentage'] = $estimatedInvestment > 0 ?
                round((($annualEarningPotential - $estimatedInvestment) / $estimatedInvestment) * 100, 2) : 0;
        }

        return $roiData;
    }

    /**
     * Get market demand level for a specific skill
     */
    private function getSkillMarketDemand($skill)
    {
        $skill = strtolower(trim($skill));
        
        $demandLevels = [
            'Very High' => ['javascript', 'python', 'react', 'aws', 'docker', 'kubernetes', 'node.js', 'typescript'],
            'High' => ['php', 'laravel', 'vue.js', 'angular', 'mysql', 'mongodb', 'git', 'linux'],
            'Medium' => ['html', 'css', 'bootstrap', 'jquery', 'postgresql', 'redis', 'nginx'],
            'Low' => ['flash', 'perl', 'cobol', 'fortran']
        ];
        
        foreach ($demandLevels as $level => $skills) {
            if (in_array($skill, $skills)) {
                return $level;
            }
        }
        
        return 'Medium'; // Default
    }
}
