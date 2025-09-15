<?php

use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\StripeWebhookController;
use App\Http\Controllers\Frontend\WishlistController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

/*
|--------------------------------------------------------------------------
| Web Routes (Frontend)
|--------------------------------------------------------------------------
*/

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

// Public pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'frontend.about')->name('about');
Route::view('/contact', 'frontend.contact')->name('contact');

// Products
Route::resource('products', ProductController::class);

// Categories
Route::resource('categories', CategoryController::class);

// ⚠️ IMPORTANT: Stripe webhook MUST be outside middleware groups
// This route should be accessible without CSRF protection
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->name('stripe.webhook');

// Authenticated user profile
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Change password form
Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', [ProfileController::class, 'changePasswordForm'])->name('password.change');
});

// Seller panel
Route::middleware(['auth', 'role:seller'])->group(function () {
    Route::get('/seller', fn() => 'Seller Panel')->name('seller.panel');
});

// Cart accessible for both guests and authenticated users
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
Route::delete('/cart/clear/all', [CartController::class, 'clearAll'])
    ->name('cart.clearAll');

// Contact
Route::post('/contact', [HomeController::class, 'contact'])->name('contact.submit');
// About
Route::get('/about', [HomeController::class, 'about'])->name('about');

// Checkout only for authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/stripe', [CheckoutController::class, 'stripeCheckout'])->name('checkout.stripe');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    // Display payment page
    Route::get('/payment', [CheckoutController::class, 'payment'])->name('payment.index');
    // Process payment
    Route::post('/payment', [CheckoutController::class, 'processPayment'])->name('payment.process');
});

// Orders for authenticated users
Route::middleware('auth')->prefix('orders')->name('frontend.orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index'); // My Orders list
    Route::get('/{order}', [OrderController::class, 'show'])->name('show'); // Order Details
});

// Wishlist for authenticated users
Route::middleware(['auth'])->prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::post('/{product}', [WishlistController::class, 'store']);
    Route::delete('/{product}', [WishlistController::class, 'destroy']);
});

// Debug routes - remove in production
Route::get('/debug/webhook-test', function () {
    $webhookUrl = route('stripe.webhook');

    return response()->json([
        'webhook_url' => $webhookUrl,
        'stripe_config' => [
            'key_exists' => !empty(config('services.stripe.key')),
            'secret_exists' => !empty(config('services.stripe.secret')),
            'webhook_secret_exists' => !empty(config('services.stripe.webhook_secret')),
        ],
        'database_tables' => [
            'orders' => DB::table('orders')->count(),
            'order_items' => DB::table('order_items')->count(),
            'payments' => DB::table('payments')->count(),
            'clients' => DB::table('clients')->count(),
        ],
    ]);
});

Route::post('/debug/checkout', function (Request $request) {
    return response()->json([
        'auth_check' => auth()->check(),
        'user_id' => auth()->id(),
        'request_data' => $request->all(),
        'stripe_config' => [
            'key_exists' => !empty(config('services.stripe.key')),
            'secret_exists' => !empty(config('services.stripe.secret')),
        ]
    ]);
});

// Admin routes
require __DIR__ . '/admin.php';

// Auth routes
require __DIR__ . '/auth.php';