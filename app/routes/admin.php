<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductImageController;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard - require 'view dashboard' permission
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view dashboard')
        ->name('dashboard');

    // Roles & Permissions (admin only)
    Route::resource('roles', RoleController::class)->middleware('permission:manage users');
    Route::resource('permissions', PermissionController::class)->middleware('permission:manage users');

    // Users
    Route::resource('users', UserController::class)
        ->middleware('permission:manage users')
        ->names('users');

    // Products
    Route::resource('products', ProductController::class)->middleware('permission:manage products');
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import')->middleware('permission:manage products');
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export')->middleware('permission:manage products');

    // Product Images
    Route::delete('products/{product}/images/{image}', [ProductImageController::class, 'destroy'])
        ->name('products.images.destroy')->middleware('permission:manage products');

    Route::post('products/{product}/images/{image}/set-main', [ProductImageController::class, 'setMain'])
        ->name('products.images.setMain')->middleware('permission:manage products');

    // Categories
    Route::resource('categories', CategoryController::class)->middleware('permission:manage products');

    // Orders
    Route::resource('orders', OrderController::class)->middleware('permission:manage orders');
    Route::get('orders/pending', [OrderController::class, 'pending'])->name('orders.pending')->middleware('permission:manage orders');

    // User roles/permissions
    Route::put('users/{user}/roles-permissions', [UserController::class, 'updateRolesPermissions'])
        ->name('users.updateRolesPermissions')
        ->middleware('permission:manage users');
});