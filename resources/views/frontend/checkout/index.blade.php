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
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('checkout.process') }}" id="checkout-form">
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

                <div class="mb-4">
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

                <!-- Stripe Card Element (Hidden by default, shown when Card is selected) -->
                <div id="card-payment-section" style="display: none;">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Card Information</h5>
                            
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="card-email" class="form-label">Email</label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="card-email" 
                                    value="{{ auth()->user()->email }}"
                                    readonly>
                            </div>

                            <!-- Card Number -->
                            <div class="mb-3">
                                <label for="card-number" class="form-label">Card number</label>
                                <div class="input-group">
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="card-number" 
                                        placeholder="4242 4242 4242 4242"
                                        maxlength="19">
                                    <span class="input-group-text">
                                        <img src="https://img.icons8.com/color/24/000000/visa.png" alt="Visa" style="height: 20px;">
                                    </span>
                                </div>
                                <small class="text-muted">Test card: 4242 4242 4242 4242</small>
                            </div>

                            <div class="row">
                                <!-- Expiry Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="card-expiry" class="form-label">Expiry Date</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="card-expiry" 
                                        placeholder="MM / YY"
                                        maxlength="7">
                                </div>

                                <!-- CVC -->
                                <div class="col-md-6 mb-3">
                                    <label for="card-cvc" class="form-label">CVC</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="card-cvc" 
                                        placeholder="123"
                                        maxlength="4">
                                </div>
                            </div>

                            <!-- Cardholder Name -->
                            <div class="mb-3">
                                <label for="card-name" class="form-label">Name on card</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="card-name" 
                                    placeholder="Zhang San"
                                    value="{{ auth()->user()->name }}">
                            </div>

                            <!-- Country -->
                            <div class="mb-3">
                                <label for="card-country" class="form-label">Country or region</label>
                                <select class="form-select" id="card-country">
                                    <option value="US" selected>United States</option>
                                    <option value="GB">United Kingdom</option>
                                    <option value="CA">Canada</option>
                                    <option value="AU">Australia</option>
                                    <option value="PK">Pakistan</option>
                                    <!-- Add more countries as needed -->
                                </select>
                            </div>

                            <!-- ZIP Code -->
                            <div class="mb-3">
                                <label for="card-zip" class="form-label">ZIP</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="card-zip" 
                                    placeholder="12345">
                            </div>

                            <div class="alert alert-info" role="alert">
                                <small>
                                    <strong>Test Mode:</strong> Use card number 4242 4242 4242 4242 with any future expiry date and any 3-digit CVC.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg" id="submit-btn">
                        <span id="btn-text">Place Order</span>
                        <span id="btn-spinner" class="spinner-border spinner-border-sm ms-2" style="display: none;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </button>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">Back to Cart</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    #card-payment-section .form-control:focus,
    #card-payment-section .form-select:focus {
        border-color: #5469d4;
        box-shadow: 0 0 0 0.2rem rgba(84, 105, 212, 0.25);
    }
    
    .input-group-text img {
        height: 20px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.getElementById('payment_method');
    const cardPaymentSection = document.getElementById('card-payment-section');
    const checkoutForm = document.getElementById('checkout-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnSpinner = document.getElementById('btn-spinner');

    // Toggle card payment section
    paymentMethodSelect.addEventListener('change', function() {
        if (this.value === 'card') {
            cardPaymentSection.style.display = 'block';
            btnText.textContent = 'Pay ${{ number_format($total, 2) }}';
        } else {
            cardPaymentSection.style.display = 'none';
            btnText.textContent = 'Place Order';
        }
    });

    // Format card number
    const cardNumberInput = document.getElementById('card-number');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });
    }

    // Format expiry date
    const cardExpiryInput = document.getElementById('card-expiry');
    if (cardExpiryInput) {
        cardExpiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/\//g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + ' / ' + value.slice(2, 4);
            }
            e.target.value = value;
        });
    }

    // Form submission with loading state
    checkoutForm.addEventListener('submit', function() {
        submitBtn.disabled = true;
        btnText.style.display = 'none';
        btnSpinner.style.display = 'inline-block';
    });
});
</script>
@endpush
@endsection