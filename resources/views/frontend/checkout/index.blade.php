@extends('frontend.layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-center">Checkout</h2>
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left Side - Payment Methods -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <!-- Coupon Section -->
                    <div class="mb-4">
                        <h5 class="mb-3">
                            <i class="fas fa-tag text-primary me-2"></i>Have a coupon?
                        </h5>
                        <p class="text-muted small">Enter your coupon code below to get a discount on your purchase.</p>
                        
                        @if($appliedCoupon)
                            <div class="alert alert-success d-flex justify-content-between align-items-center" role="alert">
                                <div>
                                    <strong>Coupon Applied:</strong> {{ $appliedCoupon }}
                                    <br>
                                    <small>You saved ${{ number_format($discountAmount, 2) }}</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="remove-coupon">Remove</button>
                            </div>
                        @else
                            <div class="input-group">
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="coupon-code" 
                                    placeholder="Enter coupon code">
                                <button class="btn btn-primary" type="button" id="apply-coupon">
                                    <span id="coupon-btn-text">Apply Coupon</span>
                                    <span id="coupon-spinner" class="spinner-border spinner-border-sm ms-2 d-none"></span>
                                </button>
                            </div>
                            <div id="coupon-message" class="mt-2"></div>
                        @endif
                    </div>

                    <hr class="my-4">

                    <!-- Payment Methods -->
                    <h5 class="mb-4">Payment</h5>
                    
                    <form method="POST" action="{{ route('checkout.process') }}" id="checkout-form">
                        @csrf
                        
                        <!-- Shipping Address -->
                        <div class="mb-4">
                            <label for="address" class="form-label fw-bold">Shipping Address</label>
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

                        <!-- Payment Method Tabs -->
                        <div class="payment-methods">
                            <!-- Stripe Card Payment -->
                            <div class="payment-option mb-3">
                                <input 
                                    type="radio" 
                                    class="btn-check" 
                                    name="payment_method" 
                                    id="stripe-radio" 
                                    value="stripe" 
                                    checked>
                                <label class="btn btn-outline-primary w-100 text-start py-3" for="stripe-radio">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-credit-card me-2"></i>
                                            <strong>Pay with Card</strong>
                                        </div>
                                        <div>
                                            <img src="https://img.icons8.com/color/30/000000/visa.png" alt="Visa" class="me-1">
                                            <img src="https://img.icons8.com/color/30/000000/mastercard.png" alt="Mastercard">
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Stripe Card Details (shown when selected) -->
                            <div id="stripe-details" class="payment-details">
                                <div class="card bg-light border-0 mb-3">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-lock text-success me-2"></i>
                                            <small class="text-muted">Secure, fast checkout with Link</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label small">Card number</label>
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                placeholder="1234 1234 1234 1234"
                                                readonly>
                                        </div>
                                        
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="form-label small">Expiry date</label>
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    placeholder="MM / YY"
                                                    readonly>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small">Security code</label>
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    placeholder="CVC"
                                                    readonly>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3 mt-3">
                                            <label class="form-label small">Country</label>
                                            <select class="form-select" disabled>
                                                <option>Pakistan</option>
                                            </select>
                                        </div>
                                        
                                        <div class="alert alert-info small mb-0" role="alert">
                                            <i class="fas fa-info-circle me-2"></i>
                                            You'll be redirected to Stripe's secure payment page
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PayPal Payment -->
                            <div class="payment-option">
                                <input 
                                    type="radio" 
                                    class="btn-check" 
                                    name="payment_method" 
                                    id="paypal-radio" 
                                    value="paypal">
                                <label class="btn btn-outline-primary w-100 text-start py-3" for="paypal-radio">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fab fa-paypal me-2"></i>
                                            <strong>PayPal</strong>
                                        </div>
                                        <img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_37x23.jpg" alt="PayPal" style="height: 24px;">
                                    </div>
                                </label>
                            </div>

                            <!-- PayPal Details (shown when selected) -->
                            <div id="paypal-details" class="payment-details d-none">
                                <div class="card bg-light border-0 mt-3">
                                    <div class="card-body text-center py-4">
                                        <i class="fab fa-paypal text-primary" style="font-size: 48px;"></i>
                                        <p class="mt-3 mb-0 text-muted">
                                            You'll be redirected to PayPal to complete your purchase securely.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 mt-4" id="submit-btn">
                            <span id="btn-text">Pay now</span>
                            <span id="btn-spinner" class="spinner-border spinner-border-sm ms-2 d-none"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Side - Order Summary -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 position-sticky" style="top: 20px;">
                <div class="card-body p-4">
                    <h5 class="mb-4">Order Summary</h5>
                    
                    <!-- Cart Items -->
                    <div class="mb-3">
                        @foreach($cartItems as $item)
                        <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                            @if($item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="me-3 rounded" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <div class="bg-light me-3 rounded d-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $item->product->name }}</h6>
                                <small class="text-muted">Quantity: {{ $item->quantity }}</small>
                            </div>
                            <div class="text-end">
                                <strong>${{ number_format($item->price * $item->quantity, 2) }}</strong>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pricing Details -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span id="subtotal-amount">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        
                        @if($discountAmount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>
                                <i class="fas fa-tag me-1"></i>Discount
                                <small class="text-muted">({{ $appliedCoupon }})</small>
                            </span>
                            <span id="discount-amount">-${{ number_format($discountAmount, 2) }}</span>
                        </div>
                        @else
                        <div class="d-flex justify-content-between mb-2 text-success d-none" id="discount-row">
                            <span>
                                <i class="fas fa-tag me-1"></i>Discount
                                <small class="text-muted" id="discount-code"></small>
                            </span>
                            <span id="discount-amount">-$0.00</span>
                        </div>
                        @endif
                        
                        <hr class="my-3">
                        
                        <div class="d-flex justify-content-between mb-0">
                            <strong class="h5">Order Total</strong>
                            <strong class="h5 text-success" id="total-amount">${{ number_format($total, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .payment-option .btn-check:checked + .btn-outline-primary {
        background-color: #f0f7ff;
        border-color: #0d6efd;
        border-width: 2px;
    }
    
    .payment-details {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .payment-option label {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .payment-option label:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stripeRadio = document.getElementById('stripe-radio');
    const paypalRadio = document.getElementById('paypal-radio');
    const stripeDetails = document.getElementById('stripe-details');
    const paypalDetails = document.getElementById('paypal-details');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnSpinner = document.getElementById('btn-spinner');
    const checkoutForm = document.getElementById('checkout-form');

    // Toggle payment details
    function togglePaymentDetails() {
        if (stripeRadio.checked) {
            stripeDetails.classList.remove('d-none');
            paypalDetails.classList.add('d-none');
        } else {
            stripeDetails.classList.add('d-none');
            paypalDetails.classList.remove('d-none');
        }
    }

    stripeRadio.addEventListener('change', togglePaymentDetails);
    paypalRadio.addEventListener('change', togglePaymentDetails);

    // Apply Coupon
    const applyCouponBtn = document.getElementById('apply-coupon');
    const couponCodeInput = document.getElementById('coupon-code');
    const couponMessage = document.getElementById('coupon-message');
    const couponBtnText = document.getElementById('coupon-btn-text');
    const couponSpinner = document.getElementById('coupon-spinner');

    if (applyCouponBtn) {
        applyCouponBtn.addEventListener('click', function() {
            const code = couponCodeInput.value.trim();
            
            if (!code) {
                showCouponMessage('Please enter a coupon code', 'danger');
                return;
            }

            couponBtnText.textContent = 'Applying...';
            couponSpinner.classList.remove('d-none');
            applyCouponBtn.disabled = true;

            fetch('{{ route("checkout.apply-coupon") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ coupon_code: code })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCouponMessage(data.message, 'success');
                    
                    // Update pricing
                    document.getElementById('discount-row').classList.remove('d-none');
                    document.getElementById('discount-code').textContent = '(' + data.coupon_code + ')';
                    document.getElementById('discount-amount').textContent = '-$' + data.discount;
                    document.getElementById('total-amount').textContent = '$' + data.total;
                    
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showCouponMessage(data.message, 'danger');
                }
            })
            .catch(error => {
                showCouponMessage('An error occurred. Please try again.', 'danger');
            })
            .finally(() => {
                couponBtnText.textContent = 'Apply Coupon';
                couponSpinner.classList.add('d-none');
                applyCouponBtn.disabled = false;
            });
        });

        couponCodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyCouponBtn.click();
            }
        });
    }

    // Remove Coupon
    const removeCouponBtn = document.getElementById('remove-coupon');
    if (removeCouponBtn) {
        removeCouponBtn.addEventListener('click', function() {
            fetch('{{ route("checkout.remove-coupon") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        });
    }

    function showCouponMessage(message, type) {
        couponMessage.innerHTML = `<div class="alert alert-${type} small mb-0">${message}</div>`;
        
        if (type === 'success') {
            setTimeout(() => {
                couponMessage.innerHTML = '';
            }, 3000);
        }
    }

    // Form submission
    checkoutForm.addEventListener('submit', function() {
        submitBtn.disabled = true;
        btnText.textContent = 'Processing...';
        btnSpinner.classList.remove('d-none');
    });
});
</script>
@endpush
@endsection