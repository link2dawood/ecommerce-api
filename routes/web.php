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
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminCategoryController;

/*
|--------------------------------------------------------------------------
| PUBLIC FRONTEND ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('frontend.home');
})->name('home');

// Guest routes (only for non-authenticated users)
Route::middleware('guest')->group(function () {
    Route::get('/register', fn () => view('auth.register'))->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    
    Route::get('/login', fn () => view('auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'webLogin'])->name('login.post');
});

// Logout (for authenticated users)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Public pages
Route::get('/terms', fn () => view('terms'))->name('terms');
Route::get('/contact', fn () => view('frontend.contact'))->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Shop & Products (Public)
Route::get('/shop', [ProductController::class, 'index'])->name('shop.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/search', [ProductController::class, 'search'])->name('search');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');



/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    // User Dashboard
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    
    // Profile Management
    Route::prefix('profile')->group(function () {
        Route::get('/', [UserController::class, 'profile'])->name('profile');
        Route::get('/edit', [UserController::class, 'edit'])->name('profile.edit');
        Route::post('/update', [UserController::class, 'updateProfile'])->name('profile.update');
        Route::post('/change-password', [UserController::class, 'changePassword'])->name('profile.change-password');
        Route::delete('/delete', [UserController::class, 'deleteAccount'])->name('profile.delete');
    });
    
    // Wishlist
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('wishlist');
        Route::post('/', [WishlistController::class, 'store'])->name('wishlist.store');
        Route::delete('/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
        Route::delete('/', [WishlistController::class, 'clear'])->name('wishlist.clear');
    });
    
    // Shopping Cart
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('cart.index');
        Route::post('/add/{id}', [CartController::class, 'add'])->name('cart.add');
        Route::post('/update/{id}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
        Route::delete('/clear', [CartController::class, 'clear'])->name('cart.clear');
    });
    
    // Checkout & Orders
    Route::get('/checkout', fn () => view('frontend.checkout'))->name('checkout');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

// Admin Login (Separate)
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', fn () => view('admin.auth.login'))->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');
});

// Admin Panel
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // ✅ Remove extra "/admin" from here
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    
    // Products
    Route::prefix('products')->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('admin.products.index');
        Route::get('/create', [AdminProductController::class, 'create'])->name('admin.products.create');
        Route::post('/', [AdminProductController::class, 'store'])->name('admin.products.store');
        Route::get('/{id}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
        Route::put('/{id}', [AdminProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/{id}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');
    });
    
    // Categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
        Route::get('/create', [AdminCategoryController::class, 'create'])->name('admin.categories.create');
        Route::post('/', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
        Route::get('/{id}/edit', [AdminCategoryController::class, 'edit'])->name('admin.categories.edit');
        Route::put('/{id}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/{id}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');
    });
    
    // Orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
        Route::put('/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.status');
    });
    
    // Contacts
    Route::prefix('contacts')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('admin.contacts.index');
        Route::get('/{id}', [ContactController::class, 'show'])->name('admin.contacts.show');
        Route::delete('/{id}', [ContactController::class, 'destroy'])->name('admin.contacts.destroy');
    });
    
    Route::get('/newsletters', [NewsletterController::class, 'index'])->name('admin.newsletters.index');
    Route::get('/settings', fn () => view('admin.settings'))->name('admin.settings');
});