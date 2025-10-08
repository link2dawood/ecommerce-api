@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Create New Order</h2>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.orders.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-md-8">
                <!-- Order Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="user_id" class="form-label">Customer <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                    <option value="">Select Customer</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <input type="text" name="payment_method" id="payment_method" 
                                       class="form-control @error('payment_method') is-invalid @enderror"
                                       value="{{ old('payment_method') }}"
                                       placeholder="e.g., Credit Card, PayPal">
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Order Status</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ old('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="refunded" {{ old('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="payment_status" class="form-label">Payment Status</label>
                                <select name="payment_status" id="payment_status" class="form-select @error('payment_status') is-invalid @enderror">
                                    <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="failed" {{ old('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="refunded" {{ old('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                                @error('payment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Order Notes</label>
                            <textarea name="notes" id="notes" rows="4" 
                                      class="form-control @error('notes') is-invalid @enderror"
                                      placeholder="Add any special instructions or notes...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Addresses -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Addresses</h5>
                    </div>
                    <div class="card-body">
                        <h6>Shipping Address</h6>
                        <div class="row mb-3">
                            <div class="col-md-12 mb-3">
                                <label for="shipping_name" class="form-label">Full Name</label>
                                <input type="text" id="shipping_name" class="form-control" 
                                       placeholder="Full Name">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="shipping_address" class="form-label">Address</label>
                                <input type="text" id="shipping_address" class="form-control" 
                                       placeholder="Street Address">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shipping_city" class="form-label">City</label>
                                <input type="text" id="shipping_city" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shipping_state" class="form-label">State</label>
                                <input type="text" id="shipping_state" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shipping_zip" class="form-label">ZIP Code</label>
                                <input type="text" id="shipping_zip" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shipping_country" class="form-label">Country</label>
                                <input type="text" id="shipping_country" class="form-control">
                            </div>
                        </div>

                        <hr>

                        <h6>Billing Address</h6>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="same_as_shipping">
                            <label class="form-check-label" for="same_as_shipping">
                                Same as shipping address
                            </label>
                        </div>
                        <div class="row" id="billing_fields">
                            <div class="col-md-12 mb-3">
                                <label for="billing_name" class="form-label">Full Name</label>
                                <input type="text" id="billing_name" class="form-control" 
                                       placeholder="Full Name">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="billing_address" class="form-label">Address</label>
                                <input type="text" id="billing_address" class="form-control" 
                                       placeholder="Street Address">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="billing_city" class="form-label">City</label>
                                <input type="text" id="billing_city" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="billing_state" class="form-label">State</label>
                                <input type="text" id="billing_state" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="billing_zip" class="form-label">ZIP Code</label>
                                <input type="text" id="billing_zip" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="billing_country" class="form-label">Country</label>
                                <input type="text" id="billing_country" class="form-control">
                            </div>
                        </div>

                        <input type="hidden" name="shipping_address" id="shipping_address_json">
                        <input type="hidden" name="billing_address" id="billing_address_json">
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Order Summary -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="subtotal" class="form-label">Subtotal <span class="text-danger">*</span></label>
                            <input type="number" name="subtotal" id="subtotal" step="0.01" 
                                   class="form-control @error('subtotal') is-invalid @enderror"
                                   value="{{ old('subtotal', 0) }}" required>
                            @error('subtotal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tax_amount" class="form-label">Tax Amount</label>
                            <input type="number" name="tax_amount" id="tax_amount" step="0.01" 
                                   class="form-control @error('tax_amount') is-invalid @enderror"
                                   value="{{ old('tax_amount', 0) }}">
                            @error('tax_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="shipping_amount" class="form-label">Shipping Amount</label>
                            <input type="number" name="shipping_amount" id="shipping_amount" step="0.01" 
                                   class="form-control @error('shipping_amount') is-invalid @enderror"
                                   value="{{ old('shipping_amount', 0) }}">
                            @error('shipping_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="discount_amount" class="form-label">Discount Amount</label>
                            <input type="number" name="discount_amount" id="discount_amount" step="0.01" 
                                   class="form-control @error('discount_amount') is-invalid @enderror"
                                   value="{{ old('discount_amount', 0) }}">
                            @error('discount_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="currency" class="form-label">Currency</label>
                            <select name="currency" id="currency" class="form-select @error('currency') is-invalid @enderror">
                                <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                                <option value="PKR" {{ old('currency') == 'PKR' ? 'selected' : '' }}>PKR</option>
                            </select>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label for="total_amount" class="form-label">Total Amount <span class="text-danger">*</span></label>
                            <input type="number" name="total_amount" id="total_amount" step="0.01" 
                                   class="form-control @error('total_amount') is-invalid @enderror"
                                   value="{{ old('total_amount', 0) }}" required readonly>
                            @error('total_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Create Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calculate total
    function calculateTotal() {
        const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
        const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
        const shipping = parseFloat(document.getElementById('shipping_amount').value) || 0;
        const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
        
        const total = subtotal + tax + shipping - discount;
        document.getElementById('total_amount').value = total.toFixed(2);
    }

    // Add event listeners
    ['subtotal', 'tax_amount', 'shipping_amount', 'discount_amount'].forEach(id => {
        document.getElementById(id).addEventListener('input', calculateTotal);
    });

    // Same as shipping checkbox
    document.getElementById('same_as_shipping').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('billing_name').value = document.getElementById('shipping_name').value;
            document.getElementById('billing_address').value = document.getElementById('shipping_address').value;
            document.getElementById('billing_city').value = document.getElementById('shipping_city').value;
            document.getElementById('billing_state').value = document.getElementById('shipping_state').value;
            document.getElementById('billing_zip').value = document.getElementById('shipping_zip').value;
            document.getElementById('billing_country').value = document.getElementById('shipping_country').value;
            document.getElementById('billing_fields').style.display = 'none';
        } else {
            document.getElementById('billing_fields').style.display = 'block';
        }
    });

    // Handle form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        // Prepare shipping address JSON
        const shippingAddress = {
            name: document.getElementById('shipping_name').value,
            address: document.getElementById('shipping_address').value,
            city: document.getElementById('shipping_city').value,
            state: document.getElementById('shipping_state').value,
            zip: document.getElementById('shipping_zip').value,
            country: document.getElementById('shipping_country').value
        };
        document.getElementById('shipping_address_json').value = JSON.stringify(shippingAddress);

        // Prepare billing address JSON
        const billingAddress = {
            name: document.getElementById('billing_name').value,
            address: document.getElementById('billing_address').value,
            city: document.getElementById('billing_city').value,
            state: document.getElementById('billing_state').value,
            zip: document.getElementById('billing_zip').value,
            country: document.getElementById('billing_country').value
        };
        document.getElementById('billing_address_json').value = JSON.stringify(billingAddress);
    });

    // Initial calculation
    calculateTotal();
});
</script>
@endpush
@endsection