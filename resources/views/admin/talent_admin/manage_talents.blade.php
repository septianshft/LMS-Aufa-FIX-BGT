@extends('layout.template.mainTemplate')

@section('title', 'Manage Talents')
@section('container')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Talents</h1>
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
            <h6 class="m-0 font-weight-bold text-primary">Talents List</h6>
        </div>
        <div class="card-body">
            @if($talents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Talent</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($talents as $talent)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($talent->user->avatar)
                                                <img class="rounded-circle mr-3" src="{{ asset('storage/' . $talent->user->avatar) }}"
                                                     alt="{{ $talent->user->name }}" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-secondary mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-weight-bold">{{ $talent->user->name }}</div>
                                                <div class="text-muted small">{{ $talent->user->pekerjaan }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $talent->user->email }}</td>
                                    <td>
                                        <span class="badge badge-{{ $talent->is_active ? 'success' : 'secondary' }}">
                                            {{ $talent->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $talent->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <form action="{{ route('talent_admin.toggle_talent_status', $talent) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-sm btn-{{ $talent->is_active ? 'warning' : 'success' }}"
                                                    onclick="return confirm('Are you sure you want to {{ $talent->is_active ? 'deactivate' : 'activate' }} this talent?')">
                                                {{ $talent->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $talents->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-tie fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No talents found</h5>
                    <p class="text-muted">No talents have registered yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
