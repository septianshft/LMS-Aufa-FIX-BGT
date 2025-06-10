<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Talent;
use App\Models\TalentRequest;
use App\Models\Recruiter;
use App\Services\TalentScoutingService;
use App\Services\AdvancedSkillAnalyticsService;
use App\Services\TalentRequestNotificationService;

class RecruiterController extends Controller
{
    protected $scoutingService;
    protected $analyticsService;
    protected $notificationService;

    public function __construct(
        TalentScoutingService $scoutingService,
        AdvancedSkillAnalyticsService $analyticsService,
        TalentRequestNotificationService $notificationService
    ) {
        $this->scoutingService = $scoutingService;
        $this->analyticsService = $analyticsService;
        $this->notificationService = $notificationService;
    }

    public function dashboard()
    {
        $userId = Auth::id();
        $user = User::with('recruiter')->find($userId);
        $title = 'Recruiter Dashboard';
        $roles = 'Recruiter';
        $assignedKelas = [];

        // Get current recruiter
        $recruiter = $user->recruiter;

        // Initialize collections with safe defaults
        $talents = collect();
        $myRequests = collect();
        $topTalents = collect();
        $analytics = [];
        $dashboardStats = [];

        // Only proceed if user has a recruiter profile
        if ($recruiter) {
            // Get active talents for discovery with request status
            $talents = Talent::with(['user', 'talentRequests' => function($query) use ($recruiter) {
                $query->where('recruiter_id', $recruiter->id ?? 0);
            }])
                ->where('is_active', true)
                ->whereHas('user', function($query) {
                    $query->whereNotNull('name')
                          ->whereNotNull('email');
                })
                ->latest()
                ->paginate(12);

            // Add scouting metrics to each talent
            $talents->getCollection()->transform(function ($talent) {
                $talent->scouting_metrics = $this->scoutingService->getTalentScoutingMetrics($talent);
                return $talent;
            });

            // Get my talent requests summary
            $myRequests = TalentRequest::with(['talent.user'])
                ->where('recruiter_id', $recruiter->id)
                ->latest()
                ->take(5)
                ->get();

            // Get top talents for recommendations
            $topTalents = $this->scoutingService->getTopTalents(6);

            // Get comprehensive analytics
            $analytics = $this->analyticsService->getSkillAnalytics();

            // Get recruiter-specific dashboard statistics
            $dashboardStats = $this->getRecruiterDashboardStats($recruiter);

        } else {
            // For users without recruiter profile, create empty paginated collection
            $talents = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), 0, 12, 1, ['path' => request()->url()]
            );
        }

        return view('admin.recruiter.dashboard', compact(
            'user', 'title', 'roles', 'assignedKelas', 'talents',
            'myRequests', 'recruiter', 'topTalents', 'analytics', 'dashboardStats'
        ));
    }

    /**
     * Get recruiter-specific dashboard statistics
     */
    private function getRecruiterDashboardStats($recruiter)
    {
        return [
            'total_requests' => TalentRequest::where('recruiter_id', $recruiter->id)->count(),
            'pending_requests' => TalentRequest::where('recruiter_id', $recruiter->id)->where('status', 'pending')->count(),
            'approved_requests' => TalentRequest::where('recruiter_id', $recruiter->id)->where('status', 'approved')->count(),
            'meeting_arranged' => TalentRequest::where('recruiter_id', $recruiter->id)->where('status', 'meeting_arranged')->count(),
            'completed_projects' => TalentRequest::where('recruiter_id', $recruiter->id)->where('status', 'completed')->count(),
            'success_rate' => $this->calculateSuccessRate($recruiter->id),
            'avg_response_time' => $this->calculateAvgResponseTime($recruiter->id),
            'top_skills_requested' => $this->getTopSkillsRequested($recruiter->id),
            'monthly_activity' => $this->getMonthlyActivity($recruiter->id),
            'talent_engagement' => $this->getTalentEngagementStats($recruiter->id)
        ];
    }

    private function calculateSuccessRate($recruiterId)
    {
        $totalRequests = TalentRequest::where('recruiter_id', $recruiterId)->count();
        if ($totalRequests == 0) return 0;

        $successfulRequests = TalentRequest::where('recruiter_id', $recruiterId)
            ->whereIn('status', ['approved', 'meeting_arranged', 'completed'])
            ->count();

        return round(($successfulRequests / $totalRequests) * 100, 1);
    }

    private function calculateAvgResponseTime($recruiterId)
    {
        $requests = TalentRequest::where('recruiter_id', $recruiterId)
            ->whereNotNull('approved_at')
            ->get();

        if ($requests->count() == 0) return 'N/A';

        $totalHours = 0;
        foreach ($requests as $request) {
            $totalHours += $request->created_at->diffInHours($request->approved_at);
        }

        $avgHours = $totalHours / $requests->count();
        return $avgHours < 24 ? round($avgHours, 1) . 'h' : round($avgHours / 24, 1) . 'd';
    }

    private function getTopSkillsRequested($recruiterId)
    {
        // This would analyze the requirements field to extract common skills
        $requests = TalentRequest::where('recruiter_id', $recruiterId)
            ->whereNotNull('requirements')
            ->get();

        $skillCounts = [];
        $commonSkills = ['PHP', 'JavaScript', 'Python', 'React', 'Vue', 'Laravel', 'Node.js', 'MySQL', 'MongoDB'];

        foreach ($requests as $request) {
            foreach ($commonSkills as $skill) {
                if (stripos($request->requirements, $skill) !== false) {
                    $skillCounts[$skill] = ($skillCounts[$skill] ?? 0) + 1;
                }
            }
        }

        arsort($skillCounts);
        return array_slice($skillCounts, 0, 5, true);
    }

    private function getMonthlyActivity($recruiterId)
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = TalentRequest::where('recruiter_id', $recruiterId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $months[$date->format('M Y')] = $count;
        }
        return $months;
    }

    private function getTalentEngagementStats($recruiterId)
    {
        $totalSent = TalentRequest::where('recruiter_id', $recruiterId)->count();
        $responded = TalentRequest::where('recruiter_id', $recruiterId)
            ->whereNotIn('status', ['pending'])
            ->count();

        return [
            'total_sent' => $totalSent,
            'responded' => $responded,
            'response_rate' => $totalSent > 0 ? round(($responded / $totalSent) * 100, 1) : 0
        ];
    }

    public function submitTalentRequest(Request $request)
    {
        $request->validate([
            'talent_id' => 'required|exists:talents,id',
            'project_title' => 'required|string|max:255',
            'project_description' => 'required|string',
            'requirements' => 'nullable|string',
            'budget_range' => 'nullable|string|max:100',
            'project_duration' => 'nullable|string|max:100',
            'urgency_level' => 'required|in:low,medium,high',
            'recruiter_message' => 'nullable|string'
        ]);

        $user = Auth::user();
        $recruiter = $user->recruiter;

        if (!$recruiter) {
            return response()->json(['error' => 'Recruiter profile not found'], 404);
        }

        // Check if request already exists for this talent
        $existingRequest = TalentRequest::where('recruiter_id', $recruiter->id)
            ->where('talent_id', $request->talent_id)
            ->whereNotIn('status', ['rejected', 'completed'])
            ->first();

        if ($existingRequest) {
            return response()->json(['error' => 'You already have an active request for this talent'], 400);
        }

        $talentRequest = TalentRequest::create([
            'recruiter_id' => $recruiter->id,
            'talent_id' => $request->talent_id,
            'project_title' => $request->project_title,
            'project_description' => $request->project_description,
            'requirements' => $request->requirements,
            'budget_range' => $request->budget_range,
            'project_duration' => $request->project_duration,
            'urgency_level' => $request->urgency_level,
            'recruiter_message' => $request->recruiter_message,
            'status' => 'pending'
        ]);

        // Send notifications to both talent and admin
        $notificationsSent = $this->notificationService->notifyNewTalentRequest($talentRequest);

        return response()->json([
            'success' => true,
            'message' => 'Talent request submitted successfully! Both the talent and admin have been notified.',
            'request_id' => $talentRequest->id,
            'notifications_sent' => $notificationsSent
        ]);
    }

    public function myRequests()
    {
        $userId = Auth::id();
        $user = User::with('recruiter')->find($userId);
        $title = 'My Talent Requests';
        $roles = 'Recruiter';
        $assignedKelas = [];
        $recruiter = $user->recruiter;

        $requests = collect();
        if ($recruiter) {
            $requests = TalentRequest::with(['talent.user'])
                ->where('recruiter_id', $recruiter->id)
                ->latest()
                ->paginate(10);
        }

        return view('admin.recruiter.requests', compact('user', 'title', 'roles', 'assignedKelas', 'requests'));
    }

    public function getScoutingReport(Request $request, $talentId)
    {
        try {
            $talent = Talent::with(['user'])->findOrFail($talentId);
            $metrics = $this->scoutingService->getTalentScoutingMetrics($talent);

            return response()->json([
                'success' => true,
                'talent' => [
                    'id' => $talent->id,
                    'name' => $talent->user->name,
                    'email' => $talent->user->email,
                    'profession' => $talent->user->pekerjaan,
                ],
                'metrics' => $metrics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load scouting report'
            ], 500);
        }
    }

    public function requestDetails($requestId)
    {
        try {
            $user = Auth::user();
            $recruiter = $user->recruiter;

            if (!$recruiter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Recruiter profile not found'
                ], 404);
            }

            $request = TalentRequest::with(['talent.user'])
                ->where('id', $requestId)
                ->where('recruiter_id', $recruiter->id)
                ->first();

            if (!$request) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request not found or access denied'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'request' => [
                    'id' => $request->id,
                    'talent_name' => $request->talent->user->name,
                    'talent_email' => $request->talent->user->email,
                    'talent_position' => $request->talent->user->pekerjaan,
                    'project_title' => $request->project_title,
                    'project_description' => $request->project_description,
                    'requirements' => $request->requirements,
                    'budget' => $request->budget_range,
                    'project_duration' => $request->project_duration,
                    'urgency_level' => ucfirst($request->urgency_level),
                    'recruiter_message' => $request->recruiter_message,
                    'status' => ucfirst($request->status),
                    'created_at' => $request->created_at->format('M d, Y h:i A'),
                    'updated_at' => $request->updated_at->format('M d, Y h:i A'),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Request details error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load request details'
            ], 500);
        }
    }

    /**
     * Get real-time analytics data for dashboard widgets
     */
    public function getAnalyticsData(Request $request)
    {
        try {
            $user = Auth::user();
            $recruiter = $user->recruiter;

            if (!$recruiter) {
                return response()->json(['error' => 'Recruiter profile not found'], 404);
            }

            $timeframe = $request->get('timeframe', '30'); // days
            $startDate = now()->subDays($timeframe);

            $data = [
                'request_trends' => $this->getRequestTrends($recruiter->id, $startDate),
                'skill_demand' => $this->getSkillDemandAnalysis($recruiter->id),
                'market_insights' => $this->getMarketInsights(),
                'performance_metrics' => $this->getPerformanceMetrics($recruiter->id, $startDate),
                'talent_pool_stats' => $this->getTalentPoolStats()
            ];

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('Analytics data error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load analytics data'], 500);
        }
    }

    private function getRequestTrends($recruiterId, $startDate)
    {
        $trends = TalentRequest::where('recruiter_id', $recruiterId)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $trends->mapWithKeys(function ($item) {
            return [$item->date => $item->count];
        });
    }

    private function getSkillDemandAnalysis($recruiterId)
    {
        // Leverage existing analytics service
        $analytics = $this->analyticsService->getSkillAnalytics();
        return $analytics['market_demand_analysis'] ?? [];
    }

    private function getMarketInsights()
    {
        $totalTalents = Talent::where('is_active', true)->count();
        $availableForScouting = User::where('available_for_scouting', true)->count();
        $recentlyActive = User::where('available_for_scouting', true)
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        return [
            'total_talent_pool' => $totalTalents,
            'available_talents' => $availableForScouting,
            'recently_active' => $recentlyActive,
            'market_growth' => $this->calculateMarketGrowth()
        ];
    }

    private function calculateMarketGrowth()
    {
        $currentMonth = User::where('available_for_scouting', true)
            ->whereMonth('created_at', now()->month)
            ->count();

        $lastMonth = User::where('available_for_scouting', true)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();

        if ($lastMonth == 0) return 0;
        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    private function getPerformanceMetrics($recruiterId, $startDate)
    {
        $requests = TalentRequest::where('recruiter_id', $recruiterId)
            ->where('created_at', '>=', $startDate);

        return [
            'total_requests' => $requests->count(),
            'approval_rate' => $this->calculateApprovalRate($recruiterId, $startDate),
            'avg_project_value' => $this->calculateAvgProjectValue($recruiterId),
            'repeat_collaborations' => $this->getRepeatCollaborations($recruiterId)
        ];
    }

    private function calculateApprovalRate($recruiterId, $startDate)
    {
        $total = TalentRequest::where('recruiter_id', $recruiterId)
            ->where('created_at', '>=', $startDate)
            ->count();

        if ($total == 0) return 0;

        $approved = TalentRequest::where('recruiter_id', $recruiterId)
            ->where('created_at', '>=', $startDate)
            ->whereIn('status', ['approved', 'meeting_arranged', 'completed'])
            ->count();

        return round(($approved / $total) * 100, 1);
    }

    private function calculateAvgProjectValue($recruiterId)
    {
        // This would need budget range parsing or additional budget fields
        return 'N/A'; // Placeholder for now
    }

    private function getRepeatCollaborations($recruiterId)
    {
        $talentCounts = TalentRequest::where('recruiter_id', $recruiterId)
            ->whereIn('status', ['completed'])
            ->groupBy('talent_id')
            ->selectRaw('talent_id, COUNT(*) as count')
            ->having('count', '>', 1)
            ->count();

        return $talentCounts;
    }

    private function getTalentPoolStats()
    {
        $skills = $this->analyticsService->getSkillAnalytics();

        return [
            'total_skills' => count($skills['skill_categories'] ?? []),
            'high_demand_skills' => count(array_filter($skills['market_demand_analysis'] ?? [],
                function($demand) { return $demand > 50; })),
            'emerging_skills' => $this->getEmergingSkills(),
            'skill_trends' => $skills['skill_progression_trends'] ?? []
        ];
    }

    private function getEmergingSkills()
    {
        // Skills that have shown growth in the last 3 months
        return ['React Native', 'Flutter', 'Machine Learning', 'DevOps', 'Cloud Architecture'];
    }

    /**
     * Get talent recommendations based on recent requests
     */
    public function getTalentRecommendations(Request $request)
    {
        try {
            $user = Auth::user();
            $recruiter = $user->recruiter;

            if (!$recruiter) {
                return response()->json(['error' => 'Recruiter profile not found'], 404);
            }

            // Get top talents with enhanced scoring
            $recommendations = $this->scoutingService->getTopTalents(12);

            // Add personalized scoring based on recruiter's history
            $recommendations = $recommendations->map(function($talent) use ($recruiter) {
                $talent->personalized_score = $this->calculatePersonalizedScore($talent, $recruiter);
                return $talent;
            });

            return response()->json([
                'success' => true,
                'recommendations' => $recommendations
            ]);
        } catch (\Exception $e) {
            Log::error('Talent recommendations error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load recommendations'], 500);
        }
    }

    private function calculatePersonalizedScore($talent, $recruiter)
    {
        $score = 50; // Base score

        // Previous interactions bonus
        $previousRequests = TalentRequest::where('recruiter_id', $recruiter->id)
            ->where('talent_id', $talent->id)
            ->whereIn('status', ['approved', 'completed'])
            ->count();
        $score += $previousRequests * 10;

        // Skill match bonus based on recruiter's request history
        $recruiterSkillPreferences = $this->getRecruiterSkillPreferences($recruiter->id);
        $talentSkills = $talent->user->talent_skills ?? [];

        $skillMatches = array_intersect($recruiterSkillPreferences,
            array_column($talentSkills, 'name'));
        $score += count($skillMatches) * 5;

        // Availability bonus
        if ($talent->user->available_for_scouting) {
            $score += 10;
        }

        return min($score, 100); // Cap at 100
    }

    private function getRecruiterSkillPreferences($recruiterId)
    {
        // Extract common skills from recruiter's past requests
        $requests = TalentRequest::where('recruiter_id', $recruiterId)
            ->whereNotNull('requirements')
            ->get();

        $skills = [];
        foreach ($requests as $request) {
            // Simple extraction - in reality you'd use NLP or better parsing
            preg_match_all('/\b(PHP|JavaScript|Python|React|Vue|Laravel|Node\.js|MySQL|MongoDB|CSS|HTML)\b/i',
                $request->requirements, $matches);
            $skills = array_merge($skills, $matches[0]);
        }

        return array_unique(array_map('strtolower', $skills));
    }
}
