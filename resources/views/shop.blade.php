@extends('layouts.app')

@section('content')
    <h2>Shop</h2>
    <div class="row">
        @foreach($products as $product)
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="{{ $product->images->first()->url ?? 'default.jpg' }}" class="card-img-top" alt="{{ $product->name }}">
                    <div class="card-body">
                        <h5>{{ $product->name }}</h5>
                        <p>${{ $product->price }}</p>
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-primary">View</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-3">
        {{ $products->links() }}
    </div>
@endsection
