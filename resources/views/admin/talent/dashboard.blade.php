@extends('layout.template.mainTemplate')

@section('title', 'Talent Dashboard')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Welcome, Talent</h6>
                </div>
                <div class="card-body">
                    <h4>Hello, {{ $user->name }}!</h4>
                    <p>Welcome to your Talent Dashboard.</p>
                    <p class="text-muted">Manage your talent profile and view opportunities.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
