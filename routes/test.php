<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Talent;
use App\Models\TalentRequest;
use App\Models\Recruiter;

Route::get('/test-dashboard-data', function () {
    // Test data availability and integrity
    $results = [];

    try {
        // Test 1: Check if talents exist
        $totalTalents = Talent::count();
        $activeTalents = Talent::where('is_active', true)->count();
        $results['talents'] = [
            'total' => $totalTalents,
            'active' => $activeTalents,
            'status' => $totalTalents > 0 ? 'PASS' : 'FAIL'
        ];

        // Test 2: Check if users with talent role exist
        $talentUsers = User::whereHas('roles', function($query) {
            $query->where('name', 'talent');
        })->count();

        $scoutingReady = User::where('available_for_scouting', true)->count();
        $results['talent_users'] = [
            'total' => $talentUsers,
            'scouting_ready' => $scoutingReady,
            'status' => $scoutingReady > 0 ? 'PASS' : 'WARNING'
        ];

        // Test 3: Check if recruiters exist
        $totalRecruiters = Recruiter::count();
        $activeRecruiters = Recruiter::where('is_active', true)->count();
        $results['recruiters'] = [
            'total' => $totalRecruiters,
            'active' => $activeRecruiters,
            'status' => $totalRecruiters > 0 ? 'PASS' : 'FAIL'
        ];

        // Test 4: Check talent requests
        $totalRequests = TalentRequest::count();
        $pendingRequests = TalentRequest::where('status', 'pending')->count();
        $results['requests'] = [
            'total' => $totalRequests,
            'pending' => $pendingRequests,
            'status' => 'PASS'
        ];

        // Test 5: Check specific data for dashboard
        $talentsWithMetrics = Talent::with(['user'])
            ->where('is_active', true)
            ->whereHas('user', function($query) {
                $query->where('available_for_scouting', true);
            })
            ->limit(5)
            ->get();

        $results['dashboard_data'] = [
            'talents_for_display' => $talentsWithMetrics->count(),
            'sample_talent' => $talentsWithMetrics->first() ? [
                'id' => $talentsWithMetrics->first()->id,
                'name' => $talentsWithMetrics->first()->user->name,
                'email' => $talentsWithMetrics->first()->user->email,
                'scouting_ready' => $talentsWithMetrics->first()->user->available_for_scouting
            ] : null,
            'status' => $talentsWithMetrics->count() > 0 ? 'PASS' : 'WARNING'
        ];

        // Test 6: Check for potential issues
        $issues = [];

        if ($totalTalents === 0) {
            $issues[] = 'No talents in database - run seeders';
        }

        if ($scoutingReady === 0) {
            $issues[] = 'No talents available for scouting - check available_for_scouting field';
        }

        if ($totalRecruiters === 0) {
            $issues[] = 'No recruiters in database - run seeders';
        }

        $results['issues'] = $issues;
        $results['overall_status'] = empty($issues) ? 'HEALTHY' : 'NEEDS_ATTENTION';

    } catch (\Exception $e) {
        $results['error'] = $e->getMessage();
        $results['overall_status'] = 'ERROR';
    }

    return response()->json($results, 200, [], JSON_PRETTY_PRINT);
});
