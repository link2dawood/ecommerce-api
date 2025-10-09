@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Coupons Management</h1>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create New Coupon
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Min. Amount</th>
                            <th>Usage</th>
                            <th>Expires At</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                            <tr>
                                <td>
                                    <strong>{{ $coupon->code }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ ucfirst($coupon->type) }}
                                    </span>
                                </td>
                                <td>
                                    @if($coupon->type === 'percentage')
                                        {{ $coupon->value }}%
                                    @else
                                        ₨{{ number_format($coupon->value, 2) }}
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->minimum_amount)
                                        ₨{{ number_format($coupon->minimum_amount, 2) }}
                                    @else
                                        <span class="text-muted">No minimum</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $coupon->used_count ?? 0 }}
                                    @if($coupon->usage_limit)
                                        / {{ $coupon->usage_limit }}
                                    @else
                                        / <span class="text-muted">Unlimited</span>
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->expires_at)
                                        {{ $coupon->expires_at->format('M d, Y') }}
                                        @if($coupon->expires_at->isPast())
                                            <span class="badge bg-danger">Expired</span>
                                        @endif
                                    @else
                                        <span class="text-muted">No expiry</span>
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this coupon?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <p class="text-muted mb-0">No coupons found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $coupons->links() }}
            </div>
        </div>
    </div>
</div>
@endsection