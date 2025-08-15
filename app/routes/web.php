<?php

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/categories/{category:slug}/products', [ProductController::class, 'categoryProducts'])
    ->name('categories.products');

// Authenticated user profile
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ✅ Seller panel
Route::middleware(['auth', 'role:seller'])->group(function () {
    Route::get('/seller', fn () => 'Seller Panel')->name('seller.panel');
});

// ✅ Admin routes
require __DIR__ . '/admin.php';

// ✅ Auth routes
require __DIR__ . '/auth.php';