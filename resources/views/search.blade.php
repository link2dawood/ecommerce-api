@extends('layouts.app')

@section('content')
    <h2>Search results for: {{ $query }}</h2>
    @if($products->count() > 0)
        <ul>
            @foreach($products as $product)
                <li>{{ $product->name }}</li>
            @endforeach
        </ul>
    @else
        <p>No products found.</p>
    @endif
@endsection
