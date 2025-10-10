@extends('frontend.layouts.app')
@section('title', 'Track Order - ' . $order->order_number)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Track Order</h2>
                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary">Back to Order</a>
            </div>

            <!-- Order Info Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Order Number:</strong></p>
                            <p>{{ $order->order_number }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Order Date:</strong></p>
                            <p>{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Total Amount:</strong></p>
                            <p class="text-success fw-bold">${{ number_format($order->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Status Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="order-timeline">
                        @foreach($timeline as $index => $step)
                        <div class="timeline-item {{ $step['completed'] ? 'completed' : '' }}">
                            <div class="timeline-marker">
                                @if($step['completed'])
                                    <i class="fas fa-check-circle"></i>
                                @else
                                    <i class="far fa-circle"></i>
                                @endif
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">{{ $step['label'] }}</h6>
                                @if($step['completed'] && $step['status'] === $order->status)
                                    <p class="text-muted mb-0">
                                        <small>{{ $order->updated_at->format('M d, Y h:i A') }}</small>
                                    </p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="alert alert-info mt-4">
                        <strong>Current Status:</strong> 
                        <span class="badge bg-{{ $order->status_badge }} ms-2">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    @if($order->status === 'shipped')
                        <div class="alert alert-success">
                            <i class="fas fa-truck me-2"></i>
                            Your order has been shipped and is on its way!
                        </div>
                    @elseif($order->status === 'delivered')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Your order has been delivered successfully!
                        </div>
                    @elseif($order->status === 'cancelled')
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle me-2"></i>
                            This order has been cancelled.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Items Summary -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Items ({{ $order->items->count() }})</h5>
                </div>
                <div class="card-body">
                    @foreach($order->items as $item)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        @if($item->product && $item->product->images && $item->product->images->first())
                            <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                 alt="{{ $item->product->name }}" 
                                 class="img-thumbnail me-3" 
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        @endif
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $item->product->name ?? 'Product Not Available' }}</h6>
                            <p class="text-muted mb-1">Quantity: {{ $item->quantity }}</p>
                            <p class="mb-0"><strong>${{ number_format($item->price, 2) }}</strong></p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.order-timeline {
    position: relative;
    padding-left: 40px;
}

.timeline-item {
    position: relative;
    padding-bottom: 30px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -28px;
    top: 30px;
    width: 2px;
    height: calc(100% - 10px);
    background: #dee2e6;
}

.timeline-item.completed:not(:last-child)::before {
    background: #28a745;
}

.timeline-marker {
    position: absolute;
    left: -40px;
    top: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.timeline-item.completed .timeline-marker {
    color: #28a745;
}

.timeline-item:not(.completed) .timeline-marker {
    color: #dee2e6;
}

.timeline-content h6 {
    font-weight: 600;
    color: #333;
}

.timeline-item.completed .timeline-content h6 {
    color: #28a745;
}
</style>
@endsection