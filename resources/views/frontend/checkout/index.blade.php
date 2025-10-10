@extends('frontend.layouts.app')
@section('title', 'Checkout')
@section('content')
<div class="container py-5">
    <h2 class="mb-4">Checkout</h2>
    
    <div class="row">
        <!-- Order Summary -->
        <div class="col-md-4 order-md-2 mb-4">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Your cart</span>
                <span class="badge bg-secondary rounded-pill">{{ $cartItems->count() }}</span>
            </h4>
            <ul class="list-group mb-3">
                @foreach($cartItems as $item)
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0">{{ $item->product->name }}</h6>
                        <small class="text-muted">Quantity: {{ $item->quantity }}</small>
                    </div>
                    <span class="text-muted">${{ number_format($item->price * $item->quantity, 2) }}</span>
                </li>
                @endforeach
                <li class="list-group-item d-flex justify-content-between">
                    <strong>Total (USD)</strong>
                    <strong>${{ number_format($total, 2) }}</strong>
                </li>
            </ul>
        </div>

        <!-- Checkout Form -->
        <div class="col-md-8 order-md-1">
            <h4 class="mb-3">Shipping & Payment</h4>
            
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('checkout.process') }}">
                @csrf
                
                <div class="mb-3">
                    <label for="address" class="form-label">Shipping Address</label>
                    <textarea 
                        name="address" 
                        id="address" 
                        class="form-control @error('address') is-invalid @enderror" 
                        rows="3" 
                        required
                        placeholder="Enter your full shipping address">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select 
                        name="payment_method" 
                        id="payment_method" 
                        class="form-select @error('payment_method') is-invalid @enderror" 
                        required>
                        <option value="">Select payment method</option>
                        <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                    </select>
                    @error('payment_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">Place Order</button>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">Back to Cart</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection