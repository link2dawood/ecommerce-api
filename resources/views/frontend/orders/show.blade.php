@extends('frontend.layouts.app')
@section('title', 'Order Details - ' . $order->order_number)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Order Details</h2>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back to Orders</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Order Information -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                            <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                            <p><strong>Payment Method:</strong> {{ strtoupper($order->payment_method ?? 'COD') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Order Status:</strong> 
                                <span class="badge bg-{{ $order->status_badge }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </p>
                            <p>
                                <strong>Payment Status:</strong> 
                                <span class="badge bg-{{ $order->payment_status_badge }}">
                                    {{ ucfirst($order->payment_status ?? 'pending') }}
                                </span>
                            </p>
                            <p><strong>Total Amount:</strong> <span class="text-success fw-bold">${{ number_format($order->total_amount, 2) }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Shipping Address</h5>
                </div>
                <div class="card-body">
                    @if(is_array($order->shipping_address))
                        <p class="mb-1">{{ $order->shipping_address['address'] ?? '' }}</p>
                        <p class="mb-1">{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['zip'] ?? '' }}</p>
                        <p class="mb-0">{{ $order->shipping_address['country'] ?? '' }}</p>
                    @else
                        <p class="mb-0">{{ $order->shipping_address }}</p>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product && $item->product->images && $item->product->images->first())
                                                <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     class="img-thumbnail me-3" 
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <strong>{{ $item->product->name ?? 'Product Not Available' }}</strong>
                                                @if($item->product && $item->product->categories && $item->product->categories->first())
                                                    <br><small class="text-muted">{{ $item->product->categories->first()->name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->total ?? ($item->price * $item->quantity), 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                    <td><strong>${{ number_format($order->subtotal ?? $order->total_amount, 2) }}</strong></td>
                                </tr>
                                @if($order->tax_amount > 0)
                                <tr>
                                    <td colspan="3" class="text-end">Tax:</td>
                                    <td>${{ number_format($order->tax_amount, 2) }}</td>
                                </tr>
                                @endif
                                @if($order->shipping_amount > 0)
                                <tr>
                                    <td colspan="3" class="text-end">Shipping:</td>
                                    <td>${{ number_format($order->shipping_amount, 2) }}</td>
                                </tr>
                                @endif
                                @if($order->discount_amount > 0)
                                <tr>
                                    <td colspan="3" class="text-end">Discount:</td>
                                    <td class="text-success">-${{ number_format($order->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td><strong class="text-success">${{ number_format($order->total_amount, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            @if(in_array($order->status, ['pending', 'processing']))
                                <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-danger">Cancel Order</button>
                                </form>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('orders.track', $order->id) }}" class="btn btn-primary">Track Order</a>
                            <button onclick="window.print()" class="btn btn-secondary">Print Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .card-header, nav { display: none !important; }
}
</style>
@endsection