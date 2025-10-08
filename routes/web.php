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
    Route::get('/search', [ShopController::class, 'search'])->name('search');
    Route::get('/product/{slug}', [ShopController::class, 'show'])->name('detail');
});

// Category Routes (Public)
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');

// Product Routes (Public - Alternative)
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

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
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
        Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
    });
    
    // Order Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show.order');
    });
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
    
    // Contacts Management
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::get('/{id}', [ContactController::class, 'show'])->name('show');
        Route::delete('/{id}', [ContactController::class, 'destroy'])->name('destroy');
    });
    
    // Newsletters Management
    Route::get('/newsletters', [NewsletterController::class, 'index'])->name('newsletters.index');
    
    // Settings
    Route::get('/settings', fn () => view('admin.settings'))->name('settings');
});