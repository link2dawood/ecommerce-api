@extends('frontend.layouts.app')
@section('title', $product->name . ' - Product Details')

@section('content')
<!-- Breadcrumb Start -->
<div class="container-fluid bg-light py-3">
    <div class="row px-xl-5">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Shop</a></li>
                    <li class="breadcrumb-item active">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Product Detail Start -->
<div class="container-fluid pt-5 pb-3">
    <div class="row px-xl-5">
        <!-- Product Images -->
        <div class="col-lg-5 mb-4">
            <div class="position-relative">
                @if($product->images->count() > 0)
                    <!-- Main Image -->
                    <div id="productCarousel" class="carousel slide mb-3" data-ride="carousel">
                        <div class="carousel-inner border rounded">
                            @foreach($product->images as $index => $image)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <img src="{{ $image->url }}" class="d-block w-100" alt="{{ $product->name }}"
                                         style="height: 500px; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                        @if($product->images->count() > 1)
                            <a class="carousel-control-prev" href="#productCarousel" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon bg-dark rounded-circle p-3"></span>
                            </a>
                            <a class="carousel-control-next" href="#productCarousel" role="button" data-slide="next">
                                <span class="carousel-control-next-icon bg-dark rounded-circle p-3"></span>
                            </a>
                        @endif
                    </div>

                    <!-- Thumbnail Images -->
                    @if($product->images->count() > 1)
                        <div class="row">
                            @foreach($product->images->take(4) as $index => $image)
                                <div class="col-3">
                                    <a href="#" data-target="#productCarousel" data-slide-to="{{ $index }}">
                                        <img src="{{ $image->url }}" class="img-thumbnail" alt="{{ $product->name }}"
                                             style="height: 100px; object-fit: cover; cursor: pointer;">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <img src="{{ asset('images/default-product.jpg') }}" class="img-fluid rounded" alt="{{ $product->name }}">
                @endif
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-7">
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h2 class="font-weight-bold flex-grow-1">{{ $product->name }}</h2>
                    
                    <!-- Wishlist Button (Right Side) -->
                    @auth
                        <button class="btn btn-outline-danger wishlist-toggle ml-3" 
                                data-product-id="{{ $product->id }}"
                                style="min-width: 45px; height: 45px;">
                            <i class="fas fa-heart {{ isset($isInWishlist) && $isInWishlist ? '' : 'far' }}"></i>
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-danger ml-3" 
                           style="min-width: 45px; height: 45px;"
                           title="Add to Wishlist">
                            <i class="far fa-heart"></i>
                        </a>
                    @endauth
                </div>
                
                <!-- Rating -->
                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning mr-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <small class="text-muted">(4.5/5 - 50 Reviews)</small>
                </div>

                <!-- Price -->
                <h3 class="text-primary font-weight-bold mb-3">
                    ${{ number_format($product->price, 2) }}
                </h3>

                <!-- Description -->
                <div class="mb-4">
                    <h5 class="font-weight-semi-bold">Description:</h5>
                    <p class="text-muted">{{ $product->description }}</p>
                </div>

                <!-- Product Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex mb-2">
                            <strong class="text-dark mr-2">Category:</strong>
                            <span>
                                <a href="{{ route('shop.index', ['category' => $product->category_id]) }}" class="text-primary">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </a>
                            </span>
                        </div>
                        <div class="d-flex mb-2">
                            <strong class="text-dark mr-2">SKU:</strong>
                            <span>{{ str_pad($product->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex mb-2">
                            <strong class="text-dark mr-2">Availability:</strong>
                            @if($product->stock_quantity > 0)
                                <span class="badge badge-success">In Stock ({{ $product->stock_quantity }} items)</span>
                            @else
                                <span class="badge badge-danger">Out of Stock</span>
                            @endif
                        </div>
                        <div class="d-flex mb-2">
                            <strong class="text-dark mr-2">Status:</strong>
                            <span class="badge badge-{{ $product->is_active ? 'success' : 'secondary' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Add to Cart Form -->
                @if($product->stock_quantity > 0)
                    <form action="{{ route('cart.add.id', $product->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="d-flex align-items-center">
                            <!-- Quantity -->
                            <div class="input-group mr-3" style="width: 150px;">
                                <div class="input-group-prepend">
                                    <button class="btn btn-outline-primary btn-minus" type="button">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                                <input type="number" name="quantity" class="form-control text-center quantity-input" 
                                       value="1" min="1" max="{{ $product->stock_quantity }}" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary btn-plus" type="button">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Add to Cart Button -->
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-shopping-cart mr-2"></i>Add to Cart
                            </button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Out of Stock!</strong> This product is currently unavailable.
                    </div>
                @endif

                <!-- Share Buttons -->
                <div class="border-top pt-3">
                    <h6 class="font-weight-semi-bold mb-2">Share this product:</h6>
                    <div class="d-inline-flex">
                        <a class="btn btn-sm btn-outline-secondary mr-2" href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a class="btn btn-sm btn-outline-secondary mr-2" href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $product->name }}" target="_blank">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a class="btn btn-sm btn-outline-secondary mr-2" href="https://pinterest.com/pin/create/button/?url={{ url()->current() }}" target="_blank">
                            <i class="fab fa-pinterest"></i>
                        </a>
                        <a class="btn btn-sm btn-outline-secondary" href="https://www.linkedin.com/shareArticle?mini=true&url={{ url()->current() }}" target="_blank">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Product Detail End -->

<!-- Product Tabs Start -->
<div class="container-fluid py-5">
    <div class="row px-xl-5">
        <div class="col-12">
            <ul class="nav nav-tabs border-bottom" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab-description">Description</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-specifications">Specifications</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-reviews">Reviews (0)</a>
                </li>
            </ul>

            <div class="tab-content py-4">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="tab-description">
                    <h4 class="mb-3">Product Description</h4>
                    <p>{{ $product->description }}</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.</p>
                </div>

                <!-- Specifications Tab -->
                <div class="tab-pane fade" id="tab-specifications">
                    <h4 class="mb-3">Product Specifications</h4>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 200px;">Product ID</th>
                                <td>{{ str_pad($product->id, 6, '0', STR_PAD_LEFT) }}</td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Price</th>
                                <td>${{ number_format($product->price, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Stock Quantity</th>
                                <td>{{ $product->stock_quantity }} units</td>
                            </tr>
                            <tr>
                                <th>Date Added</th>
                                <td>{{ $product->created_at->format('F d, Y') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="tab-reviews">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="mb-4">Customer Reviews</h4>
                            <div class="text-center py-5 border rounded">
                                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4 class="mb-4">Write a Review</h4>
                            <form>
                                <div class="form-group">
                                    <label>Your Rating</label>
                                    <div class="rating-stars">
                                        <i class="far fa-star" data-rating="1"></i>
                                        <i class="far fa-star" data-rating="2"></i>
                                        <i class="far fa-star" data-rating="3"></i>
                                        <i class="far fa-star" data-rating="4"></i>
                                        <i class="far fa-star" data-rating="5"></i>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Your Name</label>
                                    <input type="text" class="form-control" placeholder="Enter your name">
                                </div>
                                <div class="form-group">
                                    <label>Your Email</label>
                                    <input type="email" class="form-control" placeholder="Enter your email">
                                </div>
                                <div class="form-group">
                                    <label>Your Review</label>
                                    <textarea class="form-control" rows="5" placeholder="Write your review here..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Review</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Product Tabs End -->

<!-- Related Products Start -->
@if($relatedProducts->count() > 0)
<div class="container-fluid py-5 bg-light">
    <div class="row px-xl-5">
        <div class="col-12">
            <h3 class="font-weight-semi-bold mb-4">You May Also Like</h3>
            <div class="row">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card product-card h-100 border-0 shadow-sm">
                            <div class="product-image-wrapper position-relative">
                                <a href="{{ route('products.show', $relatedProduct->id) }}">
                                    <img src="{{ $relatedProduct->images->first()->url ?? asset('images/default-product.jpg') }}" 
                                         class="card-img-top product-image" 
                                         alt="{{ $relatedProduct->name }}"
                                         style="height: 200px; object-fit: cover;">
                                </a>
                                
                                <!-- Wishlist Button for Related Products -->
                                @auth
                                    <button class="btn btn-light btn-wishlist position-absolute wishlist-toggle" 
                                            data-product-id="{{ $relatedProduct->id }}"
                                            style="top: 10px; left: 10px; width: 35px; height: 35px; padding: 0; border-radius: 50%;">
                                        <i class="fas fa-heart {{ in_array($relatedProduct->id, $relatedWishlistIds ?? []) ? 'text-danger' : 'text-muted' }}"></i>
                                    </button>
                                @endauth
                                
                                @if($relatedProduct->stock_quantity < 10 && $relatedProduct->stock_quantity > 0)
                                    <span class="badge badge-warning position-absolute" style="top: 10px; right: 10px;">Low Stock</span>
                                @elseif($relatedProduct->stock_quantity == 0)
                                    <span class="badge badge-danger position-absolute" style="top: 10px; right: 10px;">Out of Stock</span>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title text-truncate mb-2">
                                    <a href="{{ route('products.show', $relatedProduct->id) }}" class="text-dark">
                                        {{ $relatedProduct->name }}
                                    </a>
                                </h6>
                                <div class="mt-auto">
                                    <h5 class="text-primary mb-3">${{ number_format($relatedProduct->price, 2) }}</h5>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('products.show', $relatedProduct->id) }}" 
                                           class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if($relatedProduct->stock_quantity > 0)
                                            <form action="{{ route('cart.add.id', $relatedProduct->id) }}" method="POST" class="flex-fill">
                                                @csrf
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                                    <i class="fas fa-shopping-cart"></i> Add
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
<!-- Related Products End -->

@push('scripts')
<script>
$(document).ready(function() {
    // Quantity Plus/Minus
    $('.btn-plus').click(function() {
        let input = $(this).closest('.input-group').find('.quantity-input');
        let currentVal = parseInt(input.val());
        let maxVal = parseInt(input.attr('max'));
        
        if (currentVal < maxVal) {
            input.val(currentVal + 1);
        } else {
            alert('Maximum stock quantity reached!');
        }
    });

    $('.btn-minus').click(function() {
        let input = $(this).closest('.input-group').find('.quantity-input');
        let currentVal = parseInt(input.val());
        
        if (currentVal > 1) {
            input.val(currentVal - 1);
        }
    });

    // Wishlist Toggle (Main Product and Related Products)
    $('.wishlist-toggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const button = $(this);
        const productId = button.data('product-id');
        const icon = button.find('i');
        const textElement = button.find('.wishlist-text');
        const isInWishlist = icon.hasClass('fas') && !icon.hasClass('far');
        
        // Disable button during request
        button.prop('disabled', true);
        
        if (isInWishlist) {
            // Remove from wishlist
            $.ajax({
                url: `/wishlist/remove/${productId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    icon.removeClass('fas').addClass('far');
                    if (textElement.length) {
                        textElement.text('Add to Wishlist');
                        button.removeClass('btn-danger').addClass('btn-outline-danger');
                    } else {
                        icon.removeClass('text-danger').addClass('text-muted');
                    }
                    showNotification('success', 'Removed from wishlist');
                    updateWishlistCount(-1);
                },
                error: function(xhr) {
                    showNotification('error', 'Error removing from wishlist');
                },
                complete: function() {
                    button.prop('disabled', false);
                }
            });
        } else {
            // Add to wishlist
            $.ajax({
                url: `/wishlist/add/${productId}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    icon.removeClass('far').addClass('fas');
                    if (textElement.length) {
                        textElement.text('In Wishlist');
                        button.removeClass('btn-outline-danger').addClass('btn-danger');
                    } else {
                        icon.removeClass('text-muted').addClass('text-danger');
                    }
                    showNotification('success', 'Added to wishlist');
                    updateWishlistCount(1);
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Error adding to wishlist';
                    showNotification('error', message);
                },
                complete: function() {
                    button.prop('disabled', false);
                }
            });
        }
    });

    // Rating Stars
    $('.rating-stars i').click(function() {
        let rating = $(this).data('rating');
        $('.rating-stars i').removeClass('fas text-warning').addClass('far');
        $('.rating-stars i').each(function(index) {
            if (index < rating) {
                $(this).removeClass('far').addClass('fas text-warning');
            }
        });
    });

    $('.rating-stars i').hover(
        function() {
            let rating = $(this).data('rating');
            $('.rating-stars i').each(function(index) {
                if (index < rating) {
                    $(this).removeClass('far').addClass('fas text-warning');
                } else {
                    $(this).removeClass('fas text-warning').addClass('far');
                }
            });
        }
    );
    
    // Show notification function
    function showNotification(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 250px;" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        $('body').append(alertHtml);
        
        setTimeout(function() {
            $('.alert').fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    // Update wishlist count in header
    function updateWishlistCount(change) {
        const countElement = $('.wishlist-count');
        if (countElement.length) {
            const currentCount = parseInt(countElement.text()) || 0;
            countElement.text(currentCount + change);
        }
    }
});
</script>
@endpush

<style>
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
}

.product-image {
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-image-wrapper {
    overflow: hidden;
}

.btn-wishlist {
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.btn-wishlist:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-wishlist i {
    font-size: 1rem;
    transition: all 0.3s ease;
}

.d-flex.gap-2 {
    gap: 0.5rem;
}

.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #6c757d;
}

.nav-tabs .nav-link.active {
    color: #D19C97;
    border-bottom-color: #D19C97;
    font-weight: 600;
}

.rating-stars i {
    font-size: 1.5rem;
    cursor: pointer;
    margin-right: 5px;
    transition: all 0.2s;
}

.rating-stars i:hover {
    transform: scale(1.2);
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    width: 40px;
    height: 40px;
}

.breadcrumb {
    background: transparent;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
}

.btn-primary {
    background-color: #D19C97;
    border-color: #D19C97;
}

.btn-primary:hover {
    background-color: #c18a84;
    border-color: #c18a84;
}

.text-primary {
    color: #D19C97 !important;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}
</style>
@endsection