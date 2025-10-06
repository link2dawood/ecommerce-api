@extends('frontend.layouts.app')

@section('title', $category->name . ' - Products')

@section('content')
<!-- Page Header Start -->
<div class="container-fluid bg-secondary mb-5">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px">
        <h1 class="font-weight-semi-bold text-uppercase mb-3">{{ $category->name }}</h1>
        <div class="d-inline-flex">
            <p class="m-0"><a href="{{ route('home') }}">Home</a></p>
            <p class="m-0 px-2">-</p>
            <p class="m-0">{{ $category->name }}</p>
        </div>
    </div>
</div>
<!-- Page Header End -->

<!-- Shop Start -->
<div class="container-fluid pt-5">
    <div class="row px-xl-5">
        <!-- Shop Sidebar Start -->
        <div class="col-lg-3 col-md-12">
            <!-- Category Start -->
            <div class="border-bottom mb-4 pb-4">
                <h5 class="font-weight-semi-bold mb-4">Filter by Category</h5>
                @if($categories ?? [])
                    @foreach($categories as $cat)
                        <div class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                            <a href="{{ route('category.show', $cat->slug) }}" 
                               class="text-dark {{ $cat->id == $category->id ? 'font-weight-bold' : '' }}">
                                {{ $cat->name }}
                            </a>
                            <span class="badge border font-weight-normal">{{ $cat->products_count ?? 0 }}</span>
                        </div>
                    @endforeach
                @endif
            </div>
            <!-- Category End -->
        </div>
        <!-- Shop Sidebar End -->

        <!-- Shop Product Start -->
        <div class="col-lg-9 col-md-12">
            <div class="row pb-3">
                <div class="col-12 pb-1">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <p class="m-0">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</p>
                        </div>
                        <div class="dropdown ml-4">
                            <button class="btn border dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Sort by
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="triggerId">
                                <a class="dropdown-item" href="#">Latest</a>
                                <a class="dropdown-item" href="#">Popularity</a>
                                <a class="dropdown-item" href="#">Price: Low to High</a>
                                <a class="dropdown-item" href="#">Price: High to Low</a>
                            </div>
                        </div>
                    </div>
                </div>

                @forelse($products as $product)
                    <div class="col-lg-4 col-md-6 col-sm-12 pb-1">
                        <div class="card product-item border-0 mb-4">
                            <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
                                <img class="img-fluid w-100" src="{{ asset($product->image ?? 'img/product-default.jpg') }}" alt="{{ $product->name }}">
                            </div>
                            <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                                <h6 class="text-truncate mb-3">{{ $product->name }}</h6>
                                <div class="d-flex justify-content-center">
                                    <h6>${{ number_format($product->price, 2) }}</h6>
                                    @if($product->compare_price)
                                        <h6 class="text-muted ml-2"><del>${{ number_format($product->compare_price, 2) }}</del></h6>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-between bg-light border">
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm text-dark p-0">
                                    <i class="fas fa-eye text-primary mr-1"></i>View Detail
                                </a>
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm text-dark p-0">
                                        <i class="fas fa-shopping-cart text-primary mr-1"></i>Add To Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <h5>No products found in this category</h5>
                            <p>Check back later or browse other categories.</p>
                            <a href="{{ route('shop.index') }}" class="btn btn-primary">Browse All Products</a>
                        </div>
                    </div>
                @endforelse

                <!-- Pagination -->
                <div class="col-12 pb-1">
                    <nav aria-label="Page navigation">
                        {{ $products->links() }}
                    </nav>
                </div>
            </div>
        </div>
        <!-- Shop Product End -->
    </div>
</div>
<!-- Shop End -->
@endsection