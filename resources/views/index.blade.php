@extends('layouts.app')

@section('content')
<div class="container">
    <h2>My Orders</h2>

    @if($orders->isEmpty())
        <p>You have no orders yet.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>${{ $order->total }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>{{ $order->created_at->format('d M, Y') }}</td>
                    <td><a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary">View</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
