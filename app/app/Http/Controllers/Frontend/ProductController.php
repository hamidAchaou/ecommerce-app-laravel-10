<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\ProductRepository;
use App\Repositories\Admin\CategoryRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductRepository $productRepo,
        private CategoryRepository $categoryRepo
    ) {}

    /**
     * Display a paginated list of all products.
     */
    public function index(Request $request)
    {
        $products = $this->productRepo->getProductsPaginate(
            filters: $request->all(),
            perPage: 12
        );

        return view('frontend.products.index', compact('products'));
    }

    /**
     * Display a single product details page.
     */
    public function show(string $slug)
    {
        $product = $this->productRepo->getBySlug($slug);

        if (!$product) {
            abort(404);
        }

        return view('frontend.products.show', compact('product'));
    }

    /**
     * Display products under a specific category.
     */
    public function categoryProducts($categorySlug, Request $request)
    {
        $category = $this->categoryRepo->getBySlug($categorySlug);

        if (!$category) {
            abort(404);
        }

        $products = $this->productRepo->getProductsPaginate(
            filters: ['category_id' => $category->id],
            perPage: 12
        );

        return view('frontend.products.index', compact('products', 'category'));
    }
}