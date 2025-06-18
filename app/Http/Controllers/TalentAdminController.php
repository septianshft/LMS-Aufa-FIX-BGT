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
                    -- Talent statistics (users with talent role and active status)
                    COUNT(CASE WHEN ur_talent.role_id = (SELECT id FROM roles WHERE name = "talent" LIMIT 1) AND u.is_active_talent = 1 THEN 1 END) as active_talents,
                    COUNT(CASE WHEN ur_talent.role_id = (SELECT id FROM roles WHERE name = "talent" LIMIT 1) AND u.available_for_scouting = 1 THEN 1 END) as available_talents,

                    -- Recruiter statistics
                    COUNT(CASE WHEN ur_rec.role_id = (SELECT id FROM roles WHERE name = "recruiter" LIMIT 1) THEN 1 END) as active_recruiters,
                    COUNT(CASE WHEN ur_rec.role_id = (SELECT id FROM roles WHERE name = "recruiter" LIMIT 1) THEN 1 END) as total_recruiters,

                    -- Request statistics
                    (SELECT COUNT(*) FROM talent_requests WHERE deleted_at IS NULL) as total_requests,
                    (SELECT COUNT(*) FROM talent_requests WHERE status = "pending" AND deleted_at IS NULL) as pending_requests,
                    (SELECT COUNT(*) FROM talent_requests WHERE status = "approved" AND deleted_at IS NULL) as approved_requests,
                    (SELECT COUNT(*) FROM talent_requests WHERE status = "rejected" AND deleted_at IS NULL) as rejected_requests,

                    -- Recent registrations (last 30 days) - only actual talents
                    COUNT(CASE WHEN ur_talent.role_id = (SELECT id FROM roles WHERE name = "talent" LIMIT 1) AND u.is_active_talent = 1 AND u.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as recent_talents,
                    COUNT(CASE WHEN ur_rec.role_id = (SELECT id FROM roles WHERE name = "recruiter" LIMIT 1) AND u.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as recent_recruiters

                FROM users u
                LEFT JOIN model_has_roles ur_talent ON u.id = ur_talent.model_id AND ur_talent.model_type = "App\\\\Models\\\\User" AND ur_talent.role_id = (SELECT id FROM roles WHERE name = "talent" LIMIT 1)
                LEFT JOIN model_has_roles ur_rec ON u.id = ur_rec.model_id AND ur_rec.model_type = "App\\\\Models\\\\User" AND ur_rec.role_id = (SELECT id FROM roles WHERE name = "recruiter" LIMIT 1)
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

        // Get recent activity with minimal cache time for real-time updates
        $recentActivity = Cache::remember('talent_admin_recent_activity_' . $user->id, 5, function() {
            try {
                return [
                    'latestTalents' => User::select(['id', 'name', 'email', 'created_at', 'avatar', 'pekerjaan', 'is_active_talent'])
                        ->whereHas('roles', function($query) {
                            $query->where('name', 'talent');
                        })
                        ->where('is_active_talent', true)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get(),

                    'latestRecruiters' => User::select(['id', 'name', 'email', 'created_at', 'avatar', 'pekerjaan'])
                        ->whereHas('roles', function($query) {
                            $query->where('name', 'recruiter');
                        })
                        ->with('roles')
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get(),

                    'latestRequests' => TalentRequest::select([
                            'id', 'project_title', 'status', 'created_at', 'updated_at',
                            'recruiter_id', 'talent_user_id', 'talent_accepted', 'admin_accepted',
                            'both_parties_accepted'
                        ])
                        ->with([
                            'recruiter:id,user_id',
                            'recruiter.user:id,name,avatar',
                            'talentUser:id,name,avatar'
                        ])
                        ->whereNotNull('recruiter_id')
                        ->whereNotNull('talent_user_id')
                        ->where(function($query) {
                            // Show requests that need admin attention
                            $query->where('status', 'pending') // New requests
                                  ->orWhere(function($subQuery) {
                                      // Talent accepted, waiting for admin (regardless of status)
                                      $subQuery->where('talent_accepted', true)
                                               ->where('admin_accepted', false);
                                  })
                                  ->orWhere(function($subQuery) {
                                      // Handle inconsistent status - if status is approved but admin_accepted is false
                                      $subQuery->where('status', 'approved')
                                               ->where('admin_accepted', false);
                                  });
                        })
                        ->orderBy('created_at', 'desc')
                        ->limit(10) // Show more since these are actionable items
                        ->get()
                ];
            } catch (\Exception $e) {
                Log::error('Error loading recent activity for talent admin dashboard: ' . $e->getMessage());
                return [
                    'latestTalents' => collect([]),
                    'latestRecruiters' => collect([]),
                    'latestRequests' => collect([])
                ];
            }
        });

        // Extract recent activity
        $latestTalents = $recentActivity['latestTalents'] ?? collect([]);
        $latestRecruiters = $recentActivity['latestRecruiters'] ?? collect([]);
        $latestRequests = $recentActivity['latestRequests'] ?? collect([]);

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

        // Enhanced filter by status including acceptance states
        if ($request->filled('status')) {
            $status = $request->status;

            // Handle complex status filters
            switch ($status) {
                case 'talent_awaiting_admin':
                    // Talent accepted, awaiting admin approval
                    $query->where(function($q) {
                        $q->where('talent_accepted', true)
                          ->where('admin_accepted', false)
                          ->whereIn('status', ['pending', 'approved']);
                    });
                    break;
                case 'admin_awaiting_talent':
                    // Admin approved, awaiting talent acceptance
                    $query->where(function($q) {
                        $q->where('talent_accepted', false)
                          ->where('admin_accepted', true)
                          ->whereIn('status', ['pending', 'approved']);
                    });
                    break;
                case 'both_accepted':
                    // Both parties accepted, ready for meeting
                    $query->where('both_parties_accepted', true)
                          ->whereIn('status', ['pending', 'approved']);
                    break;
                case 'pending_review':
                    // No acceptances yet
                    $query->where('talent_accepted', false)
                          ->where('admin_accepted', false)
                          ->where('status', 'pending');
                    break;
                default:
                    // Standard status filter
                    $query->where('status', $status);
                    break;
            }
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
            'status' => 'required|in:pending,approved,rejected,meeting_arranged,agreement_reached,onboarded,completed'
        ]);

        // Additional validation for meeting arrangement
        if ($request->status === 'meeting_arranged') {
            if (!$talentRequest->canAdminArrangeMeeting()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot arrange meeting: Both talent and admin must accept the request first.',
                    'talent_accepted' => $talentRequest->talent_accepted,
                    'admin_accepted' => $talentRequest->admin_accepted,
                    'both_parties_accepted' => $talentRequest->both_parties_accepted
                ], 400);
            }
        }

        $updateData = [
            'status' => $request->status,
        ];

        // Set timestamp based on status
        switch($request->status) {
            case 'approved':
                $updateData['approved_at'] = now();
                // Mark admin as accepted when approving
                $updateData['admin_accepted'] = true;
                $updateData['admin_accepted_at'] = now();

                // Check if talent has already accepted and mark both accepted if so
                if ($talentRequest->talent_accepted) {
                    $updateData['both_parties_accepted'] = true;
                    $updateData['workflow_completed_at'] = now();
                }
                break;
            case 'meeting_arranged':
                $updateData['meeting_arranged_at'] = now();
                // Ensure both parties are marked as accepted when meeting is arranged
                if (!$talentRequest->both_parties_accepted) {
                    $updateData['both_parties_accepted'] = true;
                    $updateData['workflow_completed_at'] = now();
                }
                break;
            case 'onboarded':
                $updateData['onboarded_at'] = now();

                // Auto-create ProjectAssignment if project_id exists and assignment doesn't exist yet
                if ($talentRequest->project_id && $talentRequest->talent_id) {
                    $existingAssignment = \App\Models\ProjectAssignment::where('project_id', $talentRequest->project_id)
                        ->where('talent_id', $talentRequest->talent_id)
                        ->first();

                    if (!$existingAssignment) {
                        try {
                            // Extract numeric budget value
                            $budgetValue = 0;
                            if ($talentRequest->budget_range) {
                                // Extract first number from budget range like "Rp 5.000.000 - Rp 15.000.000"
                                preg_match('/[\d,]+/', str_replace('.', '', $talentRequest->budget_range), $matches);
                                if (!empty($matches)) {
                                    $budgetValue = intval(str_replace(',', '', $matches[0]));
                                }
                            }

                            // Create assignment with 'accepted' status to auto-transition project
                            \App\Models\ProjectAssignment::create([
                                'project_id' => $talentRequest->project_id,
                                'talent_id' => $talentRequest->talent_id,
                                'specific_role' => $talentRequest->project_title ?? 'General Role',
                                'status' => \App\Models\ProjectAssignment::STATUS_ACCEPTED,
                                'talent_accepted_at' => now(),
                                'assignment_notes' => 'Auto-assigned from talent request onboarding',
                                'individual_budget' => $budgetValue,
                                'priority_level' => 'medium',
                                'talent_start_date' => $talentRequest->project_start_date ?? now(),
                                'talent_end_date' => $talentRequest->project_end_date ?? now()->addDays(30)
                            ]);

                            // Check if all assignments for this project are accepted and update project status
                            $this->checkAndActivateProject($talentRequest->project_id);
                        } catch (\Exception $e) {
                            // Failed to auto-create project assignment - log for debugging if needed
                            \Illuminate\Support\Facades\Log::warning('Failed to auto-create project assignment during onboarding', [
                                'talent_request_id' => $talentRequest->id,
                                'project_id' => $talentRequest->project_id,
                                'talent_id' => $talentRequest->talent_id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    } else {
                        // If assignment exists but not accepted, auto-accept it
                        if ($existingAssignment->status !== \App\Models\ProjectAssignment::STATUS_ACCEPTED) {
                            $existingAssignment->update([
                                'status' => \App\Models\ProjectAssignment::STATUS_ACCEPTED,
                                'talent_accepted_at' => now(),
                                'assignment_notes' => ($existingAssignment->assignment_notes ?? '') . ' - Auto-accepted during onboarding'
                            ]);

                            // Check if all assignments for this project are accepted and update project status
                            $this->checkAndActivateProject($talentRequest->project_id);
                        }
                    }
                }
                break;
            case 'completed':
                $updateData['completed_at'] = now();
                break;
            case 'rejected':
                // Reset acceptance flags if rejected
                $updateData['admin_accepted'] = false;
                $updateData['talent_accepted'] = false;
                $updateData['both_parties_accepted'] = false;
                break;
        }

        $talentRequest->update($updateData);

        // Handle special actions based on status
        if ($request->status === 'completed') {
            // Stop time-blocking when project is completed
            $talentRequest->stopTimeBlocking();

            // Clear talent availability cache to reflect updated status immediately
            \App\Models\TalentRequest::clearTalentAvailabilityCache($talentRequest->talent_user_id);
        }

        // Send notifications about status change
        $this->notificationService->notifyStatusChange($talentRequest, $talentRequest->getOriginal('status'), $request->status);

        $statusMessage = match($request->status) {
            'approved' => 'Request has been approved by admin. Waiting for talent acceptance to proceed to meeting arrangement.',
            'rejected' => 'Request has been rejected.',
            'meeting_arranged' => 'Meeting has been arranged successfully.',
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
            'acceptance_status' => $talentRequest->fresh()->getAcceptanceStatus(),
            'can_arrange_meeting' => $talentRequest->fresh()->canAdminArrangeMeeting()
        ]);
    }

    /**
     * Check if admin can arrange meeting for a request
     */
    public function canArrangeMeeting(TalentRequest $talentRequest)
    {
        $canArrange = $talentRequest->canAdminArrangeMeeting();

        $reason = '';
        if (!$canArrange) {
            if (!$talentRequest->talent_accepted) {
                $reason = 'Talent has not accepted the request yet';
            } elseif (!$talentRequest->admin_accepted) {
                $reason = 'Admin has not accepted the request yet';
            } elseif (!$talentRequest->both_parties_accepted) {
                $reason = 'Both parties must accept before arranging meeting';
            } elseif ($talentRequest->status !== 'approved') {
                $reason = 'Request status must be approved';
            } else {
                $reason = 'Meeting arrangement requirements not met';
            }
        }

        return response()->json([
            'canArrangeMeeting' => $canArrange,
            'reason' => $reason,
            'talent_accepted' => $talentRequest->talent_accepted,
            'admin_accepted' => $talentRequest->admin_accepted,
            'both_parties_accepted' => $talentRequest->both_parties_accepted,
            'current_status' => $talentRequest->status
        ]);
    }

    /**
     * Admin accepts a talent request (separate from approval)
     */
    public function adminAcceptRequest(Request $request, TalentRequest $talentRequest)
    {
        // Check if already accepted
        if ($talentRequest->admin_accepted) {
            return response()->json([
                'success' => false,
                'message' => 'Admin has already accepted this request.'
            ], 400);
        }

        // Check if request is in valid state for acceptance
        if ($talentRequest->status === 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot accept a rejected request.'
            ], 400);
        }

        // Mark admin as accepted
        $oldStatus = $talentRequest->status;
        $talentRequest->markAdminAccepted();

        // Refresh to get updated data
        $talentRequest->refresh();

        // Send notifications about acceptance
        $this->notificationService->notifyStatusChange($talentRequest, $oldStatus, $talentRequest->status);

        return response()->json([
            'success' => true,
            'message' => 'Admin acceptance recorded successfully!',
            'admin_accepted' => true,
            'both_parties_accepted' => $talentRequest->both_parties_accepted,
            'acceptance_status' => $talentRequest->getAcceptanceStatus(),
            'workflow_progress' => $talentRequest->getWorkflowProgress(),
            'can_arrange_meeting' => $talentRequest->canAdminArrangeMeeting()
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
        try {
            // Load talent with user relationship
            $talent->load('user');

            // Check if user exists
            if (!$talent->user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found for this talent'
                ], 404);
            }

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
                'skill_count' => count($talentSkills),
                'experience_years' => $talent->experience_years ?? 0
            ];

            // Format skills for display - simplified structure
            $formattedSkills = collect($talentSkills)->map(function($skill) {
                if (is_array($skill)) {
                    return [
                        'name' => $skill['skill_name'] ?? ($skill['name'] ?? 'Unknown'),
                        'proficiency' => $skill['proficiency'] ?? ($skill['level'] ?? 'intermediate'),
                        'completed_date' => $skill['completed_date'] ?? ($skill['acquired_at'] ?? null)
                    ];
                }
                return ['name' => (string)$skill, 'proficiency' => 'intermediate', 'completed_date' => null];
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
                'success' => true,
                'talent' => [
                    'id' => $talent->id,
                    'name' => $talent->user->name,
                    'email' => $talent->user->email,
                    'phone' => $talent->user->phone ?? null,
                    'location' => $talent->user->location ?? null,
                    'job' => $talent->user->pekerjaan ?? null,
                    'bio' => $talent->user->bio ?? null,
                    'experience_level' => $talent->experience_level ?? null,
                    'avatar' => $talent->user->avatar ? asset('storage/' . $talent->user->avatar) : null,
                    'is_active' => $talent->is_active,
                    'joined_date' => $talent->created_at->format('M d, Y H:i'),
                    'formatted_skills' => $formattedSkills,
                    'portfolio' => $portfolio,
                    'portfolio_url' => $talent->portfolio_url ?? null,
                    'stats' => $stats
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getTalentDetails: ' . $e->getMessage(), [
                'talent_id' => $talent->id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching talent details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get talent details for modal view (project context)
     * For now, allows any authenticated user - should be improved with proper project access control
     */
    public function getProjectTalentDetails(Talent $talent)
    {
        try {
            // For now, just ensure user is authenticated
            // TODO: Add proper project-specific access control
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            // Use the existing logic for getting talent details
            return $this->getTalentDetails($talent);

        } catch (\Exception $e) {
            Log::error('Error in getProjectTalentDetails: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch talent details'
            ], 500);
        }
    }

    /**
     * Get talent details by user ID for talent requests (project context)
     * For now, allows any authenticated user - should be improved with proper project access control
     */
    public function getProjectTalentDetailsByUserId(User $user)
    {
        try {
            // For now, just ensure user is authenticated
            // TODO: Add proper project-specific access control
            $authUser = Auth::user();
            if (!$authUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            // Find talent record for this user
            $talent = Talent::where('user_id', $user->id)->first();

            if (!$talent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Talent record not found for this user'
                ], 404);
            }

            // Use the project-specific access control
            return $this->getProjectTalentDetails($talent);

        } catch (\Exception $e) {
            Log::error('Error in getProjectTalentDetailsByUserId: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch talent details'
            ], 500);
        }
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
            $recruiterName = $recruiter->user->name ?? 'Unknown Recruiter';
            $userId = $recruiter->user_id;

            // Check if the recruiter has active talent requests
            $activeRequestsCount = TalentRequest::where('recruiter_id', $recruiter->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->count();

            if ($activeRequestsCount > 0) {
                $message = "Cannot delete recruiter '{$recruiterName}' because they have {$activeRequestsCount} active talent request(s). Please complete or cancel these requests first.";

                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }

                return redirect()->back()->with('error', $message);
            }

            // Begin transaction for data integrity
            DB::beginTransaction();

            // Update completed talent requests to remove recruiter reference
            TalentRequest::where('recruiter_id', $recruiter->id)
                ->whereIn('status', ['completed', 'cancelled'])
                ->update(['recruiter_id' => null]);

            // Delete the recruiter (this will soft-delete due to SoftDeletes trait)
            $recruiter->delete();

            // Also delete the user account
            if ($userId) {
                User::find($userId)?->delete();
            }

            DB::commit();

            $successMessage = "Perekrut '{$recruiterName}' berhasil dihapus!";

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage
                ]);
            }

            return redirect()->route('talent_admin.manage_recruiters')->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting recruiter: ' . $e->getMessage());

            $errorMessage = 'Terjadi kesalahan saat menghapus perekrut. Silakan coba lagi.';

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['general' => ['Terjadi kesalahan sistem. Silakan coba lagi.']]
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Clear dashboard cache manually
     */
    public function clearDashboardCache()
    {
        $user = Auth::user();

        // Clear multiple cache keys
        Cache::forget('talent_admin_dashboard_stats');
        Cache::forget('talent_admin_recent_activity_' . $user->id);
        Cache::forget('talent_admin_recent_activity'); // Legacy cache key

        // Also clear for other admins to ensure consistency
        $this->invalidateAllAdminCaches();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard cache cleared successfully'
        ]);
    }

    /**
     * Invalidate recent activity cache (called when new requests are created)
     */
    public function invalidateRecentActivityCache()
    {
        // Clear cache for all admin users since we don't know who's viewing
        $this->invalidateAllAdminCaches();

        Log::info('Talent admin recent activity cache invalidated');
    }

    /**
     * Clear caches for all talent admin users
     */
    private function invalidateAllAdminCaches()
    {
        try {
            // Get all talent admin users
            $talentAdmins = User::whereHas('roles', function($query) {
                $query->where('name', 'talent_admin');
            })->get();

            // Clear user-specific cache for each admin
            foreach ($talentAdmins as $admin) {
                Cache::forget('talent_admin_recent_activity_' . $admin->id);
            }

            // Clear general cache keys
            Cache::forget('talent_admin_dashboard_stats');
            Cache::forget('talent_admin_recent_activity');

            Log::info('Cleared dashboard caches for ' . $talentAdmins->count() . ' talent admins');
        } catch (\Exception $e) {
            Log::error('Failed to clear talent admin dashboard caches: ' . $e->getMessage());
        }
    }

    /**
     * Get dashboard data for AJAX refresh
     */
    public function getDashboardData()
    {
        $user = Auth::user();

        // Get current request count
        $currentTotalRequests = TalentRequest::count();
        $currentPendingRequests = TalentRequest::where('status', 'pending')->count();

        // Get recent requests
        $recentRequests = TalentRequest::select(['id', 'project_title', 'status', 'created_at', 'recruiter_id', 'talent_user_id'])
            ->with([
                'recruiter:id,user_id',
                'recruiter.user:id,name,avatar',
                'talentUser:id,name,avatar'
            ])
            ->whereNotNull('recruiter_id')
            ->whereNotNull('talent_user_id')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Check if there are new requests since last check
        $lastCheckTime = session('last_dashboard_check', now()->subMinutes(1));
        $newRequestsCount = TalentRequest::where('created_at', '>', $lastCheckTime)->count();

        // Update last check time
        session(['last_dashboard_check' => now()]);

        return response()->json([
            'success' => true,
            'totalRequests' => $currentTotalRequests,
            'pendingRequests' => $currentPendingRequests,
            'newRequestsCount' => $newRequestsCount,
            'recentRequests' => $recentRequests->map(function($request) {
                return [
                    'id' => $request->id,
                    'project_title' => $request->project_title,
                    'status' => $request->status,
                    'recruiter_name' => $request->recruiter?->user?->name ?? 'Unknown',
                    'talent_name' => $request->talentUser?->name ?? 'Unknown',
                    'created_at' => $request->created_at->format('M d, Y H:i'),
                    'time_ago' => $request->created_at->diffForHumans()
                ];
            })
        ]);
    }

    /**
     * Suggest talent conversion to a trainee
     */
    public function suggestConversion(Request $request, User $user)
    {
        try {
            // Validate that the user is a trainee
            if (!$user->hasRole('trainee')) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a trainee'
                ], 400);
            }

            // Check if user already has talent role
            if ($user->hasRole('talent')) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already a talent'
                ], 400);
            }

            // Get user's readiness score
            $readinessScore = $this->conversionTracking->calculateReadinessScore($user);

            // Validate readiness score (should be high enough for suggestion)
            if ($readinessScore < 70) {
                return response()->json([
                    'success' => false,
                    'message' => 'User readiness score is too low for conversion suggestion'
                ], 400);
            }

            // Get user's skills and course completion data
            $completedCourses = $user->courseProgress()->where('progress', 100)->count();
            $totalSkills = 0;
            if ($user->talent_skills) {
                $skills = is_string($user->talent_skills) ? json_decode($user->talent_skills, true) : $user->talent_skills;
                $totalSkills = is_array($skills) ? count($skills) : 0;
            }

            // Create conversion suggestion message
            $suggestionMessage = $this->generateConversionMessage($user, $readinessScore, $completedCourses, $totalSkills);

            // Store the suggestion notification for the user
            $this->storeConversionSuggestion($user, $suggestionMessage, $readinessScore, $completedCourses, $totalSkills);

            // Log the suggestion
            Log::info('Conversion suggestion sent', [
                'admin_id' => Auth::id(),
                'target_user_id' => $user->id,
                'readiness_score' => $readinessScore,
                'completed_courses' => $completedCourses,
                'skills_count' => $totalSkills
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Conversion suggestion sent successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ],
                'suggestion_data' => [
                    'readiness_score' => $readinessScore,
                    'completed_courses' => $completedCourses,
                    'skills_count' => $totalSkills,
                    'message' => $suggestionMessage
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send conversion suggestion', [
                'user_id' => $user->id,
                'admin_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send conversion suggestion'
            ], 500);
        }
    }

    /**
     * Generate personalized conversion message
     */
    private function generateConversionMessage(User $user, float $readinessScore, int $completedCourses, int $totalSkills): string
    {
        $name = $user->name;

        if ($readinessScore >= 90) {
            return "Hi {$name}! 🌟 You've demonstrated exceptional learning progress with {$completedCourses} completed courses and {$totalSkills} skills. You're ready to showcase your expertise as a professional talent!";
        } elseif ($readinessScore >= 80) {
            return "Hello {$name}! 🚀 Your learning journey has been impressive with {$completedCourses} courses completed and {$totalSkills} verified skills. Consider becoming a discoverable talent to connect with potential employers!";
        } else {
            return "Hi {$name}! 📈 You've made great progress with {$completedCourses} completed courses and {$totalSkills} skills. You're ready to take the next step and become a professional talent!";
        }
    }

    /**
     * Store conversion suggestion for the user
     */
    private function storeConversionSuggestion(User $user, string $message, float $readinessScore, int $completedCourses, int $totalSkills): void
    {
        // Create a session-based notification that will be shown when the user logs in
        // In a production system, you might want to store this in the database

        $suggestionData = [
            'type' => 'conversion_suggestion',
            'message' => $message,
            'reason' => "Based on your {$completedCourses} completed courses and {$totalSkills} verified skills, you're ready for professional opportunities.",
            'readiness_score' => $readinessScore,
            'skill_count' => $totalSkills,
            'course_count' => $completedCourses,
            'action_url' => route('profile.edit') . '#talent-settings',
            'suggested_by' => Auth::user()->name,
            'suggested_at' => now()->format('M d, Y H:i'),
            'expires_at' => now()->addDays(7)->format('M d, Y H:i')
        ];

        // Store in cache with user-specific key (7 days expiration)
        Cache::put("conversion_suggestion_{$user->id}", $suggestionData, now()->addDays(7));

        // Also store in session if the user is currently logged in
        if (Auth::check() && Auth::id() === $user->id) {
            session()->flash('smart_talent_suggestion', $suggestionData);
        }
    }

    /**
     * Get talent details by user ID for talent requests (original admin method)
     */
    public function getTalentDetailsByUserId(User $user)
    {
        // Find talent record for this user
        $talent = Talent::where('user_id', $user->id)->first();

        if (!$talent) {
            return response()->json([
                'success' => false,
                'message' => 'Talent record not found for this user'
            ], 404);
        }

        // Use the existing getTalentDetails logic (admin access)
        return $this->getTalentDetails($talent);
    }

    /**
     * Check if all assignments for a project are accepted and activate the project
     */
    private function checkAndActivateProject($projectId)
    {
        try {
            $project = \App\Models\Project::find($projectId);
            if (!$project) {
                return;
            }

            // Only transition from 'approved' to 'active'
            if ($project->status !== \App\Models\Project::STATUS_APPROVED) {
                return;
            }

            // Check if all project assignments are accepted
            $totalAssignments = $project->assignments()->count();
            $acceptedAssignments = $project->assignments()
                ->where('status', \App\Models\ProjectAssignment::STATUS_ACCEPTED)
                ->count();

            // If all assignments are accepted, transition project to active
            if ($totalAssignments > 0 && $acceptedAssignments === $totalAssignments) {
                $project->update([
                    'status' => \App\Models\Project::STATUS_ACTIVE
                ]);
            }
        } catch (\Exception $e) {
            // Silently handle errors to avoid disrupting the onboarding process
        }
    }

    /**
     * Manage projects for talent admins (especially closure requests)
     */
    public function manageProjects(Request $request)
    {
        $query = \App\Models\Project::with(['recruiter.user', 'assignments.talent.user'])
            ->orderBy('updated_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('recruiter.user', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $projects = $query->paginate(15);

        // Append query parameters to pagination links
        $projects->appends($request->query());

        $title = 'Manage Projects';
        $roles = 'Talent Admin';
        $assignedKelas = [];
        $user = Auth::user();

        return view('admin.talent_admin.manage_projects', compact(
            'projects',
            'title',
            'roles',
            'assignedKelas',
            'user'
        ));
    }
}
