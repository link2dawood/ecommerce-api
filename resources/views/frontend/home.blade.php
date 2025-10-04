@extends('frontend.layouts.app')

@section('title', 'My Dashboard')

@section('content')<!-- Hero Section -->
<div class="container-fluid mb-5">
    <div class="row border-top px-xl-5">
        <div class="col-lg-12">
            <div id="header-carousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active" style="height: 410px;">
                        <img class="img-fluid" src="{{ asset('img/carousel-1.jpg') }}" alt="Image">
                        <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                            <div class="p-3" style="max-width: 700px;">
                                <h4 class="text-light text-uppercase font-weight-medium mb-3">Welcome to EShopper</h4>
                                <h3 class="display-4 text-white font-weight-semi-bold mb-4">Best Products</h3>
                                <a href="{{ route('shop.index') }}" class="btn btn-light py-2 px-3">Shop Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Products -->
<div class="container-fluid pt-5">
    <div class="text-center mb-4">
        <h2 class="section-title px-5"><span class="px-2">Featured Products</span></h2>
    </div>
    <div class="row px-xl-5 pb-3">
        <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
            <div class="card product-item border-0 mb-4">
                <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
                    <img class="img-fluid w-100" src="{{ asset('img/product-1.jpg') }}" alt="">
                </div>
                <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                    <h6 class="text-truncate mb-3">Sample Product</h6>
                    <div class="d-flex justify-content-center">
                        <h6>$99.00</h6>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between bg-light border">
                    <a href="#" class="btn btn-sm text-dark p-0"><i class="fas fa-eye text-primary mr-1"></i>View</a>
                    <a href="#" class="btn btn-sm text-dark p-0"><i class="fas fa-shopping-cart text-primary mr-1"></i>Add to Cart</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="container-fluid pt-5">
    <div class="row px-xl-5">
        <div class="col text-center">
            <h3>Start Shopping Today!</h3>
            <p class="mb-4">Discover amazing products at great prices</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary py-2 px-4">Browse Shop</a>
        </div>
    </div>
</div>
@endsection