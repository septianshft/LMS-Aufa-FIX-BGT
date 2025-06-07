<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Talent;
use App\Models\TalentRequest;
use App\Models\Recruiter;

class RecruiterController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $title = 'Recruiter Dashboard';
        $roles = 'Recruiter';
        $assignedKelas = [];

        // Get current recruiter
        $recruiter = $user->recruiter;

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

        // Get my talent requests summary
        $myRequests = collect();
        if ($recruiter) {
            $myRequests = TalentRequest::with(['talent.user'])
                ->where('recruiter_id', $recruiter->id)
                ->latest()
                ->take(5)
                ->get();
        }

        return view('admin.recruiter.dashboard', compact('user', 'title', 'roles', 'assignedKelas', 'talents', 'myRequests', 'recruiter'));
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

        return response()->json([
            'success' => true,
            'message' => 'Talent request submitted successfully! The Talent Admin will review your request.',
            'request_id' => $talentRequest->id
        ]);
    }

    public function myRequests()
    {
        $user = Auth::user();
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
}
