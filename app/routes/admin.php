<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| These routes are accessible only to authenticated users with 'admin' role.
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', fn() => 'Admin Panel')->name('panel');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::resource('users', UserController::class)->names('users');

        Route::resource('products', ProductController::class);
        Route::get('products/import', [ProductController::class, 'importForm'])->name('products.import.form');
        Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
        Route::get('products/export', [ProductController::class, 'export'])->name('products.export');

        Route::resource('categories', CategoryController::class);

        // ✅ Commandes (Orders)
        Route::resource('orders', OrderController::class);

        // ✅ Gestion des orders
        Route::resource('orders', OrderController::class);
        // ✅ Pending orders route
        Route::get('orders/pending', [OrderController::class, 'pending'])
            ->name('orders.pending');

        Route::put('users/{user}/roles-permissions', [UserController::class, 'updateRolesPermissions'])
            ->name('users.updateRolesPermissions');
    });