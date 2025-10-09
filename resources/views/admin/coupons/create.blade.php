@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Create New Coupon</h1>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Coupons
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.coupons.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="code" class="form-label">Coupon Code <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('code') is-invalid @enderror" 
                                   id="code" 
                                   name="code" 
                                   value="{{ old('code') }}"
                                   placeholder="e.g., SAVE20"
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
                                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>
                                        Percentage (%)
                                    </option>
                                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>
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
                                       value="{{ old('value') }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="e.g., 20"
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
                                       value="{{ old('minimum_amount') }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="e.g., 500">
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
                                       value="{{ old('usage_limit') }}"
                                       min="1"
                                       placeholder="e.g., 100">
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
                                   value="{{ old('expires_at') }}">
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
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active (Coupon can be used)
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Create Coupon
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
                    <h5 class="mb-0">Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="small">
                        <li class="mb-2">Use memorable and easy-to-type codes</li>
                        <li class="mb-2">Percentage discounts are great for all products</li>
                        <li class="mb-2">Fixed amount works best for minimum purchase requirements</li>
                        <li class="mb-2">Set usage limits to control promotional costs</li>
                        <li class="mb-2">Inactive coupons won't be visible to customers</li>
                    </ul>
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