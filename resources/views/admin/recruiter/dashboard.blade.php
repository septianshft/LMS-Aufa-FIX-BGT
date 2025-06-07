@extends('layout.template.mainTemplate')

@section('title', 'Recruiter Dashboard')
@section('container')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Recruiter Dashboard</h1>
        <div class="d-none d-sm-inline-block">
            <span class="text-muted">Welcome back, {{ $user->name }}!</span>
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Welcome Back!
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Hello, {{ $user->name }}
                            </div>
                            <div class="text-gray-600 mt-2">
                                Discover talented individuals and connect with potential candidates for your opportunities.
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-search fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Available Talents
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $talents->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Status
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $user->recruiter && $user->recruiter->is_active ? 'Active' : 'Inactive' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Member Since
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $user->created_at->format('M Y') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Talent Discovery Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users mr-2"></i>Discover Talents
            </h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Options:</div>
                    <a class="dropdown-item" href="#" onclick="refreshTalents()">
                        <i class="fas fa-sync-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Refresh
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($talents->count() > 0)
                <div class="row">
                    @foreach($talents as $talent)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border-left-primary shadow-sm h-100">
                                <div class="card-body">
                                    <!-- Profile Header -->
                                    <div class="text-center mb-3">
                                        @if($talent->user->avatar)
                                            <img class="rounded-circle mb-3"
                                                 src="{{ asset('storage/' . $talent->user->avatar) }}"
                                                 alt="{{ $talent->user->name }}"
                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary mx-auto mb-3 d-flex align-items-center justify-content-center"
                                                 style="width: 80px; height: 80px;">
                                                <i class="fas fa-user-tie fa-2x text-white"></i>
                                            </div>
                                        @endif

                                        <h6 class="font-weight-bold text-gray-800 mb-1">{{ $talent->user->name }}</h6>

                                        @if($talent->user->pekerjaan)
                                            <p class="text-muted small mb-2">{{ $talent->user->pekerjaan }}</p>
                                        @endif

                                        <span class="badge badge-success">Active Talent</span>
                                    </div>

                                    <!-- Contact Info -->
                                    <div class="mb-3">
                                        <div class="small text-gray-600">
                                            <i class="fas fa-envelope fa-sm mr-2"></i>{{ $talent->user->email }}
                                        </div>
                                        @if($talent->user->no_telp)
                                            <div class="small text-gray-600 mt-1">
                                                <i class="fas fa-phone fa-sm mr-2"></i>{{ $talent->user->no_telp }}
                                            </div>
                                        @endif
                                        @if($talent->user->alamat)
                                            <div class="small text-gray-600 mt-1">
                                                <i class="fas fa-map-marker-alt fa-sm mr-2"></i>{{ Str::limit($talent->user->alamat, 30) }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Member Since -->
                                    <div class="text-center">
                                        <small class="text-muted">
                                            Member since {{ $talent->created_at->format('M Y') }}
                                        </small>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="mt-3 text-center">
                                        <button type="button" class="btn btn-primary btn-sm"
                                                onclick="viewTalentDetails('{{ $talent->user->name }}', '{{ $talent->user->email }}', '{{ $talent->user->pekerjaan ?? 'Not specified' }}', '{{ $talent->user->alamat ?? 'Not specified' }}', '{{ $talent->user->no_telp ?? 'Not specified' }}')">
                                            <i class="fas fa-eye fa-sm mr-1"></i>View Details
                                        </button>
                                        <a href="mailto:{{ $talent->user->email }}" class="btn btn-outline-primary btn-sm ml-1">
                                            <i class="fas fa-envelope fa-sm mr-1"></i>Contact
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $talents->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-tie fa-4x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No Talents Available</h5>
                    <p class="text-muted">There are currently no active talents in the system.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Talent Details Modal -->
<div class="modal fade" id="talentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="talentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="talentDetailsModalLabel">
                    <i class="fas fa-user-tie mr-2"></i>Talent Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Full Name:</label>
                            <p id="modalTalentName" class="text-gray-800"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Email:</label>
                            <p id="modalTalentEmail" class="text-gray-800"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Phone:</label>
                            <p id="modalTalentPhone" class="text-gray-800"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Profession:</label>
                            <p id="modalTalentProfession" class="text-gray-800"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Address:</label>
                            <p id="modalTalentAddress" class="text-gray-800"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="contactTalent()">
                    <i class="fas fa-envelope mr-1"></i>Send Email
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentTalentEmail = '';

function viewTalentDetails(name, email, profession, address, phone) {
    document.getElementById('modalTalentName').textContent = name;
    document.getElementById('modalTalentEmail').textContent = email;
    document.getElementById('modalTalentProfession').textContent = profession;
    document.getElementById('modalTalentAddress').textContent = address;
    document.getElementById('modalTalentPhone').textContent = phone;
    currentTalentEmail = email;

    $('#talentDetailsModal').modal('show');
}

function contactTalent() {
    if (currentTalentEmail) {
        window.location.href = 'mailto:' + currentTalentEmail;
    }
}

function refreshTalents() {
    location.reload();
}
</script>
@endsection
