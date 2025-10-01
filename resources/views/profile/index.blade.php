@extends('layouts.header')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body text-center">

            <!-- Avatar -->
            <div class="mb-3">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded-circle" width="120" height="120">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=120" class="rounded-circle" alt="Avatar">
                @endif
            </div>

            <!-- User Info -->
            <h3>{{ $user->name }}</h3>
            <p class="text-muted">{{ ucfirst($user->role) }}</p>
            <hr>

            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Phone:</strong> {{ $user->phone ?? 'Not provided' }}</p>
            <p><strong>Status:</strong>
                @if($user->is_active)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-danger">Inactive</span>
                @endif
            </p>
            <p><strong>Joined:</strong> {{ $user->created_at->format('d M Y') }}</p>

            <hr>

            <!-- Actions -->
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('profile.delete') }}" class="btn btn-warning">Delete Account</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
