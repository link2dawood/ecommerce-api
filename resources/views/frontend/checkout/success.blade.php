@extends('frontend.layouts.app')
@section('title', 'Order Success')
@section('content')
<div class="container py-5">
    <div class="text-center">
        <div class="mb-4">
            <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
        </div>
        <h2>Order Placed Successfully!</h2>
        <p class="text-muted">Your order #{{ $order->id }} has been received.</p>
        <p><strong>Total: ${{ number_format($order->total_amount, 2) }}</strong></p>
        <div class="mt-4">
            <a href="{{ route('home') }}" class="btn btn-primary">Continue Shopping</a>
            <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">View Order</a>
        </div>
    </div>
</div>
@endsection