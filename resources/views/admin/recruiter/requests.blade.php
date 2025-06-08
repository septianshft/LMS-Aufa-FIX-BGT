@extends('layout.template.mainTemplate')

@section('title', 'My Talent Requests')
@section('container')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Talent Requests</h1>
        <a href="{{ route('recruiter.dashboard') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Dashboard
        </a>
    </div>

    <!-- Requests Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-clipboard-list mr-2"></i>All My Talent Requests
            </h6>
        </div>
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Talent</th>
                                <th>Project Details</th>
                                <th>Budget & Duration</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($request->talent->user->avatar)
                                                <img class="rounded-circle mr-3" src="{{ asset('storage/' . $request->talent->user->avatar) }}"
                                                     alt="{{ $request->talent->user->name }}" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-secondary mr-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-weight-bold">{{ $request->talent->user->name }}</div>
                                                <div class="text-muted small">{{ $request->talent->user->email }}</div>
                                                @if($request->talent->user->pekerjaan)
                                                    <div class="text-muted small">{{ $request->talent->user->pekerjaan }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-primary">{{ $request->project_title }}</div>
                                        <div class="text-muted small mt-1">{{ Str::limit($request->project_description, 100) }}</div>
                                        @if($request->requirements)
                                            <div class="text-muted small mt-1">
                                                <strong>Requirements:</strong> {{ Str::limit($request->requirements, 80) }}
                                            </div>
                                        @endif
                                        <div class="mt-2">
                                            <span class="badge badge-{{ $request->urgency_level == 'high' ? 'danger' : ($request->urgency_level == 'medium' ? 'warning' : 'info') }}">
                                                {{ ucfirst($request->urgency_level) }} Priority
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($request->budget_range)
                                            <div class="small"><strong>Budget:</strong> {{ $request->budget_range }}</div>
                                        @endif
                                        @if($request->project_duration)
                                            <div class="small"><strong>Duration:</strong> {{ $request->project_duration }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $request->getStatusBadgeColor() }} p-2">
                                            {{ $request->getFormattedStatus() }}
                                        </span>

                                        @if($request->admin_notes)
                                            <div class="text-muted small mt-2">
                                                <strong>Admin Notes:</strong><br>
                                                {{ $request->admin_notes }}
                                            </div>
                                        @endif

                                        @if($request->approved_at)
                                            <div class="text-success small mt-1">
                                                <i class="fas fa-check-circle"></i> Approved {{ $request->approved_at->format('M d, Y') }}
                                            </div>
                                        @endif

                                        @if($request->meeting_arranged_at)
                                            <div class="text-info small mt-1">
                                                <i class="fas fa-calendar"></i> Meeting arranged {{ $request->meeting_arranged_at->format('M d, Y') }}
                                            </div>
                                        @endif

                                        @if($request->onboarded_at)
                                            <div class="text-success small mt-1">
                                                <i class="fas fa-handshake"></i> Onboarded {{ $request->onboarded_at->format('M d, Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">{{ $request->created_at->format('M d, Y') }}</div>
                                        <div class="text-muted small">{{ $request->created_at->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary mb-1"
                                                onclick="viewRequestDetails('{{ addslashes($request->project_title) }}', '{{ addslashes($request->project_description) }}', '{{ addslashes($request->requirements ?? '') }}', '{{ $request->budget_range ?? 'Not specified' }}', '{{ $request->project_duration ?? 'Not specified' }}', '{{ ucfirst($request->urgency_level) }}', '{{ addslashes($request->recruiter_message ?? '') }}')">
                                            <i class="fas fa-eye"></i> View Details
                                        </button>

                                        @if($request->status == 'approved' || $request->status == 'meeting_arranged')
                                            <a href="mailto:{{ $request->talent->user->email }}" class="btn btn-sm btn-outline-success mb-1">
                                                <i class="fas fa-envelope"></i> Contact
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $requests->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-4x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No Talent Requests</h5>
                    <p class="text-muted">You haven't submitted any talent requests yet.</p>
                    <a href="{{ route('recruiter.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i>Discover Talents
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Request Details Modal -->
<div class="modal fade" id="requestDetailsModal" tabindex="-1" role="dialog" aria-labelledby="requestDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestDetailsModalLabel">
                    <i class="fas fa-clipboard-list mr-2"></i>Request Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Project Title:</label>
                            <p id="modalProjectTitle" class="text-gray-800"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Budget Range:</label>
                            <p id="modalBudgetRange" class="text-gray-800"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Project Duration:</label>
                            <p id="modalProjectDuration" class="text-gray-800"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Urgency Level:</label>
                            <p id="modalUrgencyLevel" class="text-gray-800"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Project Description:</label>
                            <p id="modalProjectDescription" class="text-gray-800"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Requirements:</label>
                            <p id="modalRequirements" class="text-gray-800"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Personal Message:</label>
                            <p id="modalRecruiterMessage" class="text-gray-800"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewRequestDetails(title, description, requirements, budget, duration, urgency, message) {
    document.getElementById('modalProjectTitle').textContent = title;
    document.getElementById('modalProjectDescription').textContent = description;
    document.getElementById('modalRequirements').textContent = requirements || 'Not specified';
    document.getElementById('modalBudgetRange').textContent = budget;
    document.getElementById('modalProjectDuration').textContent = duration;
    document.getElementById('modalUrgencyLevel').textContent = urgency;
    document.getElementById('modalRecruiterMessage').textContent = message || 'No personal message';

    $('#requestDetailsModal').modal('show');
}

// Ensure modal close functionality works
$(document).ready(function() {
    $('.close, [data-dismiss="modal"]').on('click', function() {
        $('#requestDetailsModal').modal('hide');
    });
});
</script>
@endsection
