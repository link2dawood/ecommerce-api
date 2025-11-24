@extends('admin.layouts.app')
@section('title', 'Newsletter Subscribers')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Newsletter Subscribers</h1>
        <div>
            <button class="btn btn-danger" id="bulkDeleteBtn" style="display:none;">
                <i class="fas fa-trash"></i> Delete Selected
            </button>
            <form action="{{ route('admin.newsletters.export') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <p class="text-muted">Total Subscribers: <strong>{{ $newsletters->total() }}</strong></p>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Subscribed At</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($newsletters as $newsletter)
                            <tr data-id="{{ $newsletter->id }}">
                                <td>
                                    <input type="checkbox" class="select-item" value="{{ $newsletter->id }}">
                                </td>
                                <td>{{ $newsletter->id }}</td>
                                <td>{{ $newsletter->email }}</td>
                                <td>{{ $newsletter->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <form action="{{ route('admin.newsletters.destroy', $newsletter->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No subscribers yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $newsletters->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Select all checkbox
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectItems = document.querySelectorAll('.select-item');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            selectItems.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleBulkDelete();
        });
    }

    // Individual checkboxes
    selectItems.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            toggleBulkDelete();
            updateSelectAll();
        });
    });

    // Toggle bulk delete button
    function toggleBulkDelete() {
        const checkedCount = document.querySelectorAll('.select-item:checked').length;
        if (checkedCount > 0) {
            bulkDeleteBtn.style.display = 'block';
        } else {
            bulkDeleteBtn.style.display = 'none';
        }
    }

    // Update select all checkbox
    function updateSelectAll() {
        const totalItems = selectItems.length;
        const checkedItems = document.querySelectorAll('.select-item:checked').length;
        selectAllCheckbox.checked = totalItems === checkedItems && totalItems > 0;
    }

    // Delete form submission
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const url = this.getAttribute('action');
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.closest('tr').remove();
                    showAlert('success', data.message || 'Subscriber deleted successfully');
                    // Reload page after 1 second
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('danger', data.message || 'Error deleting subscriber');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Error deleting subscriber');
            });
        });
    });

    // Bulk delete
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.select-item:checked'))
                .map(checkbox => checkbox.value);

            if (selectedIds.length === 0) {
                showAlert('warning', 'Please select subscribers to delete');
                return;
            }

            if (confirm(`Are you sure you want to delete ${selectedIds.length} subscriber(s)?`)) {
                fetch('{{ route("admin.newsletters.bulkDelete") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ids: selectedIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message || 'Subscribers deleted successfully');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert('danger', data.message || 'Error deleting subscribers');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'Error deleting subscribers');
                });
            }
        });
    }

    // Show alert
    function showAlert(type, message) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.setAttribute('role', 'alert');
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.container-fluid').firstChild);
        
        setTimeout(() => {
            alert.remove();
        }, 4000);
    }
});
</script>
@endpush
@endsection