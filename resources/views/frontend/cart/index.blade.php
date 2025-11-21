@extends('frontend.layouts.app')
@section('title', 'Shopping Cart')

@section('content')
<div class="container-fluid pt-5">
    <div class="row px-xl-5">
        <div class="col-lg-12">
            <h2 class="mb-4">Shopping Cart</h2>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            @if($cartItems->isEmpty())
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                        <h4>Your cart is empty</h4>
                        <p class="text-muted">Add some products to get started!</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-shopping-bag"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            @else
                <div class="row">
                    <!-- Cart Items -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Cart Items ({{ $cartItems->count() }})</h5>
                                    <form action="{{ route('cart.clear') }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to clear your cart?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i> Clear Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width: 100px;">Image</th>
                                                <th>Product</th>
                                                <th style="width: 120px;">Price</th>
                                                <th style="width: 150px;">Quantity</th>
                                                <th style="width: 120px;">Subtotal</th>
                                                <th style="width: 80px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cartItems as $item)
                                                <tr>
                                                    <td>
                                                        <img src="{{ $item->product->images->first()->url ?? asset('images/default-product.jpg') }}" 
                                                             alt="{{ $item->product->name }}"
                                                             class="img-thumbnail"
                                                             style="width: 80px; height: 80px; object-fit: cover;">
                                                    </td>
                                                    <td>
                                                        <h6 class="mb-1">{{ $item->product->name }}</h6>
                                                        <small class="text-muted">Stock: {{ $item->product->stock_quantity }}</small>
                                                    </td>
                                                    <td>
                                                        <strong>${{ number_format($item->price, 2) }}</strong>
                                                    </td>
                                                    <td>
                                                        <form action="{{ route('cart.update', Auth::check() ? $item->id : $item->product->id) }}" 
                                                              method="POST" class="quantity-form">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="input-group input-group-sm">
                                                                <button type="button" class="btn btn-outline-secondary btn-minus">
                                                                    <i class="fas fa-minus"></i>
                                                                </button>
                                                                <input type="number" 
                                                                       name="quantity" 
                                                                       class="form-control text-center quantity-input" 
                                                                       value="{{ $item->quantity }}"
                                                                       min="1"
                                                                       max="{{ $item->product->stock_quantity }}"
                                                                       style="width: 60px;">
                                                                <button type="button" class="btn btn-outline-secondary btn-plus">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <strong class="text-primary">
                                                            ${{ number_format($item->quantity * $item->price, 2) }}
                                                        </strong>
                                                    </td>
                                                    <td>
    <form action="{{ route('cart.remove', Auth::check() ? $item->id : $item->product->id) }}" 
          method="POST"
          onsubmit="return confirm('Remove this item from cart?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-times"></i>
        </button>
    </form>
</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <strong>${{ number_format($subtotal, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax (10%):</span>
                                    <strong>${{ number_format($tax, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <strong>Free</strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <h5>Total:</h5>
                                    <h5 class="text-primary">${{ number_format($total, 2) }}</h5>
                                </div>

                                @auth
                                    <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block btn-lg">
                                        <i class="fas fa-lock"></i> Proceed to Checkout
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-block btn-lg">
                                        <i class="fas fa-sign-in-alt"></i> Login to Checkout
                                    </a>
                                    <p class="text-center text-muted small mt-2">
                                        Don't have an account? 
                                        <a href="{{ route('register') }}">Register now</a>
                                    </p>
                                @endauth
                            </div>
                        </div>

                        <!-- Security Badge -->
                        <div class="card border-0 shadow-sm mt-3">
                            <div class="card-body text-center">
                                <i class="fas fa-shield-alt fa-3x text-success mb-2"></i>
                                <p class="mb-0 small text-muted">Secure checkout with SSL encryption</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Quantity plus button
    $('.btn-plus').click(function() {
        let input = $(this).closest('.input-group').find('.quantity-input');
        let currentVal = parseInt(input.val());
        let maxVal = parseInt(input.attr('max'));
        
        if (currentVal < maxVal) {
            input.val(currentVal + 1);
            $(this).closest('form').submit();
        } else {
            alert('Maximum stock reached!');
        }
    });

    // Quantity minus button
    $('.btn-minus').click(function() {
        let input = $(this).closest('.input-group').find('.quantity-input');
        let currentVal = parseInt(input.val());
        
        if (currentVal > 1) {
            input.val(currentVal - 1);
            $(this).closest('form').submit();
        }
    });

    // Direct input change
    $('.quantity-input').change(function() {
        let val = parseInt($(this).val());
        let max = parseInt($(this).attr('max'));
        
        if (val < 1) {
            $(this).val(1);
        } else if (val > max) {
            $(this).val(max);
            alert('Maximum stock reached!');
        }
        
        $(this).closest('form').submit();
    });
});
</script>
@endpush
@endsection