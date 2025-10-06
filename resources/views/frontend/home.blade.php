@extends('frontend.layouts.app')
@section('title', 'Home - E-Commerce')
@section('content')

<!-- Featured Start -->
<div class="container-fluid pt-5">
    <div class="row px-xl-5 pb-3">
        <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
            <div class="d-flex align-items-center border mb-4" style="padding: 30px;">
                <h1 class="fa fa-check text-primary m-0 mr-3"></h1>
                <h5 class="font-weight-semi-bold m-0">Quality Product</h5>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
            <div class="d-flex align-items-center border mb-4" style="padding: 30px;">
                <h1 class="fa fa-shipping-fast text-primary m-0 mr-2"></h1>
                <h5 class="font-weight-semi-bold m-0">Free Shipping</h5>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
            <div class="d-flex align-items-center border mb-4" style="padding: 30px;">
                <h1 class="fas fa-exchange-alt text-primary m-0 mr-3"></h1>
                <h5 class="font-weight-semi-bold m-0">14-Day Return</h5>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
            <div class="d-flex align-items-center border mb-4" style="padding: 30px;">
                <h1 class="fa fa-phone-volume text-primary m-0 mr-3"></h1>
                <h5 class="font-weight-semi-bold m-0">24/7 Support</h5>
            </div>
        </div>
    </div>
</div>
<!-- Featured End -->

<!-- Categories Start -->
<div class="container-fluid pt-5">
    <div class="row px-xl-5 pb-3">
        @forelse($categories ?? [] as $category)
            <div class="col-lg-4 col-md-6 pb-1">
                <div class="cat-item d-flex flex-column border mb-4" style="padding: 30px;">
                    <p class="text-right">{{ $category->products_count ?? 0 }} Products</p>
                    <a href="{{ route('category.show', $category->slug) }}" class="cat-img position-relative overflow-hidden mb-3">
                        <img class="img-fluid" src="{{ $category->image ? asset('storage/' . $category->image) : asset('img/cat-1.jpg') }}" alt="{{ $category->name }}">
                    </a>
                    <h5 class="font-weight-semi-bold m-0">{{ $category->name }}</h5>
                </div>
            </div>
        @empty
            <div class="col-lg-4 col-md-6 pb-1">
                <div class="cat-item d-flex flex-column border mb-4" style="padding: 30px;">
                    <p class="text-right">15 Products</p>
                    <a href="{{ route('shop.index') }}" class="cat-img position-relative overflow-hidden mb-3">
                        <img class="img-fluid" src="{{ asset('img/cat-1.jpg') }}" alt="">
                    </a>
                    <h5 class="font-weight-semi-bold m-0">Men's Dresses</h5>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 pb-1">
                <div class="cat-item d-flex flex-column border mb-4" style="padding: 30px;">
                    <p class="text-right">15 Products</p>
                    <a href="{{ route('shop.index') }}" class="cat-img position-relative overflow-hidden mb-3">
                        <img class="img-fluid" src="{{ asset('img/cat-2.jpg') }}" alt="">
                    </a>
                    <h5 class="font-weight-semi-bold m-0">Women's Dresses</h5>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 pb-1">
                <div class="cat-item d-flex flex-column border mb-4" style="padding: 30px;">
                    <p class="text-right">15 Products</p>
                    <a href="{{ route('shop.index') }}" class="cat-img position-relative overflow-hidden mb-3">
                        <img class="img-fluid" src="{{ asset('img/cat-3.jpg') }}" alt="">
                    </a>
                    <h5 class="font-weight-semi-bold m-0">Baby's Dresses</h5>
                </div>
            </div>
        @endforelse
    </div>
</div>
<!-- Categories End -->

<!-- Offer Start -->
<div class="container-fluid offer pt-5">
    <div class="row px-xl-5">
        <div class="col-md-6 pb-4">
            <div class="position-relative bg-secondary text-center text-md-right text-white mb-2 py-5 px-5">
                <img src="{{ asset('img/offer-1.png') }}" alt="">
                <div class="position-relative" style="z-index: 1;">
                    <h5 class="text-uppercase text-primary mb-3">20% off the all order</h5>
                    <h1 class="mb-4 font-weight-semi-bold">Spring Collection</h1>
                    <a href="{{ route('shop.index') }}" class="btn btn-outline-primary py-md-2 px-md-3">Shop Now</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 pb-4">
            <div class="position-relative bg-secondary text-center text-md-left text-white mb-2 py-5 px-5">
                <img src="{{ asset('img/offer-2.png') }}" alt="">
                <div class="position-relative" style="z-index: 1;">
                    <h5 class="text-uppercase text-primary mb-3">20% off the all order</h5>
                    <h1 class="mb-4 font-weight-semi-bold">Winter Collection</h1>
                    <a href="{{ route('shop.index') }}" class="btn btn-outline-primary py-md-2 px-md-3">Shop Now</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Offer End -->

<!-- Products Start -->
<div class="container-fluid pt-5">
    <div class="text-center mb-4">
        <h2 class="section-title px-5"><span class="px-2">Trendy Products</span></h2>
    </div>
    <div class="row px-xl-5 pb-3">
        @forelse($products ?? [] as $product)
            <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                <div class="card product-item border-0 mb-4">
                    <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
                        <img class="img-fluid w-100" src="{{ $product->image ? asset('storage/' . $product->image) : asset('img/product-1.jpg') }}" alt="{{ $product->name }}">
                    </div>
                    <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                        <h6 class="text-truncate mb-3">{{ $product->name }}</h6>
                        <div class="d-flex justify-content-center">
                            <h6>${{ number_format($product->price, 2) }}</h6>
                            @if($product->old_price)
                                <h6 class="text-muted ml-2"><del>${{ number_format($product->old_price, 2) }}</del></h6>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between bg-light border">
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm text-dark p-0">
                            <i class="fas fa-eye text-primary mr-1"></i>View Detail
                        </a>
                        <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-sm text-dark p-0 border-0 bg-transparent">
                                <i class="fas fa-shopping-cart text-primary mr-1"></i>Add To Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            @for($i = 1; $i <= 8; $i++)
                <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                    <div class="card product-item border-0 mb-4">
                        <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
                            <img class="img-fluid w-100" src="{{ asset('img/product-' . $i . '.jpg') }}" alt="">
                        </div>
                        <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                            <h6 class="text-truncate mb-3">Colorful Stylish Shirt</h6>
                            <div class="d-flex justify-content-center">
                                <h6>$123.00</h6><h6 class="text-muted ml-2"><del>$123.00</del></h6>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between bg-light border">
                            <a href="{{ route('shop.index') }}" class="btn btn-sm text-dark p-0">
                                <i class="fas fa-eye text-primary mr-1"></i>View Detail
                            </a>
                            <a href="{{ route('shop.index') }}" class="btn btn-sm text-dark p-0">
                                <i class="fas fa-shopping-cart text-primary mr-1"></i>Add To Cart
                            </a>
                        </div>
                    </div>
                </div>
            @endfor
        @endforelse
    </div>
</div>
<!-- Products End -->

<!-- Subscribe Start -->
<div class="container-fluid bg-secondary my-5">
    <div class="row justify-content-md-center py-5 px-xl-5">
        <div class="col-md-6 col-12 py-5">
            <div class="text-center mb-2 pb-2">
                <h2 class="section-title px-5 mb-3"><span class="bg-secondary px-2">Stay Updated</span></h2>
                <p>Subscribe to our newsletter and get exclusive offers and updates delivered to your inbox.</p>
            </div>
            <form action="{{ route('newsletter.subscribe') }}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="email" name="email" class="form-control border-white p-4" placeholder="Your Email Address" required>
                    <div class="input-group-append">
                        <button class="btn btn-primary px-4" type="submit">Subscribe</button>
                    </div>
                </div>
                @if(session('newsletter_success'))
                    <p class="text-success mt-2 mb-0">{{ session('newsletter_success') }}</p>
                @endif
                @error('email')
                    <p class="text-danger mt-2 mb-0">{{ $message }}</p>
                @enderror
            </form>
        </div>
    </div>
</div>
<!-- Subscribe End -->

<!-- Products Start -->
<div class="container-fluid pt-5">
    <div class="text-center mb-4">
        <h2 class="section-title px-5"><span class="px-2">Just Arrived</span></h2>
    </div>
    <div class="row px-xl-5 pb-3">
        @forelse($newProducts ?? [] as $product)
            <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                <div class="card product-item border-0 mb-4">
                    <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
                        <img class="img-fluid w-100" src="{{ $product->image ? asset('storage/' . $product->image) : asset('img/product-1.jpg') }}" alt="{{ $product->name }}">
                    </div>
                    <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                        <h6 class="text-truncate mb-3">{{ $product->name }}</h6>
                        <div class="d-flex justify-content-center">
                            <h6>${{ number_format($product->price, 2) }}</h6>
                            @if($product->old_price)
                                <h6 class="text-muted ml-2"><del>${{ number_format($product->old_price, 2) }}</del></h6>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between bg-light border">
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm text-dark p-0">
                            <i class="fas fa-eye text-primary mr-1"></i>View Detail
                        </a>
                        <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-sm text-dark p-0 border-0 bg-transparent">
                                <i class="fas fa-shopping-cart text-primary mr-1"></i>Add To Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            @for($i = 1; $i <= 8; $i++)
                <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                    <div class="card product-item border-0 mb-4">
                        <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
                            <img class="img-fluid w-100" src="{{ asset('img/product-' . $i . '.jpg') }}" alt="">
                        </div>
                        <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                            <h6 class="text-truncate mb-3">Colorful Stylish Shirt</h6>
                            <div class="d-flex justify-content-center">
                                <h6>$123.00</h6><h6 class="text-muted ml-2"><del>$123.00</del></h6>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between bg-light border">
                            <a href="{{ route('shop.index') }}" class="btn btn-sm text-dark p-0">
                                <i class="fas fa-eye text-primary mr-1"></i>View Detail
                            </a>
                            <a href="{{ route('shop.index') }}" class="btn btn-sm text-dark p-0">
                                <i class="fas fa-shopping-cart text-primary mr-1"></i>Add To Cart
                            </a>
                        </div>
                    </div>
                </div>
            @endfor
        @endforelse
    </div>
</div>
<!-- Products End -->

<!-- Vendor Start -->
<div class="container-fluid py-5">
    <div class="row px-xl-5">
        <div class="col">
            <div class="owl-carousel vendor-carousel">
                <div class="vendor-item border p-4">
                    <img src="{{ asset('img/vendor-1.jpg') }}" alt="">
                </div>
                <div class="vendor-item border p-4">
                    <img src="{{ asset('img/vendor-2.jpg') }}" alt="">
                </div>
                <div class="vendor-item border p-4">
                    <img src="{{ asset('img/vendor-3.jpg') }}" alt="">
                </div>
                <div class="vendor-item border p-4">
                    <img src="{{ asset('img/vendor-4.jpg') }}" alt="">
                </div>
                <div class="vendor-item border p-4">
                    <img src="{{ asset('img/vendor-5.jpg') }}" alt="">
                </div>
                <div class="vendor-item border p-4">
                    <img src="{{ asset('img/vendor-6.jpg') }}" alt="">
                </div>
                <div class="vendor-item border p-4">
                    <img src="{{ asset('img/vendor-7.jpg') }}" alt="">
                </div>
                <div class="vendor-item border p-4">
                    <img src="{{ asset('img/vendor-8.jpg') }}" alt="">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Vendor End -->

@endsection