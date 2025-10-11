@extends('frontend.layouts.app')
@section('title', 'Contact Us')

@section('content')
<!-- Save this file as: resources/views/frontend/contact/index.blade.php -->
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h2 class="mb-4 text-center">Contact Us</h2>
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('contact.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label for="name">Your Name *</label>
                            <input type="text" 
                                   name="name" 
                                   id="name"
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">Your Email *</label>
                            <input type="email" 
                                   name="email" 
                                   id="email"
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="subject">Subject</label>
                            <input type="text" 
                                   name="subject" 
                                   id="subject"
                                   class="form-control @error('subject') is-invalid @enderror"
                                   value="{{ old('subject') }}">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="message">Message *</label>
                            <textarea name="message" 
                                      id="message"
                                      rows="5" 
                                      class="form-control @error('message') is-invalid @enderror" 
                                      required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-paper-plane me-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="row mt-5">
                <div class="col-md-4 text-center mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                            <h5>Address</h5>
                            <p class="text-muted">123 Main Street<br>City, State 12345</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-phone fa-3x text-primary mb-3"></i>
                            <h5>Phone</h5>
                            <p class="text-muted">+1 (234) 567-8900<br>Mon-Fri 9am-6pm</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                            <h5>Email</h5>
                            <p class="text-muted">info@example.com<br>support@example.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-primary {
    background-color: #D19C97;
    border-color: #D19C97;
}

.btn-primary:hover {
    background-color: #c18a84;
    border-color: #c18a84;
}

.text-primary {
    color: #D19C97 !important;
}

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
}
</style>
@endsection