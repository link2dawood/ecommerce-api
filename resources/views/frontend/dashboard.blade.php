@extends('frontend.layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="container-fluid pt-5">
    <div class="row px-xl-5">
        <div class="col-lg-12">
            <h2 class="mb-4">Welcome back, {{ $user->name }}!</h2>

            <!-- Statistics Cards -->
            <div class="row pb-3">
                <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                    <div class="card border-0 mb-4" style="border-left: 3px solid #ffd333 !important;">
                        <div class="card-body text-center">
                            <h1 class="mb-0">{{ $stats['total_orders'] }}</h1>
                            <p class="mb-0">Total Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                    <div class="card border-0 mb-4" style="border-left: 3px solid #ff6f61 !important;">
                        <div class="card-body text-center">
                            <h1 class="mb-0">{{ $stats['pending_orders'] }}</h1>
                            <p class="mb-0">Pending Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                    <div class="card border-0 mb-4" style="border-left: 3px solid #51cbce !important;">
                        <div class="card-body text-center">
                            <h1 class="mb-0">{{ $stats['completed_orders'] }}</h1>
                            <p class="mb-0">Completed Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                    <div class="card border-0 mb-4" style="border-left: 3px solid #6bd098 !important;">
                        <div class="card-body text-center">
                            <h1 class="mb-0">{{ $stats['wishlist_count'] }}</h1>
                            <p class="mb-0">Wishlist Items</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="row pb-3">
                <div class="col-12">
                    <h4 class="mb-3">Quick Links</h4>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('shop.index') }}" class="btn btn-block btn-primary py-3">
                        <i class="fas fa-shopping-bag mr-2"></i> Browse Shop
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('orders') }}" class="btn btn-block btn-outline-primary py-3">
                        <i class="fas fa-box mr-2"></i> My Orders
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('wishlist') }}" class="btn btn-block btn-outline-primary py-3">
                        <i class="fas fa-heart mr-2"></i> Wishlist
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('profile') }}" class="btn btn-block btn-outline-primary py-3">
                        <i class="fas fa-user mr-2"></i> Profile Settings
                    </a>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 mb-5">
                        <div class="card-header bg-light">
                            <h4 class="mb-0">Recent Orders</h4>
                        </div>
                        <div class="card-body p-0">
                            @if($recent_orders->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Order #</th>
                                                <th>Date</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recent_orders as $order)
                                            <tr>
                                                <td>#{{ $order->id }}</td>
                                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                                <td>${{ number_format($order->total_amount, 2) }}</td>
                                                <td>
                                                    @if($order->status == 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @elseif($order->status == 'completed')
                                                        <span class="badge badge-success">Completed</span>
                                                    @elseif($order->status == 'cancelled')
                                                        <span class="badge badge-danger">Cancelled</span>
                                                    @else
                                                        <span class="badge badge-info">{{ ucfirst($order->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary">View Details</a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-5 text-center">
                                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">You haven't placed any orders yet.</p>
                                    <a href="{{ route('shop.index') }}" class="btn btn-primary">Start Shopping</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection