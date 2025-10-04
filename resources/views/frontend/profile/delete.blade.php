@extends('layouts.header')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body text-center">
            <h3>Delete Account</h3>
            <p class="text-danger">⚠️ This action cannot be undone. Are you sure you want to delete your account?</p>

            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')

                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('profile') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-danger">Yes, Delete My Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
