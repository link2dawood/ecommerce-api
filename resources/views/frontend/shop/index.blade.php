@extends('frontend.layouts.app')
@section('title', 'Shop')

@section('content')
<div class="container-fluid pt-5">
    <div class="row px-xl-5">
        <div class="col-lg-12">
            <h2 class="mb-4">Shop</h2>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                @forelse($products as $product)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card product-card h-100 border-0 shadow-sm">
                            <div class="product-image-wrapper position-relative">
                                @php
                                    $primaryImage = $product->images->where('is_primary', true)->first() 
                                                    ?? $product->images->first();
                                    $imageUrl = $primaryImage ? $primaryImage->url : asset('images/default-product.jpg');
                                    $altText = $primaryImage->alt_text ?? $product->name;
                                @endphp
                                
                                <img src="{{ $imageUrl }}" 
                                     class="card-img-top product-image" 
                                     alt="{{ $altText }}"
                                     style="height: 250px; object-fit: cover;"
                                     onerror="this.onerror=null; this.src='{{ asset('images/default-product.jpg') }}';">
                                
                                @if($product->stock_quantity == 0)
                                    <span class="badge badge-danger position-absolute" style="top: 10px; right: 10px;">Out of Stock</span>
                                @elseif($product->stock_quantity < 10)
                                    <span class="badge badge-warning position-absolute" style="top: 10px; right: 10px;">Low Stock</span>
                                @endif
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-truncate" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </h5>
                                
                                @if($product->description)
                                    <p class="card-text text-muted small" style="height: 40px; overflow: hidden;">
                                        {{ Str::limit($product->description, 80) }}
                                    </p>
                                @endif
                                
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="text-primary mb-0">${{ number_format($product->price, 2) }}</h4>
                                        <small class="text-muted">Stock: {{ $product->stock_quantity }}</small>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('products.show', $product->id) }}" 
                                           class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        
                                        @if($product->stock_quantity > 0)
                                            <form action="{{ route('cart.add.id', $product->id) }}" method="POST" class="flex-fill add-to-cart-form">
                                                @csrf
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-secondary btn-sm flex-fill" disabled>
                                                <i class="fas fa-ban"></i> Out of Stock
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card border-0">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                                <h4>No products available</h4>
                                <p class="text-muted">Check back later for new products!</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="row mt-4">
                    <div class="col-12 d-flex justify-content-center">
                        {{ $products->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
}

.product-image {
    transition: transform 0.3s ease;
    background-color: #f8f9fa;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-image-wrapper {
    overflow: hidden;
    background-color: #f8f9fa;
}

.d-flex.gap-2 {
    gap: 0.5rem;
}

/* Fix for flex-fill buttons */
.flex-fill {
    flex: 1 1 0%;
}

/* Ensure buttons are same height */
.d-flex.gap-2 > a,
.d-flex.gap-2 > form,
.d-flex.gap-2 > button {
    display: flex;
    align-items: center;
}

.d-flex.gap-2 .btn {
    white-space: nowrap;
}

/* Badge styles */
.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
    font-weight: 600;
}

/* Card title ellipsis */
.card-title {
    min-height: 1.5rem;
}

/* Price styling */
.text-primary {
    font-weight: 600;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .product-image {
        height: 200px !important;
    }
    
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .d-flex.gap-2 > a,
    .d-flex.gap-2 > form {
        width: 100%;
    }
}

/* Loading state for images */
.product-image {
    min-height: 250px;
}

/* Alert auto-dismiss animation */
@keyframes slideOut {
    from {
        opacity: 1;
        transform: translateY(0);
    }
    to {
        opacity: 0;
        transform: translateY(-20px);
    }
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);

    // Add to cart with loading state
    $('.add-to-cart-form').on('submit', function() {
        const $btn = $(this).find('button[type="submit"]');
        const originalHtml = $btn.html();
        
        $btn.prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin"></i> Adding...');
        
        // Re-enable after 2 seconds (in case of redirect delay)
        setTimeout(function() {
            $btn.prop('disabled', false).html(originalHtml);
        }, 2000);
    });

    // Lazy loading for images (optional enhancement)
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        });

        document.querySelectorAll('.product-image').forEach(img => {
            imageObserver.observe(img);
        });
    }
});
</script>
@endpush
@endsection