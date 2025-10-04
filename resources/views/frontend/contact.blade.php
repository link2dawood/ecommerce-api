@extends('frontend.layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <h2 class="mb-4 text-center">Contact Us</h2>

            <form id="contactForm" action="{{ route('contact.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="email">Your Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" name="subject" class="form-control">
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea name="message" rows="5" class="form-control" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Send Message</button>
            </form>

            <div id="contactResponse" class="mt-3"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelector('#contactForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = e.target;
    const responseDiv = document.getElementById('contactResponse');
    responseDiv.innerHTML = '';

    const formData = new FormData(form);

    try {
        const res = await fetch(form.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        });

        const data = await res.json();

        if (res.ok) {
            responseDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
            form.reset();
        } else {
            responseDiv.innerHTML = `<div class="alert alert-danger">${data.message || 'Something went wrong.'}</div>`;
        }
    } catch (error) {
        responseDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
    }
});
</script>
@endpush
