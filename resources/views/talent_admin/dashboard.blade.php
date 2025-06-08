@extends('layout.template.mainTemplate')

@section('title', 'Talent Admin Dashboard')
@section('container')
<div class="container-fluid">
    <!-- Page Heading with Welcome Message -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt text-primary me-2"></i>
                Talent Scouting Dashboard
            </h1>
            <p class="mb-0 text-muted">Welcome back! Here's what's happening with your talent scouting platform.</p>
        </div>
        <div class="d-none d-sm-inline-block">
            <span class="badge bg-success px-3 py-2">
                <i class="fas fa-check-circle me-1"></i>
                System Online
            </span>
        </div>
    </div>

    <!-- Overview Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Talents Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-white-50 small fw-bold text-uppercase mb-1">Total Talents</div>
                            <div class="h3 mb-0 fw-bold">{{ $totalTalents }}</div>
                            <div class="small text-white-50">
                                <i class="fas fa-arrow-up me-1"></i>
                                Active: {{ $activeTalents }}
                            </div>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-user-tie fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Recruiters Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm bg-gradient-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-white-50 small fw-bold text-uppercase mb-1">Total Recruiters</div>
                            <div class="h3 mb-0 fw-bold">{{ $totalRecruiters }}</div>
                            <div class="small text-white-50">
                                <i class="fas fa-check-circle me-1"></i>
                                Active: {{ $activeRecruiters }}
                            </div>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-building fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Requests Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm bg-gradient-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-white-50 small fw-bold text-uppercase mb-1">Total Requests</div>
                            <div class="h3 mb-0 fw-bold">{{ $totalRequests }}</div>
                            <div class="small text-white-50">
                                <i class="fas fa-thumbs-up me-1"></i>
                                Approved: {{ $approvedRequests }}
                            </div>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-handshake fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm bg-gradient-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-white-50 small fw-bold text-uppercase mb-1">Pending Requests</div>
                            <div class="h3 mb-0 fw-bold">{{ $pendingRequests }}</div>
                            <div class="small text-white-50">
                                @if($pendingRequests > 0)
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Needs Attention
                                @else
                                    <i class="fas fa-check me-1"></i>
                                    All Clear
                                @endif
                            </div>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-clock fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Management Cards -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-4">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-cogs text-primary me-2"></i>
                            Quick Management Actions
                        </h5>
                        <span class="badge bg-light text-dark ms-auto">Admin Panel</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Manage Talents -->
                        <div class="col-lg-4">
                            <div class="card border-0 bg-light h-100 hover-shadow">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <div class="bg-primary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fas fa-users text-white fa-lg"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold mb-2">Manage Talents</h6>
                                    <p class="text-muted small mb-3">View and manage talent profiles, skills, and availability status.</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('talent_admin.manage_talents') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i> View All Talents
                                        </a>
                                        <button class="btn btn-outline-success btn-sm" onclick="showComingSoon('Add New Talent')">
                                            <i class="fas fa-plus me-1"></i> Add New Talent
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Manage Recruiters -->
                        <div class="col-lg-4">
                            <div class="card border-0 bg-light h-100 hover-shadow">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <div class="bg-info bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fas fa-building text-white fa-lg"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold mb-2">Manage Recruiters</h6>
                                    <p class="text-muted small mb-3">Oversee recruiter accounts and company information.</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('talent_admin.manage_recruiters') }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye me-1"></i> View All Recruiters
                                        </a>
                                        <button class="btn btn-outline-success btn-sm" onclick="showComingSoon('Add New Recruiter')">
                                            <i class="fas fa-plus me-1"></i> Add New Recruiter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Manage Requests -->
                        <div class="col-lg-4">
                            <div class="card border-0 bg-light h-100 hover-shadow">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <div class="bg-warning bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fas fa-handshake text-white fa-lg"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold mb-2">Manage Requests</h6>
                                    <p class="text-muted small mb-3">Review and process talent scouting requests from recruiters.</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('talent_admin.manage_requests') }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-list me-1"></i> View All Requests
                                        </a>
                                        <a href="{{ route('talent_admin.manage_requests', ['status' => 'pending']) }}" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-clock me-1"></i> Pending ({{ $pendingRequests }})
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="row mb-4">
        <!-- Recent Requests -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="fas fa-clock text-warning me-2"></i>
                                Recent Talent Requests
                            </h5>
                            <p class="text-muted small mb-0">Latest requests from recruiters requiring your attention</p>
                        </div>
                        <a href="{{ route('talent_admin.manage_requests') }}" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-eye me-1"></i> View All Requests
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($latestRequests as $request)
                    <div class="d-flex align-items-center p-3 mb-3 border rounded-3 bg-light hover-bg-white transition-all">
                        <div class="me-3">
                            @if($request->recruiter->user->avatar)
                                <img class="rounded-circle shadow-sm" src="{{ asset('storage/' . $request->recruiter->user->avatar) }}"
                                     alt="{{ $request->recruiter->user->name }}" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-gradient-info d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                                    <i class="fas fa-building text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                <h6 class="mb-0 fw-bold text-dark me-2">{{ $request->recruiter->user->name }}</h6>
                                <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1 text-primary fw-semibold">{{ $request->project_title ?? 'Untitled Project' }}</p>
                            <p class="mb-0 text-muted small">
                                <i class="fas fa-user me-1"></i>
                                Requesting talent: <span class="fw-semibold">{{ $request->talentUser->name ?? ($request->talent->user->name ?? 'Unknown') }}</span>
                            </p>
                        </div>
                        <div class="me-3">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-warning text-dark',
                                    'approved' => 'bg-success',
                                    'meeting_arranged' => 'bg-info',
                                    'agreement_reached' => 'bg-primary',
                                    'onboarded' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    'completed' => 'bg-success'
                                ];
                                $statusIcons = [
                                    'pending' => 'fas fa-clock',
                                    'approved' => 'fas fa-check',
                                    'meeting_arranged' => 'fas fa-calendar',
                                    'agreement_reached' => 'fas fa-handshake',
                                    'onboarded' => 'fas fa-user-plus',
                                    'rejected' => 'fas fa-times',
                                    'completed' => 'fas fa-flag-checkered'
                                ];
                            @endphp
                            <span class="badge {{ $statusColors[$request->status] ?? 'bg-secondary' }} px-3 py-2">
                                <i class="{{ $statusIcons[$request->status] ?? 'fas fa-question' }} me-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                            </span>
                        </div>
                        <div>
                            <a href="{{ route('talent_admin.show_request', $request) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-inbox fa-3x text-muted"></i>
                        </div>
                        <h6 class="text-muted">No talent requests yet</h6>
                        <p class="text-muted small">New requests will appear here when recruiters submit them.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users Section -->
    <div class="row">
        <!-- Recent Talents -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient-primary text-white border-0 py-4">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0 fw-bold text-white">
                            <i class="fas fa-user-tie me-2"></i>
                            Recent Talents
                        </h6>
                        <span class="badge bg-white text-primary ms-auto">{{ $latestTalents->count() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($latestTalents as $talent)
                    <div class="d-flex align-items-center p-2 mb-3 rounded hover-bg-light transition-all">
                        <div class="me-3">
                            @if($talent->avatar)
                                <img class="rounded-circle shadow-sm" src="{{ asset('storage/' . $talent->avatar) }}"
                                     alt="{{ $talent->name }}" style="width: 45px; height: 45px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-gradient-secondary d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-semibold">{{ $talent->name }}</h6>
                            <p class="text-muted small mb-0">
                                <i class="fas fa-briefcase me-1"></i>
                                {{ $talent->pekerjaan ?? 'Position not specified' }}
                            </p>
                        </div>
                        <div>
                            <span class="badge {{ $talent->is_active_talent ? 'bg-success' : 'bg-secondary' }} px-2 py-1">
                                <i class="fas fa-{{ $talent->is_active_talent ? 'check-circle' : 'pause-circle' }} me-1"></i>
                                {{ $talent->is_active_talent ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-user-plus fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No talents registered yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Recruiters -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-gradient-info text-white border-0 py-4">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0 fw-bold text-white">
                            <i class="fas fa-building me-2"></i>
                            Recent Recruiters
                        </h6>
                        <span class="badge bg-white text-info ms-auto">{{ $latestRecruiters->count() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($latestRecruiters as $recruiter)
                    <div class="d-flex align-items-center p-2 mb-3 rounded hover-bg-light transition-all">
                        <div class="me-3">
                            @if($recruiter->avatar)
                                <img class="rounded-circle shadow-sm" src="{{ asset('storage/' . $recruiter->avatar) }}"
                                     alt="{{ $recruiter->name }}" style="width: 45px; height: 45px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-gradient-secondary d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                                    <i class="fas fa-building text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-semibold">{{ $recruiter->name }}</h6>
                            <p class="text-muted small mb-0">
                                <i class="fas fa-building me-1"></i>
                                {{ $recruiter->company_name ?? $recruiter->pekerjaan ?? 'Company not specified' }}
                            </p>
                        </div>
                        <div>
                            @php
                                // Check if user has recruiter role since we don't have a separate is_active field
                                $isActiveRecruiter = $recruiter->hasRole('recruiter');
                            @endphp
                            <span class="badge {{ $isActiveRecruiter ? 'bg-success' : 'bg-secondary' }} px-2 py-1">
                                <i class="fas fa-{{ $isActiveRecruiter ? 'check-circle' : 'pause-circle' }} me-1"></i>
                                {{ $isActiveRecruiter ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-user-plus fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No recruiters registered yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
/* Custom gradient backgrounds */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
}

.bg-gradient-secondary {
    background: linear-gradient(135deg, #b2bec3 0%, #636e72 100%);
}

/* Hover effects */
.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transition: all 0.3s ease;
}

.hover-bg-white:hover {
    background-color: #fff !important;
    transition: all 0.2s ease;
}

.hover-bg-light:hover {
    background-color: #f8f9fa !important;
    transition: all 0.2s ease;
}

.transition-all {
    transition: all 0.2s ease;
}

/* Status badge improvements */
.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

/* Card improvements */
.card {
    border-radius: 0.75rem;
    overflow: hidden;
}

.card-header {
    border-radius: 0.75rem 0.75rem 0 0 !important;
}

/* Icon improvements */
.fa-3x {
    font-size: 3rem;
}

/* Button improvements */
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.5rem;
}

/* Text improvements */
.fw-bold {
    font-weight: 700 !important;
}

.fw-semibold {
    font-weight: 600 !important;
}
</style>
@endsection

@section('scripts')
<script>
function showComingSoon(feature) {
    // Try to use SweetAlert if available, otherwise use regular alert
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Coming Soon!',
            text: `${feature} feature will be available in the next update.`,
            icon: 'info',
            confirmButtonText: 'Got it!',
            confirmButtonColor: '#667eea'
        });
    } else {
        alert(`Coming Soon!\n\n${feature} feature will be available in the next update.`);
    }
}

// Add some loading animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate cards on load
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

@if (session('success'))
    <script>
        // Show success message if available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#667eea'
            });
        } else {
            alert('Success: {{ session('success') }}');
        }
    </script>
@endif

@if (session('error'))
    <script>
        // Show error message if available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#667eea'
            });
        } else {
            alert('Error: {{ session('error') }}');
        }
    </script>
@endif
@endsection
