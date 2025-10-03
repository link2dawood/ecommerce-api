@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Contact Us</h2>
    <form method="POST" action="{{ route('contact.store') }}">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Subject</label>
            <input type="text" name="subject" class="form-control">
        </div>
        <div class="mb-3">
            <label>Message</label>
            <textarea name="message" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send</button>
    </form>
</div>
@endsection
