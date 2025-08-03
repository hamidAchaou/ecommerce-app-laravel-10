<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// ✅ Dashboard — accessible to all authenticated users
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// ✅ Profile routes (auth required)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ✅ Admin panel — only for admin role
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', fn () => 'Admin Panel')->name('admin.panel');
});

// ✅ Seller panel — only for seller role
Route::middleware(['auth', 'role:seller'])->group(function () {
    Route::get('/seller', fn () => 'Seller Panel')->name('seller.panel');
});

require __DIR__.'/auth.php';