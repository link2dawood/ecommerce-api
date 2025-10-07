@extends('frontend.layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body">
            <h3>Edit Profile</h3>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Profile Picture</label>
                    <input type="file" name="avatar" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">New Password (leave blank to keep old)</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('profile.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
