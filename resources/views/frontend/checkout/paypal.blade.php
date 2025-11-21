@extends('frontend.layouts.app')

@section('title', 'PayPal Payment')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5 text-center">
                    <i class="fab fa-paypal text-primary mb-4" style="font-size: 72px;"></i>
                    
                    <h3 class="mb-3">Complete Your Payment</h3>
                    <p class="text-muted mb-4">
                        Order #{{ $order->order_number }}<br>
                        Amount: <strong class="text-success">${{ number_format($order->total_amount, 2) }}</strong>
                    </p>

                    <div id="paypal-button-container" class="mb-4"></div>

                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-shield-alt me-2"></i>
                            Your payment is processed securely through PayPal
                        </small>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=USD"></script>
<script>
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '{{ $order->total_amount }}'
                    },
                    description: 'Order #{{ $order->order_number }}'
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                window.location.href = '{{ route("checkout.paypal.execute", $order->id) }}';
            });
        },
        onError: function(err) {
            alert('An error occurred with your payment. Please try again.');
            console.error(err);
        }
    }).render('#paypal-button-container');
</script>
@endpush
@endsection