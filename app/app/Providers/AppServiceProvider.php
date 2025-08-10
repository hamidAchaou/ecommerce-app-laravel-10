<?php

namespace App\Providers;

use App\Services\Contracts\ProductImageServiceInterface;
use App\Services\ProductImageService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductImageServiceInterface::class, ProductImageService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(123);
    }
}