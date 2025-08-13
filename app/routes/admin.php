<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', fn() => 'Admin Panel')->name('panel');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::resource('users', UserController::class)->names('users');

        // Image management routes - MUST come BEFORE resource routes
        Route::delete('products/{product}/images/{image}', [ProductImageController::class, 'destroy'])
        ->name('products.images.destroy');
        Route::post('products/{product}/images/{image}/set-main', [ProductImageController::class, 'setMain'])
            ->name('products.images.setMain');
        
        // Product routes
        Route::resource('products', ProductController::class);
        Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
        Route::get('products/export', [ProductController::class, 'export'])->name('products.export');

        Route::resource('categories', CategoryController::class);
        Route::resource('orders', OrderController::class);
        
        Route::get('orders/pending', [OrderController::class, 'pending'])
            ->name('orders.pending');

        Route::put('users/{user}/roles-permissions', [UserController::class, 'updateRolesPermissions'])
            ->name('users.updateRolesPermissions');
    });