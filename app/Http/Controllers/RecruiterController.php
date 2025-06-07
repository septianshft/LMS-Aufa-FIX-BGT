<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Talent;

class RecruiterController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $title = 'Recruiter Dashboard';
        $roles = 'Recruiter';
        $assignedKelas = [];

        // Get active talents for discovery
        $talents = Talent::with('user')
            ->where('is_active', true)
            ->whereHas('user', function($query) {
                $query->whereNotNull('name')
                      ->whereNotNull('email');
            })
            ->latest()
            ->paginate(12);

        return view('admin.recruiter.dashboard', compact('user', 'title', 'roles', 'assignedKelas', 'talents'));
    }
}
