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

/*
|--------------------------------------------------------------------------
| PUBLIC FRONTEND ROUTES
|--------------------------------------------------------------------------
*/

// Home Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Guest Routes (Login & Register)
Route::middleware('guest')->group(function () {
    Route::get('/register', fn () => view('auth.register'))->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    
    Route::get('/login', fn () => view('auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'webLogin'])->name('login.post');
});

// Logout Route (for authenticated users)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Shop Routes (Public)
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');

    Route::get('/search', [ShopController::class, 'search'])->name('search');
    Route::get('/product/{slug}', [ShopController::class, 'show'])->name('detail');
});

// Category Routes (Public)
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');

// Frontend Product Routes
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/featured', [ProductController::class, 'featured'])->name('products.featured');
Route::get('/products/new-arrivals', [ProductController::class, 'newArrivals'])->name('products.new-arrivals');
Route::get('/products/on-sale', [ProductController::class, 'onSale'])->name('products.on-sale');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');

// Cart Routes (Public - Can add to cart without login)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/add/{id}', [CartController::class, 'add'])->name('add.id');
    Route::patch('/update/{id}', [CartController::class, 'update'])->name('update');
    Route::post('/update/{id}', [CartController::class, 'update'])->name('update.post');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
});

// Contact Routes (Public)
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Newsletter Subscription (Public)
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Public Pages
Route::get('/terms', fn () => view('terms'))->name('terms');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    // User Dashboard
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    
    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
       Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');

        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::post('/update', [UserController::class, 'updateProfile'])->name('update.post');
        Route::put('/password', [ProfileController::class, 'password'])->name('password');
        Route::post('/change-password', [UserController::class, 'changePassword'])->name('change-password');
        Route::delete('/delete', [UserController::class, 'deleteAccount'])->name('delete');
    });
    
    // Wishlist Routes
     Route::prefix('wishlist')->name('wishlist.')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('index');
        Route::post('/', [WishlistController::class, 'store'])->name('store');
        Route::post('/add/{product}', [WishlistController::class, 'add'])->name('add');
        Route::delete('/{id}', [WishlistController::class, 'destroy'])->name('destroy');
        Route::delete('/remove/{product}', [WishlistController::class, 'remove'])->name('remove');
        Route::delete('/', [WishlistController::class, 'clear'])->name('clear');
    });
    
    // Checkout Routes
Route::prefix('checkout')->name('checkout.')->middleware('auth')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
});
    
    // Order Routes
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
// Admin Login (Separate from User Login)
Route::middleware('guest')->prefix('admin')->group(function () {
    Route::get('/login', fn () => view('admin.auth.login'))->name('admin.login');
    Route::post('/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');
});

// Admin Panel Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Products Management
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('index');
        Route::get('/create', [AdminProductController::class, 'create'])->name('create');
        Route::post('/', [AdminProductController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminProductController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminProductController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminProductController::class, 'destroy'])->name('destroy');
    });
    
    // Categories Management
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index'])->name('index');
        Route::get('/create', [AdminCategoryController::class, 'create'])->name('create');
        Route::post('/', [AdminCategoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminCategoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminCategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminCategoryController::class, 'destroy'])->name('destroy');
    });
    
    // Orders Management
    Route::resource('orders', AdminOrderController::class);
    // Additional routes for status updates
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
        ->name('orders.update-status');
    Route::patch('orders/{order}/payment-status', [AdminOrderController::class, 'updatePaymentStatus'])
        ->name('orders.update-payment-status');
    
    // 📩 Contacts Management (Admin)
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ContactController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Admin\ContactController::class, 'show'])->name('show');
        Route::delete('/{id}', [App\Http\Controllers\Admin\ContactController::class, 'destroy'])->name('destroy');
        // 🧹 Added Routes
        Route::delete('/bulk-delete', [App\Http\Controllers\Admin\ContactController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/{id}/mark-as-read', [App\Http\Controllers\Admin\ContactController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/{id}/mark-as-unread', [App\Http\Controllers\Admin\ContactController::class, 'markAsUnread'])->name('mark-as-unread');
    });
    
    // 🎟️ Coupons Management
    Route::prefix('coupons')->name('coupons.')->group(function () {
        Route::get('/', [AdminCouponController::class, 'index'])->name('index');
        Route::get('/create', [AdminCouponController::class, 'create'])->name('create');
        Route::post('/', [AdminCouponController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminCouponController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminCouponController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminCouponController::class, 'destroy'])->name('destroy');
    });
    
    // ⭐ Reviews Management
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [AdminReviewController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminReviewController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [AdminReviewController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [AdminReviewController::class, 'reject'])->name('reject');
        Route::delete('/{id}', [AdminReviewController::class, 'destroy'])->name('destroy');
    });
    
    // 💳 Payments Management
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [AdminPaymentController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminPaymentController::class, 'show'])->name('show');
        Route::post('/{orderId}/refund', [AdminPaymentController::class, 'refund'])->name('refund');
    });
       
      // 📧 Newsletters Management
    Route::prefix('newsletters')->name('newsletters.')->group(function () {
        Route::get('/', [NewsletterController::class, 'index'])->name('index');
        Route::delete('/{id}', [NewsletterController::class, 'destroy'])->name('destroy');
        Route::delete('/bulk-delete', [NewsletterController::class, 'bulkDelete'])->name('bulkDelete'); // Changed here
        Route::post('/export', [NewsletterController::class, 'export'])->name('export');
    });
    
    // ⚙️ Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/clear-cache', [SettingsController::class, 'clearCache'])->name('settings.clear-cache');
    
    // 🚪 Admin Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});