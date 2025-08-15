<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\ProductRepository;
use App\Repositories\Admin\CategoryRepository;

class HomeController extends Controller
{
    private const LATEST_PRODUCTS_LIMIT = 12;
    private const FEATURED_PRODUCTS_LIMIT = 8;
    private const HOMEPAGE_CATEGORIES_LIMIT = 6;

    public function __construct(
        private ProductRepository $productRepo,
        private CategoryRepository $categoryRepo
    ) {}

    /**
     * Display homepage with latest, featured products & categories.
     */
    public function index()
    {
        $latestProducts = $this->productRepo->getLatestProducts(self::LATEST_PRODUCTS_LIMIT);
        $featuredProducts = $this->productRepo->getFeaturedProducts(self::FEATURED_PRODUCTS_LIMIT);
        $categories = $this->categoryRepo->getHomepageCategories(self::HOMEPAGE_CATEGORIES_LIMIT);

        return view('frontend.home', compact('latestProducts', 'featuredProducts', 'categories'));
    }
}