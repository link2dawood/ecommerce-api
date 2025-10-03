<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\NewsletterController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing / Welcome
Route::get('/', function () {
    return view('welcome');
});

// Auth pages
Route::get('/register', fn () => view('auth.register'))->name('register');
Route::get('/login', fn () => view('auth.login'))->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Static pages
Route::get('/status/set', fn () => view('status.set'))->name('status.set');
Route::get('/terms', fn () => view('terms'))->name('terms');
Route::get('/feedback', fn () => view('feedback'))->name('feedback');
Route::get('/settings', fn () => view('settings'))->name('settings');

// Dashboard & Home
Route::get('/home', fn () => view('home'))->name('home');
Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

// Profile pages
Route::prefix('profile')->group(function () {
    Route::get('/', [UserController::class, 'profile'])->name('profile');
    Route::get('/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::post('/update', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('profile.change-password');
    Route::delete('/delete', [UserController::class, 'deleteAccount'])->name('profile.delete');
});

// Search
Route::get('/search', [ProductController::class, 'search'])->name('search');

// Wishlist
Route::prefix('wishlist')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::delete('/', [WishlistController::class, 'clear'])->name('wishlist.clear');
});


// Cart Routes
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('cart.clear');
});

// Shop route (all products)
Route::get('/shop', [ProductController::class, 'index'])->name('shop.index');

// Contact Page
Route::get('/contact', function () {
    return view('contact'); // resources/views/contact.blade.php
})->name('contact');

// Contact form submission
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Checkout page
Route::get('/checkout', function () {
    return view('checkout'); // resources/views/checkout.blade.php
})->name('checkout');

// Checkout process (store order)
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');


// Only authenticated users can access
Route::middleware(['auth:sanctum'])->group(function () {

    // Orders list
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');

    // Single order detail
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');

});


Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->name('newsletter.subscribe');