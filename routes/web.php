<?php

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
| Web Routes
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', function () {
    return view('welcome');
})->name('home');

// =============================
// Authentication Pages
// =============================
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dummy page to fix your error
Route::get('/status/set', function () {
    return view('status.set'); 
})->name('status.set');

// =============================
// Products
// =============================
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/featured', [ProductController::class, 'featured'])->name('products.featured');
Route::get('/products/new-arrivals', [ProductController::class, 'newArrivals'])->name('products.newArrivals');
Route::get('/products/on-sale', [ProductController::class, 'onSale'])->name('products.onSale');

// =============================
// Categories
// =============================
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');

// =============================
// User Profile & Dashboard
// =============================
Route::get('/profile', [UserController::class, 'profile'])->name('profile');
Route::put('/profile', [UserController::class, 'updateProfile']);
Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

// =============================
// Cart
// =============================
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// =============================
// Wishlist
// =============================
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');

// =============================
// Orders
// =============================
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');

// =============================
// Reviews
// =============================
Route::get('/products/{productId}/reviews', [ReviewController::class, 'index'])->name('reviews.index');
// Feedback Page
Route::get('/feedback', function () {
    return view('feedback'); // create resources/views/feedback.blade.php
})->name('feedback');
// Settings Page
Route::get('/settings', function () {
    return view('settings'); // create resources/views/settings.blade.php
})->name('settings');

// =============================
// Coupons
// =============================
Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');

// =============================
// Payment
// =============================
Route::get('/payment-methods', [PaymentController::class, 'paymentMethods'])->name('payments.methods');
