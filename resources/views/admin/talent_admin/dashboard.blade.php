@extends('layout.template.mainTemplate')

@section('title', 'Talent Admin Dashboard')
@section('container')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Talent Scouting Dashboard</h1>
    </div>

    <!-- Content Row - Statistics Cards -->
    <div class="row">
        <!-- Total Talents Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Talents</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTalents }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Talents Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Talents</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeTalents }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Recruiters Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Recruiters</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRecruiters }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Recruiters Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Active Recruiters</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeRecruiters }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-search fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Management Cards -->
    <div class="row">
        <!-- Manage Talents -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Manage Talents</h6>
                </div>
                <div class="card-body">
                    <p>View, add, and manage talent profiles in the system.</p>
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('talent_admin.manage_talents') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-users"></i> View All Talents
                            </a>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-success btn-block" onclick="alert('Feature coming soon!')">
                                <i class="fas fa-plus"></i> Add New Talent
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manage Recruiters -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Manage Recruiters</h6>
                </div>
                <div class="card-body">
                    <p>View, add, and manage recruiter accounts in the system.</p>
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('talent_admin.manage_recruiters') }}" class="btn btn-info btn-block">
                                <i class="fas fa-building"></i> View All Recruiters
                            </a>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-success btn-block" onclick="alert('Feature coming soon!')">
                                <i class="fas fa-plus"></i> Add New Recruiter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Row -->
    <div class="row">
        <!-- Recent Talents -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Talents</h6>
                </div>
                <div class="card-body">
                    @forelse($latestTalents as $talent)
                    <div class="d-flex align-items-center mb-3">
                        @if($talent->user->avatar)
                            <img class="rounded-circle mr-3" src="{{ asset('storage/' . $talent->user->avatar) }}"
                                 alt="{{ $talent->user->name }}" style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                        @endif
                        <div>
                            <h6 class="mb-0">{{ $talent->user->name }}</h6>
                            <small class="text-muted">{{ $talent->user->pekerjaan }}</small>
                        </div>
                        <div class="ml-auto">
                            <span class="badge badge-{{ $talent->is_active ? 'success' : 'secondary' }}">
                                {{ $talent->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">No talents registered yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Recruiters -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Recruiters</h6>
                </div>
                <div class="card-body">
                    @forelse($latestRecruiters as $recruiter)
                    <div class="d-flex align-items-center mb-3">
                        @if($recruiter->user->avatar)
                            <img class="rounded-circle mr-3" src="{{ asset('storage/' . $recruiter->user->avatar) }}"
                                 alt="{{ $recruiter->user->name }}" style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                        @endif
                        <div>
                            <h6 class="mb-0">{{ $recruiter->user->name }}</h6>
                            <small class="text-muted">{{ $recruiter->user->pekerjaan }}</small>
                        </div>
                        <div class="ml-auto">
                            <span class="badge badge-{{ $recruiter->is_active ? 'success' : 'secondary' }}">
                                {{ $recruiter->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">No recruiters registered yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
