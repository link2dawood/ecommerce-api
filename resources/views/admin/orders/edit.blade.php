@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Edit Order - {{ $order->order_number }}</h2>
                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Order
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

    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
        @csrf
        @method('PUT')
        
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
                                <label class="form-label">Order Number</label>
                                <input type="text" class="form-control" value="{{ $order->order_number }}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Customer</label>
                                <input type="text" class="form-control" 
                                       value="{{ $order->user->name }} ({{ $order->user->email }})" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <input type="text" name="payment_method" id="payment_method" 
                                       class="form-control @error('payment_method') is-invalid @enderror"
                                       value="{{ old('payment_method', $order->payment_method) }}"
                                       placeholder="e.g., Credit Card, PayPal">
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Order Date</label>
                                <input type="text" class="form-control" 
                                       value="{{ $order->created_at->format('M d, Y h:i A') }}" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Order Status</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ old('status', $order->status) == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="refunded" {{ old('status', $order->status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="payment_status" class="form-label">Payment Status</label>
                                <select name="payment_status" id="payment_status" class="form-select @error('payment_status') is-invalid @enderror">
                                    <option value="pending" {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="failed" {{ old('payment_status', $order->payment_status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="refunded" {{ old('payment_status', $order->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
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
                                      placeholder="Add any special instructions or notes...">{{ old('notes', $order->notes) }}</textarea>
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
                        @php
                            $shipping = is_array($order->shipping_address) 
                                ? $order->shipping_address 
                                : json_decode($order->shipping_address, true) ?? [];
                            $billing = is_array($order->billing_address) 
                                ? $order->billing_address 
                                : json_decode($order->billing_address, true) ?? [];
                        @endphp

                        <h6>Shipping Address</h6>
                        <div class="row mb-3">
                            <div class="col-md-12 mb-3">
                                <label for="shipping_name" class="form-label">Full Name</label>
                                <input type="text" id="shipping_name" class="form-control" 
                                       value="{{ $shipping['name'] ?? '' }}" placeholder="Full Name">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="shipping_address" class="form-label">Address</label>
                                <input type="text" id="shipping_address" class="form-control" 
                                       value="{{ $shipping['address'] ?? '' }}" placeholder="Street Address">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shipping_city" class="form-label">City</label>
                                <input type="text" id="shipping_city" class="form-control" 
                                       value="{{ $shipping['city'] ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shipping_state" class="form-label">State</label>
                                <input type="text" id="shipping_state" class="form-control" 
                                       value="{{ $shipping['state'] ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shipping_zip" class="form-label">ZIP Code</label>
                                <input type="text" id="shipping_zip" class="form-control" 
                                       value="{{ $shipping['zip'] ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shipping_country" class="form-label">Country</label>
                                <input type="text" id="shipping_country" class="form-control" 
                                       value="{{ $shipping['country'] ?? '' }}">
                            </div>
                        </div>

                        <hr>

                        <h6>Billing Address</h6>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="billing_name" class="form-label">Full Name</label>
                                <input type="text" id="billing_name" class="form-control" 
                                       value="{{ $billing['name'] ?? '' }}" placeholder="Full Name">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="billing_address" class="form-label">Address</label>
                                <input type="text" id="billing_address" class="form-control" 
                                       value="{{ $billing['address'] ?? '' }}" placeholder="Street Address">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="billing_city" class="form-label">City</label>
                                <input type="text" id="billing_city" class="form-control" 
                                       value="{{ $billing['city'] ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="billing_state" class="form-label">State</label>
                                <input type="text" id="billing_state" class="form-control" 
                                       value="{{ $billing['state'] ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="billing_zip" class="form-label">ZIP Code</label>
                                <input type="text" id="billing_zip" class="form-control" 
                                       value="{{ $billing['zip'] ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="billing_country" class="form-label">Country</label>
                                <input type="text" id="billing_country" class="form-control" 
                                       value="{{ $billing['country'] ?? '' }}">
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
                                   value="{{ old('subtotal', $order->subtotal) }}" required>
                            @error('subtotal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tax_amount" class="form-label">Tax Amount</label>
                            <input type="number" name="tax_amount" id="tax_amount" step="0.01" 
                                   class="form-control @error('tax_amount') is-invalid @enderror"
                                   value="{{ old('tax_amount', $order->tax_amount) }}">
                            @error('tax_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="shipping_amount" class="form-label">Shipping Amount</label>
                            <input type="number" name="shipping_amount" id="shipping_amount" step="0.01" 
                                   class="form-control @error('shipping_amount') is-invalid @enderror"
                                   value="{{ old('shipping_amount', $order->shipping_amount) }}">
                            @error('shipping_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="discount_amount" class="form-label">Discount Amount</label>
                            <input type="number" name="discount_amount" id="discount_amount" step="0.01" 
                                   class="form-control @error('discount_amount') is-invalid @enderror"
                                   value="{{ old('discount_amount', $order->discount_amount) }}">
                            @error('discount_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label for="total_amount" class="form-label">Total Amount <span class="text-danger">*</span></label>
                            <input type="number" name="total_amount" id="total_amount" step="0.01" 
                                   class="form-control @error('total_amount') is-invalid @enderror"
                                   value="{{ old('total_amount', $order->total_amount) }}" required readonly>
                            @error('total_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Update Order
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