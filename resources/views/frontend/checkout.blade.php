@extends('frontend.layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="container">
    <h2>Checkout</h2>

    <form method="POST" action="{{ route('checkout.store') }}">
        @csrf

        <div class="mb-3">
            <label>Shipping Address</label>
            <textarea name="address" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label>Payment Method</label>
            <select name="payment_method" class="form-control" required>
                <option value="cod">Cash on Delivery</option>
                <option value="card">Credit/Debit Card</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Place Order</button>
    </form>
</div>
@endsection
