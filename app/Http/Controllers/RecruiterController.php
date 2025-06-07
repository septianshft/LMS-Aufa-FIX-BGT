<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecruiterController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $title = 'Recruiter Dashboard';
        $roles = 'Recruiter';
        $assignedKelas = [];

        return view('admin.recruiter.dashboard', compact('user', 'title', 'roles', 'assignedKelas'));
    }
}
