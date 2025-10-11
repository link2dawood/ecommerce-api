@extends('frontend.layouts.app')

@section('title', 'My Wishlist')

@section('content')
<div class="container py-5">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">My Wishlist</h2>
            <p class="text-muted mb-0">
                @if($wishlist->count() > 0)
                    You have {{ $wishlist->count() }} {{ Str::plural('item', $wishlist->count()) }} in your wishlist
                @else
                    Your wishlist is empty
                @endif
            </p>
        </div>
        @if($wishlist->count() > 0)
            <button class="btn btn-outline-danger" id="clearWishlist">
                <i class="fas fa-trash-alt me-2"></i>Clear All
            </button>
        @endif
    </div>

    @if($wishlist->count() > 0)
        <div class="row g-4" id="wishlistContainer">
            @foreach($wishlist as $item)
                <div class="col-lg-3 col-md-4 col-sm-6 wishlist-item" data-id="{{ $item->id }}">
                    <div class="card h-100 shadow-sm hover-shadow-lg transition">
                        <!-- Product Image -->
                        <div class="position-relative">
                            @if($item->product->images->first())
                                <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                     class="card-img-top" 
                                     alt="{{ $item->product->name }}"
                                     style="height: 250px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 250px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                            
                            <!-- Remove Button (Top Right) -->
                            <button class="btn btn-light btn-sm position-absolute top-0 end-0 m-2 rounded-circle remove-wishlist" 
                                    data-id="{{ $item->id }}"
                                    style="width: 35px; height: 35px; padding: 0;">
                                <i class="fas fa-times text-danger"></i>
                            </button>

                            <!-- Stock Badge -->
                            @if($item->product->stock > 0)
                                <span class="badge bg-success position-absolute bottom-0 start-0 m-2">In Stock</span>
                            @else
                                <span class="badge bg-danger position-absolute bottom-0 start-0 m-2">Out of Stock</span>
                            @endif
                        </div>
                        
                        <!-- Product Details -->
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-truncate mb-2" title="{{ $item->product->name }}">
                                {{ $item->product->name }}
                            </h5>
                            
                            <p class="card-text text-muted small mb-3" style="height: 40px; overflow: hidden;">
                                {{ Str::limit($item->product->description, 80) }}
                            </p>
                            
                            <!-- Price and Category -->
                            <div class="mb-3">
                                <h4 class="text-primary mb-1">
                                    ${{ number_format($item->product->price, 2) }}
                                </h4>
                                @if($item->product->category)
                                    <span class="badge bg-light text-dark">{{ $item->product->category->name }}</span>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-auto">
                                <div class="d-grid gap-2">
                                    @if($item->product->stock > 0)
                                        <button class="btn btn-primary btn-sm add-to-cart" 
                                                data-product-id="{{ $item->product->id }}">
                                            <i class="fas fa-shopping-cart me-1"></i>Add to Cart
                                        </button>
                                    @endif
                                    <a href="{{ route('products.show', $item->product->id) }}" 
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-heart fa-5x text-muted opacity-50"></i>
            </div>
            <h3 class="mb-3">Your Wishlist is Empty</h3>
            <p class="text-muted mb-4">
                Save your favorite items for later by adding them to your wishlist.
            </p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="fas fa-shopping-bag me-2"></i>Start Shopping
            </a>
        </div>
    @endif
</div>

@push('styles')
<style>
    .hover-shadow-lg {
        transition: all 0.3s ease;
    }
    
    .hover-shadow-lg:hover {
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
        transform: translateY(-5px);
    }
    
    .transition {
        transition: all 0.3s ease;
    }
    
    .remove-wishlist {
        opacity: 0.8;
        transition: all 0.2s ease;
    }
    
    .remove-wishlist:hover {
        opacity: 1;
        transform: scale(1.1);
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Remove single item from wishlist
    $('.remove-wishlist').on('click', function(e) {
        e.preventDefault();
        const itemId = $(this).data('id');
        const card = $(this).closest('.wishlist-item');
        
        if(confirm('Remove this item from your wishlist?')) {
            $.ajax({
                url: `/wishlist/${itemId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    card.css('opacity', '0.5');
                },
                success: function(response) {
                    card.fadeOut(400, function() {
                        $(this).remove();
                        
                        // Check if wishlist is empty
                        if($('.wishlist-item').length === 0) {
                            location.reload();
                        } else {
                            // Update count
                            updateWishlistCount();
                        }
                    });
                    
                    // Show toast notification (if you have toast)
                    showNotification('success', response.message);
                },
                error: function(xhr) {
                    card.css('opacity', '1');
                    showNotification('error', 'Error removing item from wishlist');
                }
            });
        }
    });
    
    // Clear all items from wishlist
    $('#clearWishlist').on('click', function() {
        if(confirm('Are you sure you want to clear your entire wishlist?')) {
            $.ajax({
                url: '/wishlist/clear',
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('#wishlistContainer').css('opacity', '0.5');
                },
                success: function(response) {
                    showNotification('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                },
                error: function(xhr) {
                    $('#wishlistContainer').css('opacity', '1');
                    showNotification('error', 'Error clearing wishlist');
                }
            });
        }
    });
    
    // Add to cart from wishlist
    $('.add-to-cart').on('click', function() {
        const productId = $(this).data('product-id');
        const button = $(this);
        
        $.ajax({
            url: '/cart/add',
            type: 'POST',
            data: {
                product_id: productId,
                quantity: 1
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Adding...');
            },
            success: function(response) {
                button.html('<i class="fas fa-check me-1"></i>Added!');
                showNotification('success', 'Product added to cart');
                
                // Update cart count if you have it
                updateCartCount();
                
                setTimeout(function() {
                    button.prop('disabled', false).html('<i class="fas fa-shopping-cart me-1"></i>Add to Cart');
                }, 2000);
            },
            error: function(xhr) {
                button.prop('disabled', false).html('<i class="fas fa-shopping-cart me-1"></i>Add to Cart');
                const message = xhr.responseJSON?.message || 'Error adding to cart';
                showNotification('error', message);
            }
        });
    });
    
    // Helper function to show notifications
    function showNotification(type, message) {
        // If you're using Bootstrap Toast
        if(typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            // Create toast element dynamically
            const toastHtml = `
                <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            $('body').append(toastHtml);
            const toastElement = $('.toast').last()[0];
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
            
            // Remove after hidden
            $(toastElement).on('hidden.bs.toast', function() {
                $(this).remove();
            });
        } else {
            // Fallback to alert
            alert(message);
        }
    }
    
    // Helper function to update wishlist count in header
    function updateWishlistCount() {
        const count = $('.wishlist-item').length;
        $('.wishlist-count').text(count);
    }
    
    // Helper function to update cart count in header
    function updateCartCount() {
        $.ajax({
            url: '/cart/count',
            type: 'GET',
            success: function(response) {
                $('.cart-count').text(response.count);
            }
        });
    }
});
</script>
@endpush
@endsection