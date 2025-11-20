<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ContactController;     
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\SettingsController;


/*
|--------------------------------------------------------------------------
| PUBLIC FRONTEND ROUTES
|--------------------------------------------------------------------------
*/

// ========== HOME PAGE ==========
Route::get('/', [HomeController::class, 'index'])->name('home');

// ========== AUTHENTICATION ROUTES (GUEST ONLY) ==========
Route::middleware('guest')->group(function () {
    // Register
    Route::get('/register', fn () => view('auth.register'))->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    
    // Login
    Route::get('/login', fn () => view('auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'webLogin'])->name('login.post');
});

// ========== LOGOUT (AUTHENTICATED USERS) ==========
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ========== SHOP & PRODUCTS ROUTES (PUBLIC) ==========
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/search', [ShopController::class, 'search'])->name('shop.search');
Route::get('/shop/product/{slug}', [ShopController::class, 'show'])->name('shop.detail');

// Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/featured', [ProductController::class, 'featured'])->name('products.featured');
Route::get('/products/new-arrivals', [ProductController::class, 'newArrivals'])->name('products.new-arrivals');
Route::get('/products/on-sale', [ProductController::class, 'onSale'])->name('products.on-sale');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');

// ========== CATEGORY ROUTES (PUBLIC) ==========
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');

// ========== CART ROUTES (PUBLIC) ==========
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/add/{id}', [CartController::class, 'add'])->name('add.id');
    Route::patch('/update/{id}', [CartController::class, 'update'])->name('update');
    Route::post('/update/{id}', [CartController::class, 'update'])->name('update.post');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
});

// ========== CONTACT ROUTES (PUBLIC) ==========
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// ========== NEWSLETTER SUBSCRIPTION (PUBLIC) ==========
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// ========== STATIC PAGES ==========
Route::get('/terms', fn () => view('terms'))->name('terms');

// Add to web.php (outside middleware groups)
Route::post('/stripe/webhook', [CheckoutController::class, 'webhook'])->name('stripe.webhook');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    // ========== USER DASHBOARD ==========
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    
    // ========== PROFILE ROUTES ==========
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::post('/update', [UserController::class, 'updateProfile'])->name('update.post');
        Route::put('/password', [ProfileController::class, 'password'])->name('password');
        Route::post('/change-password', [UserController::class, 'changePassword'])->name('change-password');
        Route::delete('/delete', [UserController::class, 'deleteAccount'])->name('delete');
    });
    
    // ========== WISHLIST ROUTES ==========
   Route::prefix('wishlist')->middleware('auth')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/add/{product}', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/remove/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::delete('/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');
    Route::delete('/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
});
    
    // ========== CHECKOUT ROUTES ==========
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
        Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
    });
    
    // ========== ORDER ROUTES ==========
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{id}/track', [OrderController::class, 'track'])->name('orders.track');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

// ========== ADMIN LOGIN (GUEST ONLY) ==========
Route::middleware('guest')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', fn () => view('admin.auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'adminLogin'])->name('login.post');
});

// ========== ADMIN PANEL ROUTES ==========
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // ========== PRODUCTS MANAGEMENT ==========
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('index');
        Route::get('/create', [AdminProductController::class, 'create'])->name('create');
        Route::post('/', [AdminProductController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminProductController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminProductController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminProductController::class, 'destroy'])->name('destroy');
    });
    
    // ========== CATEGORIES MANAGEMENT ==========
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index'])->name('index');
        Route::get('/create', [AdminCategoryController::class, 'create'])->name('create');
        Route::post('/', [AdminCategoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminCategoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminCategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminCategoryController::class, 'destroy'])->name('destroy');
    });
    
    // ========== ORDERS MANAGEMENT ==========
    Route::resource('orders', AdminOrderController::class);
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('orders/{order}/payment-status', [AdminOrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
    
    // ========== CONTACTS MANAGEMENT ==========
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'adminIndex'])->name('index');
        Route::get('/{id}', [ContactController::class, 'show'])->name('show');
        Route::put('/{id}/toggle-read', [ContactController::class, 'toggleRead'])->name('toggle-read');
        Route::post('/mark-as-read', [ContactController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/{id}/reply', [ContactController::class, 'reply'])->name('reply');
        Route::delete('/{id}', [ContactController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [ContactController::class, 'bulkDelete'])->name('bulk-delete');
    });
    Route::get('/contacts-statistics', [ContactController::class, 'statistics'])->name('contacts.statistics');
    
    // ========== COUPONS MANAGEMENT ==========
    Route::prefix('coupons')->name('coupons.')->group(function () {
        Route::get('/', [AdminCouponController::class, 'index'])->name('index');
        Route::get('/create', [AdminCouponController::class, 'create'])->name('create');
        Route::post('/', [AdminCouponController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminCouponController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminCouponController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminCouponController::class, 'destroy'])->name('destroy');
    });
    
    // ========== REVIEWS MANAGEMENT ==========
    // Uncomment when AdminReviewController is created
    /*
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [AdminReviewController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminReviewController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [AdminReviewController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [AdminReviewController::class, 'reject'])->name('reject');
        Route::delete('/{id}', [AdminReviewController::class, 'destroy'])->name('destroy');
    });
    */
    
    // ========== PAYMENTS MANAGEMENT ==========
    // Uncomment when AdminPaymentController is created
    /*
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [AdminPaymentController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminPaymentController::class, 'show'])->name('show');
        Route::post('/{orderId}/refund', [AdminPaymentController::class, 'refund'])->name('refund');
    });
    */
       
    // ========== NEWSLETTERS MANAGEMENT ==========
    Route::get('/newsletters', [NewsletterController::class, 'index'])->name('newsletters.index');
    Route::delete('/newsletters/{id}', [NewsletterController::class, 'destroy'])->name('newsletters.destroy');
    Route::delete('/newsletters-bulk-delete', [NewsletterController::class, 'bulkDelete'])->name('newsletters.bulkDelete');
    Route::post('/newsletters/export', [NewsletterController::class, 'export'])->name('newsletters.export');
    
    // ========== SETTINGS ==========
    // Uncomment when SettingsController is created
    
   Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/clear-cache', [SettingsController::class, 'clearCache'])->name('settings.clear-cache');
    
    
    // ========== ADMIN LOGOUT ==========
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});