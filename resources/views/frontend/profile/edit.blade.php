@extends('frontend.layouts.app')
@section('title', 'Edit Profile')

@section('content')
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-edit"></i> Edit Profile</h4>
                </div>
                <div class="card-body p-4">
                    
                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Error Messages --}}
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Current Profile Picture --}}
                        <div class="mb-4 text-center">
                            <label class="form-label d-block fw-bold">Current Profile Picture</label>
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" 
                                     alt="Profile Picture" 
                                     class="rounded-circle mb-3" 
                                     style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #007bff;">
                            @else
                                <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mb-3" 
                                     style="width: 150px; height: 150px; border: 3px solid #6c757d;">
                                    <i class="fas fa-user fa-4x text-white"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Profile Picture Upload --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-camera"></i> Change Profile Picture
                            </label>
                            <input type="file" 
                                   name="avatar" 
                                   class="form-control @error('avatar') is-invalid @enderror" 
                                   accept="image/*"
                                   id="avatarInput">
                            <small class="text-muted">Allowed formats: JPG, PNG, GIF (Max: 2MB)</small>
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            {{-- Image Preview --}}
                            <div id="imagePreview" class="mt-3 text-center" style="display: none;">
                                <img id="preview" src="" alt="Preview" class="rounded" style="max-width: 200px; max-height: 200px;">
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Full Name --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user"></i> Full Name
                            </label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-envelope"></i> Email Address
                            </label>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-phone"></i> Phone Number
                            </label>
                            <input type="text" 
                                   name="phone" 
                                   value="{{ old('phone', $user->phone) }}" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   placeholder="+92 300 1234567">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        {{-- Password Section --}}
                        <h5 class="mb-3"><i class="fas fa-lock"></i> Change Password (Optional)</h5>
                        <p class="text-muted small">Leave blank if you don't want to change your password</p>

                        {{-- New Password --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">New Password</label>
                            <input type="password" 
                                   name="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="Enter new password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Confirm New Password</label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   class="form-control" 
                                   placeholder="Confirm new password">
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex justify-content-between gap-2">
                            <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Image Preview Script
document.getElementById('avatarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        document.getElementById('imagePreview').style.display = 'none';
    }
});
</script>
@endpush
@endsection