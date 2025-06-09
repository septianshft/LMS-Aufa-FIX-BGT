<?php

namespace App\Http\Controllers;

use App\Models\Talent;
use App\Models\Recruiter;
use App\Models\TalentRequest;
use App\Models\User;
use App\Services\AdvancedSkillAnalyticsService;
use App\Services\SmartConversionTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TalentAdminController extends Controller
{
    protected $skillAnalytics;
    protected $conversionTracking;

    public function __construct(
        AdvancedSkillAnalyticsService $skillAnalytics,
        SmartConversionTrackingService $conversionTracking
    ) {
        $this->skillAnalytics = $skillAnalytics;
        $this->conversionTracking = $conversionTracking;
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
                break;
            case 'meeting_arranged':
                $updateData['meeting_arranged_at'] = now();
                break;
            case 'onboarded':
                $updateData['onboarded_at'] = now();
                break;
        }

        $talentRequest->update($updateData);

        $statusMessage = match($request->status) {
            'approved' => 'Request has been approved successfully.',
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
            'status' => $request->status
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
}
