<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// ============================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::get('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', [AuthController::class, 'login']);

// Products (Public browsing)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/new-arrivals', [ProductController::class, 'newArrivals']);
Route::get('/products/on-sale', [ProductController::class, 'onSale']);

// Categories (Public browsing)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// Reviews (Public viewing)
Route::get('/products/{productId}/reviews', [ReviewController::class, 'index']);

// Payment methods (Public viewing)
Route::get('/payment-methods', [PaymentController::class, 'paymentMethods']);


// ============================================
// AUTHENTICATED ROUTES (Requires Login)
// ============================================

Route::middleware(['auth:sanctum'])->group(function () {

    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);

    // User Profile Management
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::post('/profile/change-password', [UserController::class, 'changePassword']);
    Route::delete('/profile', [UserController::class, 'deleteAccount']);
    Route::get('/dashboard', [UserController::class, 'dashboard']);

    // Shopping Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);
    Route::delete('/wishlist', [WishlistController::class, 'clear']);

    // Addresses
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);
    Route::post('/addresses/{id}/set-default', [AddressController::class, 'setDefault']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::get('/orders/{id}/track', [OrderController::class, 'track']);

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
    Route::get('/my-reviews', [ReviewController::class, 'getUserReviews']);

    // Coupons (Validation)
    Route::post('/coupons/validate', [CouponController::class, 'validate']);

    // Payment
    Route::post('/payments/process', [PaymentController::class, 'processPayment']);
    Route::post('/payments/verify', [PaymentController::class, 'verifyPayment']);
});


// ============================================
// ADMIN ROUTES (Requires Admin Role)
// ============================================

Route::middleware(['auth:sanctum', 'api.rate_limit:100,1'])->group(function () {

    // Product Management
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Category Management
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Order Management
    Route::get('/admin/orders', [OrderController::class, 'adminIndex']);
    Route::put('/admin/orders/{id}/status', [OrderController::class, 'updateStatus']);

    // Review Management
    Route::post('/admin/reviews/{id}/approve', [ReviewController::class, 'approve']);

    // Coupon Management
    Route::get('/admin/coupons', [CouponController::class, 'index']);
    Route::post('/admin/coupons', [CouponController::class, 'store']);
    Route::put('/admin/coupons/{id}', [CouponController::class, 'update']);
    Route::delete('/admin/coupons/{id}', [CouponController::class, 'destroy']);

    // Payment Management
    Route::post('/admin/payments/{orderId}/refund', [PaymentController::class, 'refund']);
});

