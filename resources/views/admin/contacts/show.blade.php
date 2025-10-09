@extends('admin.layouts.app')

@section('title', 'View Contact Message')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Contact Message Details</h2>
        <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Messages
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ $contact->subject }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6><strong>From:</strong></h6>
                            <p>{{ $contact->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>Email:</strong></h6>
                            <p>
                                <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                            </p>
                        </div>
                    </div>

                    @if($contact->user_id)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6><strong>User Account:</strong></h6>
                            <p>
                                @if($contact->user)
                                    {{ $contact->user->name }} (ID: {{ $contact->user_id }})
                                @else
                                    User ID: {{ $contact->user_id }}
                                @endif
                            </p>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6><strong>Date Received:</strong></h6>
                            <p>{{ $contact->created_at->format('F d, Y h:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>Status:</strong></h6>
                            <p>
                                @if($contact->is_read)
                                    <span class="badge bg-success">Read</span>
                                @else
                                    <span class="badge bg-primary">Unread</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h6><strong>Message:</strong></h6>
                        <div class="p-3 bg-light rounded">
                            <p style="white-space: pre-wrap;">{{ $contact->message }}</p>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="mailto:{{ $contact->email }}?subject=Re: {{ $contact->subject }}" class="btn btn-primary">
                            <i class="fas fa-reply"></i> Reply via Email
                        </a>
                        
                        @if(!$contact->is_read)
                            <button class="btn btn-info mark-as-read" data-id="{{ $contact->id }}">
                                <i class="fas fa-check"></i> Mark as Read
                            </button>
                        @else
                            <button class="btn btn-secondary mark-as-unread" data-id="{{ $contact->id }}">
                                <i class="fas fa-undo"></i> Mark as Unread
                            </button>
                        @endif

                        <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this message?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Mark as read
document.querySelectorAll('.mark-as-read').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        updateReadStatus(id, 'read');
    });
});

// Mark as unread
document.querySelectorAll('.mark-as-unread').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        updateReadStatus(id, 'unread');
    });
});

function updateReadStatus(id, action) {
    const url = action === 'read' 
        ? `/admin/contacts/${id}/mark-as-read`
        : `/admin/contacts/${id}/mark-as-unread`;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endpush