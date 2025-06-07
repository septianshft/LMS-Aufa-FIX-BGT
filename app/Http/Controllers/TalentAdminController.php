<?php

namespace App\Http\Controllers;

use App\Models\Talent;
use App\Models\Recruiter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TalentAdminController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $title = 'Talent Admin Dashboard';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        // Statistics
        $totalTalents = Talent::count();
        $activeTalents = Talent::where('is_active', true)->count();
        $totalRecruiters = Recruiter::count();
        $activeRecruiters = Recruiter::where('is_active', true)->count();

        // Recent registrations (last 30 days)
        $recentTalents = Talent::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $recentRecruiters = Recruiter::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // Recent activity (latest 5 users of each type)
        $latestTalents = Talent::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $latestRecruiters = Recruiter::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.talent_admin.dashboard', compact(
            'user', 'title', 'roles', 'assignedKelas',
            'totalTalents', 'activeTalents', 'totalRecruiters', 'activeRecruiters',
            'recentTalents', 'recentRecruiters', 'latestTalents', 'latestRecruiters'
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
}
