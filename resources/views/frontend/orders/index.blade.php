@extends('frontend.layouts.app')
@section('title', 'My Orders')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">My Orders</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($orders->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>
                            <strong>{{ $order->order_number }}</strong>
                        </td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td>{{ $order->items->count() }} item(s)</td>
                        <td>${{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $order->status_badge }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $order->payment_status_badge }}">
                                {{ ucfirst($order->payment_status ?? 'pending') }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                View Details
                            </a>
                            @if(in_array($order->status, ['pending', 'processing']))
                                <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $orders->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <p class="mb-0">You haven't placed any orders yet.</p>
            <a href="{{ route('home') }}" class="btn btn-primary mt-3">Start Shopping</a>
        </div>
    @endif
</div>
@endsection