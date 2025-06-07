<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TalentAdminController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $title = 'Talent Admin Dashboard';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        return view('admin.talent_admin.dashboard', compact('user', 'title', 'roles', 'assignedKelas'));
    }
}
