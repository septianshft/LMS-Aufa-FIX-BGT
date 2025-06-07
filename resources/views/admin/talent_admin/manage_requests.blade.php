@extends('layout.template.mainTemplate')

@section('title', 'Manage Talent Requests')

@section('styles')
<style>
/* Ensure proper pagination styling */
.pagination .page-link {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Fix icon alignment in action buttons */
.btn-group .btn {
    padding: 0.25rem 0.5rem;
}

.btn-group .btn i {
    font-size: 11px;
}

/* Ensure table icons are properly sized */
.table .rounded-circle i {
    font-size: 11px;
}

/* Badge improvements */
.badge {
    font-size: 0.75rem;
    padding: 0.25em 0.6em;
}

/* Status badge specific improvements */
.status-badge {
    font-size: 0.7rem;
    padding: 0.3em 0.7em;
    font-weight: 500;
}

/* Filter form improvements */
.form-control-sm {
    font-size: 0.875rem;
}

/* Table cell alignment improvements */
.table td {
    vertical-align: middle;
}

/* Action button improvements */
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

.btn-sm i {
    font-size: 0.7rem;
}

/* Improve hover effects for action buttons */
.btn-outline-primary:hover i,
.btn-outline-warning:hover i,
.btn-outline-danger:hover i {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}
</style>
@endsection

@section('container')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Talent Requests</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Requests</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('talent_admin.manage_requests') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" onchange="document.getElementById('filterForm').submit();">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="meeting_arranged" {{ request('status') == 'meeting_arranged' ? 'selected' : '' }}>Meeting Arranged</option>
                                <option value="agreement_reached" {{ request('status') == 'agreement_reached' ? 'selected' : '' }}>Agreement Reached</option>
                                <option value="onboarded" {{ request('status') == 'onboarded' ? 'selected' : '' }}>Onboarded</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" name="search" id="search" class="form-control"
                                   placeholder="Search recruiter or talent name..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('talent_admin.manage_requests') }}" class="btn btn-secondary">
                                    <i class="fas fa-refresh"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Talent Requests</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Recruiter</th>
                            <th>Talent</th>
                            <th>Status</th>
                            <th>Requested Date</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                        <tr>
                            <td>{{ $request->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($request->recruiter->user->avatar)
                                        <img class="rounded-circle mr-2" src="{{ asset('storage/' . $request->recruiter->user->avatar) }}"
                                             alt="{{ $request->recruiter->user->name }}" style="width: 32px; height: 32px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-info mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="fas fa-building text-white" style="font-size: 12px;"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-weight-bold">{{ $request->recruiter->user->name }}</div>
                                        <small class="text-muted">{{ $request->recruiter->user->pekerjaan }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($request->talent->user->avatar)
                                        <img class="rounded-circle mr-2" src="{{ asset('storage/' . $request->talent->user->avatar) }}"
                                             alt="{{ $request->talent->user->name }}" style="width: 32px; height: 32px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="fas fa-user text-white" style="font-size: 12px;"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-weight-bold">{{ $request->talent->user->name }}</div>
                                        <small class="text-muted">{{ $request->talent->user->pekerjaan }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'meeting_arranged' => 'info',
                                        'agreement_reached' => 'primary',
                                        'onboarded' => 'success',
                                        'rejected' => 'danger',
                                        'completed' => 'success'
                                    ];
                                @endphp
                                <span class="badge badge-{{ $statusColors[$request->status] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ $request->updated_at->format('M d, Y H:i') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('talent_admin.show_request', $request) }}"
                                       class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($request->status == 'pending')
                                        <button type="button" class="btn btn-sm btn-success"
                                                onclick="updateStatus({{ $request->id }}, 'approved')" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger"
                                                onclick="updateStatus({{ $request->id }}, 'rejected')" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @elseif($request->status == 'approved')
                                        <button type="button" class="btn btn-sm btn-warning"
                                                onclick="updateStatus({{ $request->id }}, 'meeting_arranged')" title="Arrange Meeting">
                                            <i class="fas fa-calendar"></i>
                                        </button>
                                    @elseif($request->status == 'meeting_arranged')
                                        <button type="button" class="btn btn-sm btn-primary"
                                                onclick="updateStatus({{ $request->id }}, 'agreement_reached')" title="Mark Agreement Reached">
                                            <i class="fas fa-handshake"></i>
                                        </button>
                                    @elseif($request->status == 'agreement_reached')
                                        <button type="button" class="btn btn-sm btn-success"
                                                onclick="updateStatus({{ $request->id }}, 'onboarded')" title="Mark Onboarded">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No talent requests found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $requests->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Update Request Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <input type="hidden" id="requestId" name="request_id">
                    <input type="hidden" id="newStatus" name="status">

                    <div class="form-group">
                        <label for="admin_notes">Admin Notes (Optional)</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"
                                  placeholder="Add any notes for this status update..."></textarea>
                    </div>

                    <div class="alert alert-info">
                        <strong>Action:</strong> <span id="statusAction"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirmButton">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function updateStatus(requestId, status) {
    const statusActions = {
        'approved': 'Approve this request',
        'rejected': 'Reject this request',
        'meeting_arranged': 'Mark as meeting arranged'
    };

    const statusColors = {
        'approved': 'btn-success',
        'rejected': 'btn-danger',
        'meeting_arranged': 'btn-warning'
    };

    $('#requestId').val(requestId);
    $('#newStatus').val(status);
    $('#statusAction').text(statusActions[status]);

    // Update button color
    $('#confirmButton').removeClass('btn-success btn-danger btn-warning btn-primary');
    $('#confirmButton').addClass(statusColors[status] || 'btn-primary');

    $('#statusModal').modal('show');
}

$('#statusForm').on('submit', function(e) {
    e.preventDefault();

    const requestId = $('#requestId').val();
    const status = $('#newStatus').val();
    const adminNotes = $('#admin_notes').val();

    $.ajax({
        url: `/talent-admin/request/${requestId}/status`,
        method: 'PATCH',
        data: {
            status: status,
            admin_notes: adminNotes,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#statusModal').modal('hide');
                location.reload(); // Refresh page to show updated status
            }
        },
        error: function(xhr) {
            alert('Error updating status. Please try again.');
        }
    });
});
</script>
@endsection
