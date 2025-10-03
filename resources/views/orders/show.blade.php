@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Order #{{ $order->id }}</h2>
    <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
    <p><strong>Total:</strong> ${{ $order->total }}</p>
    <p><strong>Placed on:</strong> {{ $order->created_at->format('d M, Y H:i') }}</p>
</div>
@endsection
