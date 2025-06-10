<?php

namespace App\Http\Controllers;

use App\Models\Talent;
use App\Models\Recruiter;
use App\Models\TalentRequest;
use App\Models\User;
use App\Services\AdvancedSkillAnalyticsService;
use App\Services\SmartConversionTrackingService;
use App\Services\TalentRequestNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TalentAdminController extends Controller
{
    protected $skillAnalytics;
    protected $conversionTracking;
    protected $notificationService;

    public function __construct(
        AdvancedSkillAnalyticsService $skillAnalytics,
        SmartConversionTrackingService $conversionTracking,
        TalentRequestNotificationService $notificationService
    ) {
        $this->skillAnalytics = $skillAnalytics;
        $this->conversionTracking = $conversionTracking;
        $this->notificationService = $notificationService;
    }

    public function dashboard()
    {
        $user = Auth::user();
        $title = 'Talent Admin Dashboard';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        // Enhanced analytics data
        $skillAnalytics = $this->skillAnalytics->getSkillAnalytics();
        $conversionAnalytics = $this->conversionTracking->getConversionAnalytics();

        // Use the new unified user system instead of separate Talent/Recruiter models
        // Count users with talent status
        $activeTalents = User::where('is_active_talent', true)->count();
        $totalTalents = User::where('is_active_talent', true)->count(); // Total talents for dashboard
        $availableTalents = User::where('available_for_scouting', true)->count();

        // Count users with recruiter role
        $activeRecruiters = User::whereHas('roles', function($query) {
            $query->where('name', 'recruiter');
        })->count();
        $totalRecruiters = $activeRecruiters; // Total recruiters for dashboard

        // Request statistics
        $totalRequests = TalentRequest::count();
        $pendingRequests = TalentRequest::where('status', 'pending')->count();
        $approvedRequests = TalentRequest::where('status', 'approved')->count();
        $rejectedRequests = TalentRequest::where('status', 'rejected')->count();

        // Recent registrations (last 30 days)
        $recentTalents = User::where('is_active_talent', true)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        $recentRecruiters = User::whereHas('roles', function($query) {
            $query->where('name', 'recruiter');
        })->where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // Recent activity (latest 5 users of each type)
        $latestTalents = User::where('is_active_talent', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $latestRecruiters = User::whereHas('roles', function($query) {
            $query->where('name', 'recruiter');
        })->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent requests (latest 5)
        $latestRequests = TalentRequest::with(['recruiter.user', 'talentUser'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('talent_admin.dashboard', compact(
            'user', 'title', 'roles', 'assignedKelas',
            'activeTalents', 'totalTalents', 'availableTalents', 'activeRecruiters', 'totalRecruiters',
            'totalRequests', 'pendingRequests', 'approvedRequests', 'rejectedRequests',
            'recentTalents', 'recentRecruiters', 'latestTalents', 'latestRecruiters',
            'latestRequests', 'skillAnalytics', 'conversionAnalytics'
        ));
    }

    public function manageTalents()
    {
        $talents = Talent::with('user')->orderBy('created_at', 'desc')->paginate(10);
        $title = 'Manage Talents';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        return view('admin.talent_admin.manage_talents', compact('talents', 'title', 'roles', 'assignedKelas'));
    }

    public function manageRecruiters()
    {
        $recruiters = Recruiter::with('user')->orderBy('created_at', 'desc')->paginate(10);
        $title = 'Manage Recruiters';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        return view('admin.talent_admin.manage_recruiters', compact('recruiters', 'title', 'roles', 'assignedKelas'));
    }

    public function toggleTalentStatus(Talent $talent)
    {
        $talent->update(['is_active' => !$talent->is_active]);

        return back()->with('success', 'Talent status updated successfully.');
    }

    public function toggleRecruiterStatus(Recruiter $recruiter)
    {
        $recruiter->update(['is_active' => !$recruiter->is_active]);

        return back()->with('success', 'Recruiter status updated successfully.');
    }

    public function manageRequests(Request $request)
    {
        $query = TalentRequest::with(['recruiter.user', 'talent.user']);

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality - properly group the OR conditions
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('recruiter.user', function($subQ) use ($searchTerm) {
                    $subQ->where('name', 'LIKE', '%' . $searchTerm . '%')
                         ->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                })
                ->orWhereHas('talent.user', function($subQ) use ($searchTerm) {
                    $subQ->where('name', 'LIKE', '%' . $searchTerm . '%')
                         ->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                })
                ->orWhere('project_title', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('project_description', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        // Append query parameters to pagination links
        $requests->appends($request->query());

        $title = 'Manage Talent Requests';
        $roles = 'Talent Admin';
        $assignedKelas = [];
        $user = Auth::user();

        return view('admin.talent_admin.manage_requests', compact(
            'requests',
            'title',
            'roles',
            'assignedKelas',
            'user'
        ));
    }

    public function showRequest(TalentRequest $talentRequest)
    {
        $talentRequest->load(['recruiter.user', 'talent.user']);

        $title = 'Request Details';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        return view('admin.talent_admin.request_details', compact('talentRequest', 'title', 'roles', 'assignedKelas'));
    }

    public function updateRequestStatus(Request $request, TalentRequest $talentRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,meeting_arranged,agreement_reached,onboarded,completed',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $updateData = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ];

        // Set timestamp based on status
        switch($request->status) {
            case 'approved':
                $updateData['approved_at'] = now();
                // Mark admin as accepted when approving
                $talentRequest->markAdminAccepted($request->admin_notes);
                break;
            case 'meeting_arranged':
                $updateData['meeting_arranged_at'] = now();
                break;
            case 'onboarded':
                $updateData['onboarded_at'] = now();
                break;
            case 'rejected':
                // Reset acceptance flags if rejected
                $updateData['admin_accepted'] = false;
                $updateData['talent_accepted'] = false;
                $updateData['both_parties_accepted'] = false;
                break;
        }

        $talentRequest->update($updateData);

        // Send notifications about status change
        $this->notificationService->notifyStatusChange($talentRequest, $talentRequest->getOriginal('status'), $request->status);

        $statusMessage = match($request->status) {
            'approved' => 'Request has been approved successfully. Both parties have now accepted.',
            'rejected' => 'Request has been rejected.',
            'meeting_arranged' => 'Meeting has been arranged.',
            'agreement_reached' => 'Agreement has been reached.',
            'onboarded' => 'Talent has been onboarded successfully.',
            'completed' => 'Project has been completed.',
            default => 'Request status updated successfully.'
        };

        return response()->json([
            'success' => true,
            'message' => $statusMessage,
            'status' => $request->status,
            'both_parties_accepted' => $talentRequest->fresh()->both_parties_accepted,
            'acceptance_status' => $talentRequest->fresh()->getAcceptanceStatus()
        ]);
    }

    /**
     * Display advanced analytics dashboard
     */
    public function analytics()
    {
        $user = Auth::user();
        $title = 'Advanced Analytics';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        // Get comprehensive analytics
        $skillAnalytics = $this->skillAnalytics->getSkillAnalytics();
        $conversionAnalytics = $this->conversionTracking->getConversionAnalytics();

        // Trigger smart notifications for conversion-ready users
        $notificationsSent = $this->conversionTracking->triggerSmartNotifications();

        return view('talent_admin.analytics', compact(
            'user', 'title', 'roles', 'assignedKelas',
            'skillAnalytics', 'conversionAnalytics', 'notificationsSent'
        ));
    }

    /**
     * Get conversion candidates API endpoint
     */
    public function getConversionCandidates()
    {
        $analytics = $this->conversionTracking->getConversionAnalytics();
        return response()->json($analytics['top_conversion_candidates']);
    }

    /**
     * Get skill trends API endpoint
     */
    public function getSkillTrends()
    {
        $analytics = $this->skillAnalytics->getSkillAnalytics();
        return response()->json([
            'progression_trends' => $analytics['skill_progression_trends'],
            'category_distribution' => $analytics['skill_categories'],
            'market_demand' => $analytics['market_demand_analysis']
        ]);
    }

    /**
     * Get talent details for modal view
     */
    public function getTalentDetails(Talent $talent)
    {
        // Load talent with user relationship
        $talent->load('user');

        // Get talent skills safely
        $talentSkills = [];
        if ($talent->user->talent_skills) {
            if (is_string($talent->user->talent_skills)) {
                $decoded = json_decode($talent->user->talent_skills, true);
                $talentSkills = is_array($decoded) ? $decoded : [];
            } elseif (is_array($talent->user->talent_skills)) {
                $talentSkills = $talent->user->talent_skills;
            }
        }

        // Get user statistics
        $stats = [
            'completed_courses' => $talent->user->courseProgress()->where('is_completed', true)->count(),
            'certificates' => $talent->user->certificates()->count(),
            'skill_level' => !empty($talentSkills) ? round(collect($talentSkills)->avg(function($skill) {
                return is_array($skill) && isset($skill['level']) ? (int)$skill['level'] : 0;
            }), 1) : 0,
            'experience_years' => $talent->experience_years ?? 0
        ];

        // Format skills for display
        $formattedSkills = collect($talentSkills)->map(function($skill) {
            if (is_array($skill)) {
                return [
                    'name' => $skill['name'] ?? 'Unknown',
                    'level' => $skill['level'] ?? null,
                    'category' => $skill['category'] ?? 'General'
                ];
            }
            return ['name' => (string)$skill, 'level' => null, 'category' => 'General'];
        })->toArray();

        // Get portfolio/projects if available
        $portfolio = [];
        if ($talent->portfolio) {
            if (is_string($talent->portfolio)) {
                $decoded = json_decode($talent->portfolio, true);
                $portfolio = is_array($decoded) ? $decoded : [];
            } elseif (is_array($talent->portfolio)) {
                $portfolio = $talent->portfolio;
            }
        }

        return response()->json([
            'id' => $talent->id,
            'name' => $talent->user->name,
            'email' => $talent->user->email,
            'phone' => $talent->user->phone ?? null,
            'location' => $talent->user->location ?? null,
            'job' => $talent->user->pekerjaan ?? null,
            'avatar' => $talent->user->avatar ? asset('storage/' . $talent->user->avatar) : null,
            'is_active' => $talent->is_active,
            'joined_date' => $talent->created_at->format('M d, Y H:i'),
            'skills' => $formattedSkills,
            'portfolio' => $portfolio,
            'stats' => $stats
        ]);
    }

    /**
     * Get recruiter details for modal view
     */
    public function getRecruiterDetails(Recruiter $recruiter)
    {
        // Load recruiter with user relationship
        $recruiter->load('user');

        // Get recruitment statistics
        $totalRequests = TalentRequest::where('recruiter_id', $recruiter->id)->count();
        $approvedRequests = TalentRequest::where('recruiter_id', $recruiter->id)
            ->where('status', 'approved')->count();
        $pendingRequests = TalentRequest::where('recruiter_id', $recruiter->id)
            ->where('status', 'pending')->count();

        $successRate = $totalRequests > 0 ? round(($approvedRequests / $totalRequests) * 100, 1) : 0;

        $stats = [
            'total_requests' => $totalRequests,
            'approved_requests' => $approvedRequests,
            'pending_requests' => $pendingRequests,
            'success_rate' => $successRate
        ];

        // Get recent requests
        $recentRequests = TalentRequest::where('recruiter_id', $recruiter->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($request) {
                return [
                    'id' => $request->id,
                    'project_title' => $request->project_title ?? 'Untitled Project',
                    'description' => Str::limit($request->project_description ?? 'No description', 100),
                    'status' => $request->status,
                    'created_at' => $request->created_at->format('M d, Y')
                ];
            });

        // Try to extract company info from user's additional fields or use defaults
        $companyName = $recruiter->user->company ?? 'Not specified';
        $jobTitle = $recruiter->user->pekerjaan ?? 'Recruiter';

        // Build company details from available user data
        $companyDetails = null;
        if ($recruiter->user->company) {
            $companyDetails = [
                'industry' => 'Not specified',
                'size' => 'Not specified',
                'website' => null,
                'description' => null
            ];
        }

        return response()->json([
            'id' => $recruiter->id,
            'name' => $recruiter->user->name,
            'email' => $recruiter->user->email,
            'phone' => $recruiter->user->phone ?? null,
            'company' => $companyName,
            'job' => $jobTitle,
            'avatar' => $recruiter->user->avatar ? asset('storage/' . $recruiter->user->avatar) : null,
            'is_active' => $recruiter->is_active,
            'joined_date' => $recruiter->created_at->format('M d, Y H:i'),
            'company_details' => $companyDetails,
            'stats' => $stats,
            'recent_requests' => $recentRequests
        ]);
    }
}
