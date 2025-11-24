@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Order #{{ $order->order_number }}</h2>
                    <p class="text-muted mb-0">Order placed on {{ $order->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Order
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Order Status & Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Status & Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Order Status</h6>
                            <span class="badge bg-{{ $order->status_badge }} p-2">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Payment Status</h6>
                            <span class="badge bg-{{ $order->payment_status_badge }} p-2">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Order Number</h6>
                            <p class="mb-3"><strong>{{ $order->order_number }}</strong></p>

                            <h6 class="text-muted mb-2">Order Date</h6>
                            <p class="mb-3">{{ $order->created_at->format('M d, Y h:i A') }}</p>

                            <h6 class="text-muted mb-2">Payment Method</h6>
                            <p class="mb-0">{{ $order->payment_method ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Currency</h6>
                            <p class="mb-3">{{ $order->currency }}</p>

                            <h6 class="text-muted mb-2">Order Notes</h6>
                            <p class="mb-0">{{ $order->notes ?? 'No notes' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    @if($order->items && count($order->items) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->product->name ?? 'Deleted Product' }}</strong><br>
                                                @if($item->product)
                                                    <small class="text-muted">{{ Str::limit($item->product->description ?? $item->product->short_description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <code>{{ $item->product->sku ?? 'N/A' }}</code>
                                            </td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $order->currency }} {{ number_format($item->unit_price, 2) }}</td>
                                            <td><strong>{{ $order->currency }} {{ number_format($item->quantity * $item->unit_price, 2) }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No items in this order</p>
                    @endif
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Shipping Address</h5>
                </div>
                <div class="card-body">
                    @php
                        $shipping = is_array($order->shipping_address) 
                            ? $order->shipping_address 
                            : json_decode($order->shipping_address, true) ?? [];
                    @endphp
                    
                    @if($shipping && count($shipping) > 0)
                        <p class="mb-1"><strong>{{ $shipping['name'] ?? 'N/A' }}</strong></p>
                        <p class="mb-1">{{ $shipping['address'] ?? '' }}</p>
                        <p class="mb-1">{{ $shipping['city'] ?? '' }}, {{ $shipping['state'] ?? '' }} {{ $shipping['zip'] ?? '' }}</p>
                        <p class="mb-0">{{ $shipping['country'] ?? '' }}</p>
                    @else
                        <p class="text-muted mb-0">No shipping address provided</p>
                    @endif
                </div>
            </div>

            <!-- Billing Address -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Billing Address</h5>
                </div>
                <div class="card-body">
                    @php
                        $billing = is_array($order->billing_address) 
                            ? $order->billing_address 
                            : json_decode($order->billing_address, true) ?? [];
                    @endphp
                    
                    @if($billing && count($billing) > 0)
                        <p class="mb-1"><strong>{{ $billing['name'] ?? 'N/A' }}</strong></p>
                        <p class="mb-1">{{ $billing['address'] ?? '' }}</p>
                        <p class="mb-1">{{ $billing['city'] ?? '' }}, {{ $billing['state'] ?? '' }} {{ $billing['zip'] ?? '' }}</p>
                        <p class="mb-0">{{ $billing['country'] ?? '' }}</p>
                    @else
                        <p class="text-muted mb-0">No billing address provided</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-muted mb-2">Name</h6>
                    <p class="mb-3">
                        <strong>{{ $order->user->name ?? 'N/A' }}</strong>
                    </p>

                    <h6 class="text-muted mb-2">Email</h6>
                    <p class="mb-3">
                        <a href="mailto:{{ $order->user->email ?? '' }}">{{ $order->user->email ?? 'N/A' }}</a>
                    </p>

                    <h6 class="text-muted mb-2">Phone</h6>
                    <p class="mb-0">{{ $order->user->phone ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong>{{ $order->currency }} {{ number_format($order->subtotal, 2) }}</strong>
                    </div>

                    @if($order->tax_amount > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax</span>
                            <strong>{{ $order->currency }} {{ number_format($order->tax_amount, 2) }}</strong>
                        </div>
                    @endif

                    @if($order->shipping_amount > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <strong>{{ $order->currency }} {{ number_format($order->shipping_amount, 2) }}</strong>
                        </div>
                    @endif

                    @if($order->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Discount</span>
                            <strong class="text-success">-{{ $order->currency }} {{ number_format($order->discount_amount, 2) }}</strong>
                        </div>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between">
                        <span><strong>Total</strong></span>
                        <strong class="text-primary" style="font-size: 1.25rem;">
                            {{ $order->currency }} {{ number_format($order->total_amount, 2) }}
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-warning w-100 mb-2">
                        <i class="fas fa-edit"></i> Edit Order
                    </a>

                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this order?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Delete Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection