@extends('layouts.header')

@section('content')
<div class="container py-5">
    <h2>Feedback</h2>
    <form method="POST" action="#">
        @csrf
        <div class="mb-3">
            <label class="form-label">Your Feedback</label>
            <textarea class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Feedback</button>
    </form>
</div>
@endsection
