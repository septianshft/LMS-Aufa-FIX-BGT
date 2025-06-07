@extends('layout.template.mainTemplate')

@section('title', 'Manage Recruiters')
@section('container')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Recruiters</h1>
        <a href="{{ route('talent_admin.dashboard') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Dashboard
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- DataTales Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recruiters List</h6>
        </div>
        <div class="card-body">
            @if($recruiters->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Recruiter</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Joined Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recruiters as $recruiter)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                @if($recruiter->user->avatar)
                                                    <img class="rounded-circle" width="40" height="40"
                                                         src="{{ asset('storage/' . $recruiter->user->avatar) }}"
                                                         alt="{{ $recruiter->user->name }}">
                                                @else
                                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $recruiter->user->name }}</div>
                                                @if($recruiter->user->pekerjaan)
                                                    <div class="text-muted small">{{ $recruiter->user->pekerjaan }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $recruiter->user->email }}</td>
                                    <td>
                                        @if($recruiter->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $recruiter->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <form action="{{ route('talent_admin.toggle_recruiter_status', $recruiter) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-sm {{ $recruiter->is_active ? 'btn-warning' : 'btn-success' }}"
                                                    onclick="return confirm('Are you sure you want to {{ $recruiter->is_active ? 'deactivate' : 'activate' }} this recruiter?')">
                                                {{ $recruiter->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $recruiters->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No recruiters found</h5>
                    <p class="text-muted">No recruiters have registered yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
