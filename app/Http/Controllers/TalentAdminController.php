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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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

        // Optimized dashboard statistics with caching
        $dashboardStats = Cache::remember('talent_admin_dashboard_stats', config('talent_performance.caching.analytics_ttl', 600), function() {
            // Single optimized query for all statistics
            $stats = DB::select('
                SELECT
                    COUNT(CASE WHEN u.is_active_talent = 1 THEN 1 END) as active_talents,
                    COUNT(CASE WHEN u.available_for_scouting = 1 THEN 1 END) as available_talents,
                    COUNT(CASE WHEN ur.role_id = (SELECT id FROM roles WHERE name = "recruiter" LIMIT 1) THEN 1 END) as active_recruiters,
                    COUNT(CASE WHEN ur.role_id = (SELECT id FROM roles WHERE name = "recruiter" LIMIT 1) THEN 1 END) as total_recruiters,

                    -- Request statistics
                    (SELECT COUNT(*) FROM talent_requests WHERE deleted_at IS NULL) as total_requests,
                    (SELECT COUNT(*) FROM talent_requests WHERE status = "pending" AND deleted_at IS NULL) as pending_requests,
                    (SELECT COUNT(*) FROM talent_requests WHERE status = "approved" AND deleted_at IS NULL) as approved_requests,
                    (SELECT COUNT(*) FROM talent_requests WHERE status = "rejected" AND deleted_at IS NULL) as rejected_requests,

                    -- Recent registrations (last 30 days)
                    COUNT(CASE WHEN u.is_active_talent = 1 AND u.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as recent_talents,
                    COUNT(CASE WHEN ur.role_id = (SELECT id FROM roles WHERE name = "recruiter" LIMIT 1) AND u.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as recent_recruiters

                FROM users u
                LEFT JOIN model_has_roles ur ON u.id = ur.model_id AND ur.model_type = "App\\\\Models\\\\User"
            ')[0];

            return [
                'activeTalents' => (int)$stats->active_talents,
                'totalTalents' => (int)$stats->active_talents,
                'availableTalents' => (int)$stats->available_talents,
                'activeRecruiters' => (int)$stats->active_recruiters,
                'totalRecruiters' => (int)$stats->total_recruiters,
                'totalRequests' => (int)$stats->total_requests,
                'pendingRequests' => (int)$stats->pending_requests,
                'approvedRequests' => (int)$stats->approved_requests,
                'rejectedRequests' => (int)$stats->rejected_requests,
                'recentTalents' => (int)$stats->recent_talents,
                'recentRecruiters' => (int)$stats->recent_recruiters,
            ];
        });

        // Extract cached values
        extract($dashboardStats);

        // Optimized recent activity queries with caching
        $recentActivity = Cache::remember('talent_admin_recent_activity', 300, function() {
            return [
                'latestTalents' => User::select(['id', 'name', 'email', 'created_at'])
                    ->where('is_active_talent', true)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),

                'latestRecruiters' => User::select(['id', 'name', 'email', 'created_at'])
                    ->whereHas('roles', function($query) {
                        $query->where('name', 'recruiter');
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),

                'latestRequests' => TalentRequest::select(['id', 'project_title', 'status', 'created_at', 'recruiter_id', 'talent_user_id'])
                    ->with(['recruiter:id,user_id', 'recruiter.user:id,name', 'talentUser:id,name'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
            ];
        });

        // Extract recent activity
        $latestTalents = $recentActivity['latestTalents'];
        $latestRecruiters = $recentActivity['latestRecruiters'];
        $latestRequests = $recentActivity['latestRequests'];

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

        // Optimize pagination size for better performance
        $perPage = min($request->get('per_page', 15), 50); // Max 50 items per page
        $requests = $query->orderBy('created_at', 'desc')->paginate($perPage);

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
            'completed_courses' => $talent->user->courseProgress()->where('progress', '>=', 100)->count(),
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

    /**
     * Manage Talent Admin Accounts
     */
    public function manageTalentAdmins()
    {
        $user = Auth::user();
        $title = 'Kelola Talent Admin';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        $talentAdmins = User::whereHas('roles', function($query) {
            $query->where('name', 'talent_admin');
        })->orderBy('created_at', 'desc')->paginate(10);

        return view('talent_admin.manage_talent_admins', compact('talentAdmins', 'user', 'title', 'roles', 'assignedKelas'));
    }

    /**
     * Show form to create new talent admin
     */
    public function createTalentAdmin()
    {
        $user = Auth::user();
        $title = 'Tambah Talent Admin';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        return view('talent_admin.create_talent_admin', compact('user', 'title', 'roles', 'assignedKelas'));
    }

    /**
     * Store new talent admin
     */
    public function storeTalentAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'avatar' => null, // Default to null
                'email_verified_at' => now(), // Auto-verify admin accounts
            ];

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $userData['avatar'] = $avatarPath;
            }

            $user = User::create($userData);

            // Assign talent_admin role
            $user->assignRole('talent_admin');

            return redirect()->route('talent_admin.manage_talent_admins')
                ->with('success', 'Talent Admin berhasil dibuat! Akun: ' . $user->name . ' (' . $user->email . ')');

        } catch (\Exception $e) {
            Log::error('Failed to create talent admin: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat Talent Admin: ' . $e->getMessage());
        }
    }

    /**
     * Show talent admin details
     */
    public function showTalentAdmin(User $user)
    {
        // Ensure the user is a talent admin
        if (!$user->hasRole('talent_admin')) {
            return redirect()->route('talent_admin.manage_talent_admins')
                ->with('error', 'User bukan Talent Admin');
        }

        $authUser = Auth::user();
        $title = 'Detail Talent Admin';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        // Get admin statistics
        $stats = [
            'requests_handled' => TalentRequest::where('updated_by', $user->id)->count(),
            'talents_managed' => Talent::count(), // Could be more specific if tracking who managed whom
            'recruiters_managed' => Recruiter::count(),
            'join_date' => $user->created_at->format('M d, Y'),
            'last_login' => $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never',
        ];

        return view('talent_admin.show_talent_admin', compact('user', 'stats', 'authUser', 'title', 'roles', 'assignedKelas'));
    }

    /**
     * Show form to edit talent admin
     */
    public function editTalentAdmin(User $user)
    {
        // Ensure the user is a talent admin
        if (!$user->hasRole('talent_admin')) {
            return redirect()->route('talent_admin.manage_talent_admins')
                ->with('error', 'User bukan Talent Admin');
        }

        $authUser = Auth::user();
        $title = 'Edit Talent Admin';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        return view('talent_admin.edit_talent_admin', compact('user', 'authUser', 'title', 'roles', 'assignedKelas'));
    }

    /**
     * Update talent admin
     */
    public function updateTalentAdmin(Request $request, User $user)
    {
        // Ensure the user is a talent admin
        if (!$user->hasRole('talent_admin')) {
            return redirect()->route('talent_admin.manage_talent_admins')
                ->with('error', 'User bukan Talent Admin');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $user->name = $request->name;
            $user->email = $request->email;

            // Update password if provided
            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $avatarPath;
            }

            $user->save();

            return redirect()->route('talent_admin.manage_talent_admins')
                ->with('success', 'Talent Admin berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui Talent Admin: ' . $e->getMessage());
        }
    }

    /**
     * Delete talent admin
     */
    public function destroyTalentAdmin(User $user)
    {
        // Ensure the user is a talent admin
        if (!$user->hasRole('talent_admin')) {
            return redirect()->route('talent_admin.manage_talent_admins')
                ->with('error', 'User bukan Talent Admin');
        }

        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return redirect()->route('talent_admin.manage_talent_admins')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri');
        }

        try {
            // Delete avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Remove role and delete user
            $user->removeRole('talent_admin');
            $user->delete();

            return redirect()->route('talent_admin.manage_talent_admins')
                ->with('success', 'Talent Admin berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus Talent Admin: ' . $e->getMessage());
        }
    }

    /**
     * Get talent admin details via AJAX
     */
    public function getTalentAdminDetails(User $user)
    {
        // Ensure the user is a talent admin
        if (!$user->hasRole('talent_admin')) {
            return response()->json(['error' => 'User bukan Talent Admin'], 404);
        }

        $stats = [
            'requests_handled' => TalentRequest::where('updated_by', $user->id)->count(),
            'talents_managed' => Talent::count(),
            'recruiters_managed' => Recruiter::count(),
            'join_date' => $user->created_at->format('M d, Y'),
            'last_login' => $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never',
        ];

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            'created_at' => $user->created_at->format('M d, Y H:i'),
            'updated_at' => $user->updated_at->format('M d, Y H:i'),
            'stats' => $stats,
            // Note: We don't return password for security reasons
        ]);
    }

    /**
     * Store a newly created recruiter.
     */
    public function storeRecruiter(Request $request)
    {
        Log::info('storeRecruiter method called', [
            'request_data' => $request->all(),
            'content_type' => $request->header('Content-Type'),
            'method' => $request->method(),
            'json' => $request->json()->all()
        ]);

        // Handle both JSON and form data
        $inputData = $request->all();
        if (empty($inputData) && $request->isJson()) {
            $inputData = $request->json()->all();
        }

        // Log the actual input data we're working with
        Log::info('Input data to validate:', $inputData);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'pekerjaan' => 'required|string|max:255', // This aligns with public registration
            'avatar' => 'nullable|image|mimes:png,jpg,jpeg|max:2048', // Optional for admin creation
            'company_name' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'company_size' => 'nullable|string|max:100',
            'company_description' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar, gunakan email lain.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'pekerjaan.required' => 'Pekerjaan tidak boleh kosong.',
            'avatar.image' => 'Avatar harus berupa file gambar.',
            'avatar.mimes' => 'Avatar harus berformat PNG, JPG, atau JPEG.',
            'avatar.max' => 'Avatar maksimal 2MB.',
        ]);

        Log::info('Validation passed', $validatedData);

        try {
            Log::info('Starting user creation...');

            // Handle avatar upload (SAME pattern as RegisteredUserController)
            $avatarPath = 'public\images\default-avatar.png'; // Default fallback
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                Log::info('Avatar uploaded successfully', ['path' => $avatarPath]);
            }

            // Create user account (SAME pattern as RegisteredUserController)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'pekerjaan' => $request->pekerjaan, // Required field like registration
                'avatar' => $avatarPath, // Handle avatar same as registration
                'password' => Hash::make($request->password), // Use Hash::make like registration
                'email_verified_at' => now(), // Auto-verify admin-created accounts
            ]);

            Log::info('User created successfully', ['user_id' => $user->id]);

            // Assign recruiter role (SAME pattern as RegisteredUserController)
            $user->assignRole('recruiter');
            Log::info('Role assigned successfully');

            // Create recruiter profile (SAME pattern as RegisteredUserController)
            $recruiter = Recruiter::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name ?: $request->pekerjaan, // Use job as fallback
                'industry' => $request->industry ?: 'Other',
                'company_size' => $request->company_size,
                'company_description' => $request->company_description,
                'phone' => $request->phone,
                'address' => $request->address,
                'is_active' => true,
            ]);

            Log::info('Recruiter profile created successfully', ['recruiter_id' => $recruiter->id]);

            return response()->json([
                'success' => true,
                'message' => 'Perekrut berhasil ditambahkan!',
                'recruiter' => [
                    'id' => $recruiter->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'company_name' => $recruiter->company_name,
                    'industry' => $recruiter->industry,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', ['errors' => $e->errors()]);

            return response()->json([
                'success' => false,
                'message' => 'Data yang dimasukkan tidak valid.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error creating recruiter: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan perekrut.',
                'errors' => ['general' => ['Terjadi kesalahan sistem. Silakan coba lagi.']]
            ], 500);
        }
    }

    /**
     * Edit a recruiter.
     */
    public function editRecruiter(Recruiter $recruiter)
    {
        $recruiter->load('user');

        return response()->json([
            'success' => true,
            'recruiter' => [
                'id' => $recruiter->id,
                'user_id' => $recruiter->user->id,
                'name' => $recruiter->user->name,
                'email' => $recruiter->user->email,
                'company_name' => $recruiter->company_name,
                'industry' => $recruiter->industry,
                'company_size' => $recruiter->company_size,
                'website' => $recruiter->website,
                'company_description' => $recruiter->company_description,
                'phone' => $recruiter->phone,
                'address' => $recruiter->address,
                'is_active' => $recruiter->is_active,
            ]
        ]);
    }

    /**
     * Update a recruiter.
     */
    public function updateRecruiter(Request $request, Recruiter $recruiter)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $recruiter->user->id,
            'company_name' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'company_size' => 'nullable|string|max:100',
            'company_description' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            // Update user info
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = bcrypt($request->password);
            }

            $recruiter->user->update($userData);

            // Update recruiter profile
            $recruiter->update([
                'company_name' => $request->company_name,
                'industry' => $request->industry,
                'company_size' => $request->company_size,
                'company_description' => $request->company_description,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data perekrut berhasil diperbarui!',
                'recruiter' => [
                    'id' => $recruiter->id,
                    'name' => $recruiter->user->name,
                    'email' => $recruiter->user->email,
                    'company_name' => $recruiter->company_name,
                    'industry' => $recruiter->industry,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating recruiter: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data perekrut.',
                'errors' => ['general' => ['Terjadi kesalahan sistem. Silakan coba lagi.']]
            ], 500);
        }
    }

    /**
     * Delete a recruiter.
     */
    public function destroyRecruiter(Recruiter $recruiter)
    {
        try {
            $recruiterName = $recruiter->user->name;

            // Delete the recruiter (this will also soft-delete due to SoftDeletes trait)
            $recruiter->delete();

            // Also delete the user account
            $recruiter->user->delete();

            return response()->json([
                'success' => true,
                'message' => "Perekrut '{$recruiterName}' berhasil dihapus!"
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting recruiter: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus perekrut.',
                'errors' => ['general' => ['Terjadi kesalahan sistem. Silakan coba lagi.']]
            ], 500);
        }
    }
}
