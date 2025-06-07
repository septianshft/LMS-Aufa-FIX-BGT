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
                                My Requests
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $myRequests->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Recent Requests Section -->
    @if($myRequests->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-clipboard-list mr-2"></i>My Recent Talent Requests
            </h6>
            <a href="{{ route('recruiter.my_requests') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-list mr-1"></i>View All
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Talent</th>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myRequests as $request)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($request->talent->user->avatar)
                                        <img class="rounded-circle mr-2" src="{{ asset('storage/' . $request->talent->user->avatar) }}"
                                             alt="{{ $request->talent->user->name }}" style="width: 30px; height: 30px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary mr-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                            <i class="fas fa-user text-white text-xs"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-weight-bold text-sm">{{ $request->talent->user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="font-weight-bold text-sm">{{ $request->project_title }}</div>
                                <div class="text-muted small">{{ Str::limit($request->project_description, 50) }}</div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $request->getStatusBadgeColor() }}">
                                    {{ $request->getFormattedStatus() }}
                                </span>
                            </td>
                            <td class="text-sm">{{ $request->created_at->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

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

                                        @php
                                            $existingRequest = $talent->talentRequests->first();
                                        @endphp

                                        @if($existingRequest)
                                            <span class="badge badge-{{ $existingRequest->getStatusBadgeColor() }}">
                                                {{ $existingRequest->getFormattedStatus() }}
                                            </span>
                                        @else
                                            <span class="badge badge-success">Available</span>
                                        @endif
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

                                        @php
                                            $existingRequest = $talent->talentRequests->first();
                                        @endphp

                                        @if($existingRequest)
                                            @if($existingRequest->status == 'pending')
                                                <button type="button" class="btn btn-warning btn-sm ml-1" disabled>
                                                    <i class="fas fa-clock fa-sm mr-1"></i>Pending
                                                </button>
                                            @elseif($existingRequest->status == 'approved')
                                                <button type="button" class="btn btn-info btn-sm ml-1" disabled>
                                                    <i class="fas fa-check fa-sm mr-1"></i>Approved
                                                </button>
                                            @elseif($existingRequest->status == 'onboarded')
                                                <button type="button" class="btn btn-success btn-sm ml-1" disabled>
                                                    <i class="fas fa-handshake fa-sm mr-1"></i>Onboarded
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-outline-success btn-sm ml-1"
                                                        onclick="openRequestModal('{{ $talent->id }}', '{{ $talent->user->name }}')">
                                                    <i class="fas fa-handshake fa-sm mr-1"></i>Request Again
                                                </button>
                                            @endif
                                        @else
                                            <button type="button" class="btn btn-outline-success btn-sm ml-1"
                                                    onclick="openRequestModal('{{ $talent->id }}', '{{ $talent->user->name }}')">
                                                <i class="fas fa-handshake fa-sm mr-1"></i>Request Talent
                                            </button>
                                        @endif

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

<!-- Talent Request Modal -->
<div class="modal fade" id="talentRequestModal" tabindex="-1" role="dialog" aria-labelledby="talentRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="talentRequestModalLabel">
                    <i class="fas fa-handshake mr-2"></i>Request Talent
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="talentRequestForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Note:</strong> Your request will be reviewed by the Talent Admin who will coordinate a meeting between you and the talent.
                    </div>

                    <input type="hidden" id="requestTalentId" name="talent_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="projectTitle" class="font-weight-bold">Project Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="projectTitle" name="project_title" required
                                       placeholder="e.g., Mobile App Development">
                            </div>

                            <div class="form-group">
                                <label for="budgetRange" class="font-weight-bold">Budget Range</label>
                                <select class="form-control" id="budgetRange" name="budget_range">
                                    <option value="">Select budget range</option>
                                    <option value="Under $1,000">Under $1,000</option>
                                    <option value="$1,000 - $5,000">$1,000 - $5,000</option>
                                    <option value="$5,000 - $10,000">$5,000 - $10,000</option>
                                    <option value="$10,000 - $25,000">$10,000 - $25,000</option>
                                    <option value="$25,000+">$25,000+</option>
                                    <option value="Negotiable">Negotiable</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="projectDuration" class="font-weight-bold">Project Duration</label>
                                <select class="form-control" id="projectDuration" name="project_duration">
                                    <option value="">Select duration</option>
                                    <option value="1-2 weeks">1-2 weeks</option>
                                    <option value="1 month">1 month</option>
                                    <option value="2-3 months">2-3 months</option>
                                    <option value="3-6 months">3-6 months</option>
                                    <option value="6+ months">6+ months</option>
                                    <option value="Ongoing">Ongoing</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="urgencyLevel" class="font-weight-bold">Urgency Level <span class="text-danger">*</span></label>
                                <select class="form-control" id="urgencyLevel" name="urgency_level" required>
                                    <option value="low">Low - Flexible timeline</option>
                                    <option value="medium" selected>Medium - Standard timeline</option>
                                    <option value="high">High - Urgent requirement</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="projectDescription" class="font-weight-bold">Project Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="projectDescription" name="project_description" rows="5" required
                                          placeholder="Describe your project, goals, and what you're looking for..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="requirements" class="font-weight-bold">Specific Requirements</label>
                                <textarea class="form-control" id="requirements" name="requirements" rows="3"
                                          placeholder="List any specific skills, technologies, or qualifications needed..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="recruiterMessage" class="font-weight-bold">Personal Message</label>
                                <textarea class="form-control" id="recruiterMessage" name="recruiter_message" rows="3"
                                          placeholder="Add a personal message to the talent (optional)..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#talentRequestModal').modal('hide')">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane mr-1"></i>Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentTalentEmail = '';
let currentRequestTalentId = '';
let currentRequestTalentName = '';

function viewTalentDetails(name, email, profession, address, phone) {
    document.getElementById('modalTalentName').textContent = name;
    document.getElementById('modalTalentEmail').textContent = email;
    document.getElementById('modalTalentProfession').textContent = profession;
    document.getElementById('modalTalentAddress').textContent = address;
    document.getElementById('modalTalentPhone').textContent = phone;
    currentTalentEmail = email;

    $('#talentDetailsModal').modal('show');
}

function openRequestModal(talentId, talentName) {
    currentRequestTalentId = talentId;
    currentRequestTalentName = talentName;

    // Reset form
    document.getElementById('talentRequestForm').reset();
    document.getElementById('requestTalentId').value = talentId;

    // Update modal title
    document.getElementById('talentRequestModalLabel').innerHTML =
        '<i class="fas fa-handshake mr-2"></i>Request Talent: ' + talentName;

    $('#talentRequestModal').modal('show');
}

function contactTalent() {
    if (currentTalentEmail) {
        window.location.href = 'mailto:' + currentTalentEmail;
    }
}

function refreshTalents() {
    location.reload();
}

// Ensure modal close functionality works
$(document).ready(function() {
    // Handle modal close buttons
    $('.modal .close, .modal [data-dismiss="modal"]').on('click', function() {
        $(this).closest('.modal').modal('hide');
    });
});

// Handle talent request form submission
document.getElementById('talentRequestForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    // Show loading state
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Submitting...';

    fetch('{{ route("recruiter.submit_talent_request") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('Success! Your talent request has been submitted and will be reviewed by the Talent Admin.');

            // Close modal and refresh page
            $('#talentRequestModal').modal('hide');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            alert('Error: ' + (data.message || 'Something went wrong. Please try again.'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: Failed to submit request. Please try again.');
    })
    .finally(() => {
        // Reset button state
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
});
</script>
@endsection
