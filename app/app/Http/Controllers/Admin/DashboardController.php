<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\ProductRepository;
use App\Repositories\Admin\CategoryRepository;
use App\Repositories\OrderRepository;

class DashboardController extends Controller
{
    protected ProductRepository $productRepo;
    protected CategoryRepository $categoryRepo;
    protected OrderRepository $orderRepo;

    public function __construct(
        ProductRepository $productRepo,
        CategoryRepository $categoryRepo,
        OrderRepository $orderRepo
    ) {
        $this->productRepo = $productRepo;
        $this->categoryRepo = $categoryRepo;
        $this->orderRepo = $orderRepo;
    }

    public function index()
    {
        // Homepage categories (limit handled in repository)
        $categories = $this->categoryRepo->getHomepageCategories();

        // Dashboard statistics
        $stats = [
            'total_products'   => $this->productRepo->countAll(),
            'total_categories' => $this->categoryRepo->countAll(),
            'total_orders'     => $this->orderRepo->countAll(),
            'total_revenue'    => $this->orderRepo->sumAll('total_amount'),
        ];

        // Monthly sales (move aggregation logic to repository for performance)
        $monthlySales = $this->orderRepo->getMonthlySales();

        // Admin menu from config
        $menuGroups = config('admin.menu');

        return view('admin.dashboard.index', compact('categories', 'stats', 'monthlySales', 'menuGroups'));
    }
}