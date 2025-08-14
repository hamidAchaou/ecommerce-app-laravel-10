<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Repositories\admin\ProductRepository;

class DashboardController extends Controller
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        $categories = Category::withCount('products')->get();
    
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::sum('total_amount'),
        ];
    
        $monthlySales = Order::selectRaw('MONTHNAME(created_at) as month, SUM(total_amount) as total')
        ->groupBy('month')
        ->orderByRaw('MIN(created_at)')
        ->pluck('total', 'month')
        ->toArray(); // <-- convert here    
    
        $menuGroups = config('admin.menu'); // Move menu to config/admin.php
    
        return view('admin.dashboard.index', compact('categories', 'stats', 'monthlySales', 'menuGroups'));
    }
    
}