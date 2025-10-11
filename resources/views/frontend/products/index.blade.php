@extends('frontend.layouts.app')
@section('title', 'All Products')

@section('content')
<!-- Breadcrumb Start -->
<div class="container-fluid bg-light py-3">
    <div class="row px-xl-5">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Products</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Shop Start -->
<div class="container-fluid pt-5 pb-3">
    <div class="row px-xl-5">
        <!-- Sidebar Start -->
        <div class="col-lg-3 col-md-4">
            <!-- Filter by Category -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-filter mr-2"></i>Categories</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('products.index') }}" 
                           class="list-group-item list-group-item-action {{ !request('category') ? 'active' : '' }}">
                            All Products
                        </a>
                        @foreach($categories ?? [] as $category)
                            <a href="{{ route('products.index', ['category' => $category->id]) }}" 
                               class="list-group-item list-group-item-action {{ request('category') == $category->id ? 'active' : '' }}">
                                {{ $category->name }}
                                <span class="badge badge-secondary float-right">{{ $category->products_count ?? 0 }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Filter by Price -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-dollar-sign mr-2"></i>Price Range</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.index') }}" method="GET">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        
                        <div class="form-group">
                            <label>Min Price</label>
                            <input type="number" name="min_price" class="form-control" 
                                   value="{{ request('min_price') }}" placeholder="$0" min="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <label>Max Price</label>
                            <input type="number" name="max_price" class="form-control" 
                                   value="{{ request('max_price') }}" placeholder="$1000" min="0" step="0.01">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Apply Filter</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Sidebar End -->

        <!-- Products Start -->
        <div class="col-lg-9 col-md-8">
            <!-- Search and Sort Bar -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form action="{{ route('products.index') }}" method="GET" class="input-group">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search products..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('products.index') }}" method="GET" class="d-flex justify-content-end">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <div class="input-group" style="max-width: 250px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white">Sort By:</span>
                            </div>
                            <select name="sort" class="form-control" onchange="this.form.submit()">
                                <option value="">Default</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price (Low to High)</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price (High to Low)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Count -->
            <div class="mb-3">
                <p class="text-muted">
                    Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} 
                    of {{ $products->total() }} products
                </p>
            </div>

            <!-- Products Grid -->
            @if($products->count() > 0)
                <div class="row">
                    @foreach($products as $product)
                        <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                            <div class="card product-card h-100 border-0 shadow-sm">
                                <div class="product-image-wrapper position-relative">
                                    <a href="{{ route('products.show', $product->id) }}">
                                        @if($product->images && $product->images->first())
                                            <img src="{{ $product->images->first()->url }}" 
                                                 class="card-img-top product-image" 
                                                 alt="{{ $product->name }}"
                                                 style="height: 250px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('images/default-product.jpg') }}" 
                                                 class="card-img-top product-image" 
                                                 alt="{{ $product->name }}"
                                                 style="height: 250px; object-fit: cover;">
                                        @endif
                                    </a>

                                    <!-- Badges -->
                                    @if($product->stock_quantity == 0)
                                        <span class="badge badge-danger position-absolute" style="top: 10px; right: 10px;">
                                            Out of Stock
                                        </span>
                                    @elseif($product->stock_quantity < 10)
                                        <span class="badge badge-warning position-absolute" style="top: 10px; right: 10px;">
                                            Low Stock
                                        </span>
                                    @endif

                                    @if($product->sale_price && $product->sale_price < $product->price)
                                        <span class="badge badge-success position-absolute" style="top: 10px; left: 10px;">
                                            Sale
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <!-- Category -->
                                    @if($product->category)
                                        <small class="text-muted mb-2">
                                            <i class="fas fa-tag"></i> {{ $product->category->name }}
                                        </small>
                                    @endif

                                    <!-- Product Name with Wishlist Button -->
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title flex-grow-1 mb-0 pr-2">
                                            <a href="{{ route('products.show', $product->id) }}" class="text-dark text-decoration-none">
                                                {{ Str::limit($product->name, 45) }}
                                            </a>
                                        </h6>
                                        
                                        <!-- Wishlist Button (Right Side) -->
                                        @auth
                                            <button class="btn btn-link p-0 wishlist-toggle" 
                                                    data-product-id="{{ $product->id }}"
                                                    title="{{ in_array($product->id, $wishlistProductIds ?? []) ? 'Remove from wishlist' : 'Add to wishlist' }}"
                                                    style="font-size: 1.25rem; line-height: 1; min-width: auto; flex-shrink: 0;">
                                                <i class="fas fa-heart {{ in_array($product->id, $wishlistProductIds ?? []) ? 'text-danger' : 'text-muted' }}"></i>
                                            </button>
                                        @else
                                            <a href="{{ route('login') }}" 
                                               class="btn btn-link p-0" 
                                               title="Add to wishlist"
                                               style="font-size: 1.25rem; line-height: 1; min-width: auto; flex-shrink: 0;">
                                                <i class="far fa-heart text-muted"></i>
                                            </a>
                                        @endauth
                                    </div>

                                    <!-- Description -->
                                    <p class="card-text text-muted small mb-3">
                                        {{ Str::limit($product->description, 80) }}
                                    </p>

                                    <!-- Price and Actions -->
                                    <div class="mt-auto">
                                        <!-- Price -->
                                        <div class="mb-3">
                                            @if($product->sale_price && $product->sale_price < $product->price)
                                                <h5 class="text-primary mb-0">
                                                    ${{ number_format($product->sale_price, 2) }}
                                                    <small class="text-muted">
                                                        <del>${{ number_format($product->price, 2) }}</del>
                                                    </small>
                                                </h5>
                                            @else
                                                <h5 class="text-primary mb-0">
                                                    ${{ number_format($product->price, 2) }}
                                                </h5>
                                            @endif
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('products.show', $product->id) }}" 
                                               class="btn btn-outline-primary btn-sm flex-fill">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            @if($product->stock_quantity > 0)
                                                <form action="{{ route('cart.add.id', $product->id) }}" method="POST" class="flex-fill">
                                                    @csrf
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                                        <i class="fas fa-shopping-cart"></i> Add
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-secondary btn-sm flex-fill" disabled>
                                                    Out of Stock
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="row mt-4">
                    <div class="col-12 d-flex justify-content-center">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <!-- No Products Found -->
                <div class="col-12">
                    <div class="alert alert-info text-center py-5">
                        <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                        <h4>No Products Found</h4>
                        <p class="mb-3">We couldn't find any products matching your criteria.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary">
                            <i class="fas fa-redo mr-2"></i>Clear Filters
                        </a>
                    </div>
                </div>
            @endif
        </div>
        <!-- Products End -->
    </div>
</div>
<!-- Shop End -->

@push('scripts')
<script>
$(document).ready(function() {
    // Wishlist Toggle
    $('.wishlist-toggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const button = $(this);
        const productId = button.data('product-id');
        const icon = button.find('i');
        const isInWishlist = icon.hasClass('text-danger');
        
        @guest
            alert('Please login to add items to your wishlist');
            window.location.href = '{{ route("login") }}';
            return;
        @endguest
        
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
                    icon.removeClass('text-danger').addClass('text-muted');
                    button.attr('title', 'Add to wishlist');
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
                    icon.removeClass('text-muted').addClass('text-danger');
                    button.attr('title', 'Remove from wishlist');
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
            const newCount = currentCount + change;
            countElement.text(newCount);
            
            // Show/hide badge based on count
            if (newCount > 0) {
                countElement.removeClass('d-none');
            } else {
                countElement.addClass('d-none');
            }
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
    box-shadow: 0 10px 20px rgba(0,0,0,0.2) !important;
}

.product-image {
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.1);
}

.product-image-wrapper {
    overflow: hidden;
}

/* Wishlist Button Styles */
.wishlist-toggle {
    transition: all 0.2s ease;
    text-decoration: none !important;
}

.wishlist-toggle:hover {
    transform: scale(1.2);
}

.wishlist-toggle:focus {
    outline: none;
    box-shadow: none;
}

.wishlist-toggle i {
    transition: all 0.2s ease;
}

.wishlist-toggle:hover i {
    filter: brightness(0.9);
}

.d-flex.gap-2 {
    gap: 0.5rem;
}

.list-group-item.active {
    background-color: #D19C97;
    border-color: #D19C97;
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

.bg-primary {
    background-color: #D19C97 !important;
}

.breadcrumb {
    background: transparent;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
}

.card-header {
    border-bottom: 2px solid rgba(0,0,0,0.1);
}
</style>
@endsection