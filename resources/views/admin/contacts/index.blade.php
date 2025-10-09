@extends('admin.layouts.app')

@section('title', 'Contact Messages')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Contact Messages</h2>
        <button class="btn btn-danger" id="bulkDeleteBtn" style="display: none;">
            <i class="fas fa-trash"></i> Delete Selected
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.contacts.index') }}" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, email, subject or message..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contacts as $contact)
                            <tr class="{{ !$contact->is_read ? 'table-primary' : '' }}">
                                <td>
                                    <input type="checkbox" class="contact-checkbox" value="{{ $contact->id }}">
                                </td>
                                <td>
                                    {{ $contact->name }}
                                    @if(!$contact->is_read)
                                        <span class="badge bg-primary">New</span>
                                    @endif
                                </td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ Str::limit($contact->subject, 30) }}</td>
                                <td>{{ Str::limit($contact->message, 50) }}</td>
                                <td>{{ $contact->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.contacts.show', $contact->id) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No contact messages found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $contacts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Select all checkboxes
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.contact-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    toggleBulkDeleteBtn();
});

// Show/hide bulk delete button
document.querySelectorAll('.contact-checkbox').forEach(cb => {
    cb.addEventListener('change', toggleBulkDeleteBtn);
});

function toggleBulkDeleteBtn() {
    const checkedBoxes = document.querySelectorAll('.contact-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    bulkDeleteBtn.style.display = checkedBoxes.length > 0 ? 'block' : 'none';
}

// Bulk delete
document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
    const checkedBoxes = document.querySelectorAll('.contact-checkbox:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (!confirm(`Are you sure you want to delete ${ids.length} message(s)?`)) {
        return;
    }

    fetch('{{ route("admin.contacts.bulk-delete") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ids: ids })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
});
</script>
@endpush