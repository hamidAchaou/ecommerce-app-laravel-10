<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;

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
        Route::get('/', fn () => 'Admin Panel')->name('panel');
        
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::resource('users', UserController::class)->names('users');
        Route::resource('products', ProductController::class);
        // Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggleStatus');
        Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
        Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
        // Route::put('products/{product}/stock', [ProductController::class, 'updateStock'])->name('products.updateStock');
        Route::resource('categories', CategoryController::class);

        Route::put('users/{user}/roles-permissions', [UserController::class, 'updateRolesPermissions'])
            ->name('users.updateRolesPermissions');
    });