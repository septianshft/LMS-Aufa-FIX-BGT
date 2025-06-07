@extends('layout.template.mainTemplate')

@section('title', 'Talent Admin Dashboard')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Welcome, Talent Admin</h6>
                </div>
                <div class="card-body">
                    <h4>Hello, {{ $user->name }}!</h4>
                    <p>Welcome to your Talent Admin Dashboard.</p>
                    <p class="text-muted">From here you can manage the talent scouting system.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
