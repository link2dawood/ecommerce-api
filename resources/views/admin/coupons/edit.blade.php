@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Coupon</h1>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Coupons
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="code" class="form-label">Coupon Code <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('code') is-invalid @enderror" 
                                   id="code" 
                                   name="code" 
                                   value="{{ old('code', $coupon->code) }}"
                                   style="text-transform: uppercase;"
                                   required>
                            <small class="text-muted">Code will be automatically converted to uppercase</small>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Discount Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" 
                                        name="type" 
                                        required>
                                    <option value="">Select Type</option>
                                    <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>
                                        Percentage (%)
                                    </option>
                                    <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>
                                        Fixed Amount (₨)
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="value" class="form-label">Discount Value <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('value') is-invalid @enderror" 
                                       id="value" 
                                       name="value" 
                                       value="{{ old('value', $coupon->value) }}"
                                       step="0.01"
                                       min="0"
                                       required>
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="minimum_amount" class="form-label">Minimum Purchase Amount</label>
                                <input type="number" 
                                       class="form-control @error('minimum_amount') is-invalid @enderror" 
                                       id="minimum_amount" 
                                       name="minimum_amount" 
                                       value="{{ old('minimum_amount', $coupon->minimum_amount) }}"
                                       step="0.01"
                                       min="0">
                                <small class="text-muted">Leave empty for no minimum</small>
                                @error('minimum_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="usage_limit" class="form-label">Usage Limit</label>
                                <input type="number" 
                                       class="form-control @error('usage_limit') is-invalid @enderror" 
                                       id="usage_limit" 
                                       name="usage_limit" 
                                       value="{{ old('usage_limit', $coupon->usage_limit) }}"
                                       min="1">
                                <small class="text-muted">Leave empty for unlimited uses</small>
                                @error('usage_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="expires_at" class="form-label">Expiration Date</label>
                            <input type="datetime-local" 
                                   class="form-control @error('expires_at') is-invalid @enderror" 
                                   id="expires_at" 
                                   name="expires_at" 
                                   value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}">
                            <small class="text-muted">Leave empty for no expiration</small>
                            @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active (Coupon can be used)
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Coupon
                            </button>
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Usage Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Times Used:</strong> {{ $coupon->used_count ?? 0 }}
                    </div>
                    <div class="mb-3">
                        <strong>Usage Limit:</strong> {{ $coupon->usage_limit ?? 'Unlimited' }}
                    </div>
                    <div class="mb-3">
                        <strong>Created:</strong> {{ $coupon->created_at->format('M d, Y') }}
                    </div>
                    <div class="mb-3">
                        <strong>Last Updated:</strong> {{ $coupon->updated_at->format('M d, Y') }}
                    </div>
                    @if($coupon->expires_at)
                        <div class="mb-3">
                            <strong>Status:</strong>
                            @if($coupon->expires_at->isPast())
                                <span class="badge bg-danger">Expired</span>
                            @else
                                <span class="badge bg-success">Valid</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Danger Zone</h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted">Once you delete a coupon, there is no going back.</p>
                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this coupon? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i> Delete Coupon
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('code').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});
</script>
@endsection