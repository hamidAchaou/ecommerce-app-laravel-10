<?php

namespace App\Providers;

use App\Models\ProductImage;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    
        // // Bindings first
        // Route::bind('product', function ($value) {
        //     return \App\Models\Product::findOrFail($value);
        // });
    
        Route::bind('image', function ($value, $route) {
            $productId = $route->parameter('product');
            return ProductImage::where('id', $value)
                ->where('product_id', $productId->id ?? $productId)
                ->firstOrFail();
        });
    
        // Then register routes
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
    
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
    
}