@extends('frontend.layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0"><i class="fas fa-user-circle"></i> My Profile</h3>
                </div>
                <div class="card-body p-4">
                    
                    {{-- Profile Picture Section --}}
                    <div class="text-center mb-4">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" 
                                 alt="{{ $user->name }}" 
                                 class="rounded-circle mb-3 shadow" 
                                 style="width: 180px; height: 180px; object-fit: cover; border: 5px solid #007bff;">
                        @else
                            <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mb-3 shadow" 
                                 style="width: 180px; height: 180px; border: 5px solid #6c757d;">
                                <i class="fas fa-user fa-5x text-white"></i>
                            </div>
                        @endif
                        <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                        <p class="text-muted">
                            <i class="fas fa-shield-alt"></i> 
                            <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'success' }}">
                                {{ ucfirst($user->role ?? 'Customer') }}
                            </span>
                        </p>
                    </div>

                    <hr class="my-4">

                    {{-- Profile Information --}}
                    <div class="row g-4">
                        
                        {{-- Email --}}
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <label class="text-muted small mb-1">
                                    <i class="fas fa-envelope"></i> Email Address
                                </label>
                                <p class="fw-semibold mb-0">{{ $user->email }}</p>
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <label class="text-muted small mb-1">
                                    <i class="fas fa-phone"></i> Phone Number
                                </label>
                                <p class="fw-semibold mb-0">{{ $user->phone ?? 'Not provided' }}</p>
                            </div>
                        </div>

                        {{-- Member Since --}}
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <label class="text-muted small mb-1">
                                    <i class="fas fa-calendar-alt"></i> Member Since
                                </label>
                                <p class="fw-semibold mb-0">{{ $user->created_at->format('F d, Y') }}</p>
                            </div>
                        </div>

                        {{-- Last Updated --}}
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <label class="text-muted small mb-1">
                                    <i class="fas fa-clock"></i> Last Updated
                                </label>
                                <p class="fw-semibold mb-0">{{ $user->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>

                    </div>

                    <hr class="my-4">

                    {{-- Action Buttons --}}
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary px-4">
                            <i class="fas fa-edit"></i> Edit Profile
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-primary px-4">
                            <i class="fas fa-shopping-bag"></i> My Orders
                        </a>
                        <a href="{{ route('wishlist.index') }}" class="btn btn-outline-danger px-4">
                            <i class="fas fa-heart"></i> Wishlist
                        </a>
                    </div>

                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="card shadow border-danger mt-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Danger Zone</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">Once you delete your account, there is no going back. Please be certain.</p>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="fas fa-trash"></i> Delete Account
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Delete Account Modal --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Delete Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete your account? This action cannot be undone.</p>
                <p class="text-danger fw-bold">All your data, orders, and wishlist will be permanently deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('profile.delete') }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Yes, Delete My Account</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection