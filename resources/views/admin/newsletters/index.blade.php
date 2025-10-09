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
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $newsletter->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
$(document).ready(function() {
    // Select all checkboxes
    $('#selectAll').on('change', function() {
        $('.select-item').prop('checked', $(this).prop('checked'));
        toggleBulkDelete();
    });

    // Individual checkbox change
    $('.select-item').on('change', function() {
        toggleBulkDelete();
        updateSelectAll();
    });

    // Toggle bulk delete button
    function toggleBulkDelete() {
        const checkedCount = $('.select-item:checked').length;
        if (checkedCount > 0) {
            $('#bulkDeleteBtn').show();
        } else {
            $('#bulkDeleteBtn').hide();
        }
    }

    // Update select all checkbox
    function updateSelectAll() {
        const totalItems = $('.select-item').length;
        const checkedItems = $('.select-item:checked').length;
        $('#selectAll').prop('checked', totalItems === checkedItems && totalItems > 0);
    }

    // Single delete
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this subscriber?')) {
            $.ajax({
                url: `/admin/newsletters/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $(`tr[data-id="${id}"]`).fadeOut(300, function() {
                            $(this).remove();
                            checkEmptyTable();
                        });
                        showAlert('success', response.message);
                    }
                },
                error: function() {
                    showAlert('danger', 'Error deleting subscriber');
                }
            });
        }
    });

    // Bulk delete
    $('#bulkDeleteBtn').on('click', function() {
        const selectedIds = $('.select-item:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            showAlert('warning', 'Please select subscribers to delete');
            return;
        }

        if (confirm(`Are you sure you want to delete ${selectedIds.length} subscriber(s)?`)) {
            $.ajax({
                url: '{{ route("admin.newsletters.bulkDelete") }}',
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    ids: selectedIds
                },
                success: function(response) {
                    if (response.success) {
                        selectedIds.forEach(id => {
                            $(`tr[data-id="${id}"]`).fadeOut(300, function() {
                                $(this).remove();
                                checkEmptyTable();
                            });
                        });
                        $('#selectAll').prop('checked', false);
                        $('#bulkDeleteBtn').hide();
                        showAlert('success', response.message);
                    }
                },
                error: function() {
                    showAlert('danger', 'Error deleting subscribers');
                }
            });
        }
    });

    // Check if table is empty
    function checkEmptyTable() {
        if ($('tbody tr').length === 0) {
            $('tbody').html('<tr><td colspan="5" class="text-center">No subscribers yet.</td></tr>');
        }
    }

    // Show alert
    function showAlert(type, message) {
        const alert = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('.container-fluid').prepend(alert);
        
        setTimeout(() => {
            $('.alert').fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
});
</script>
@endpush
@endsection