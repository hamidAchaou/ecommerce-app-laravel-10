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
        $filters = [
            'search' => $request->input('search'),
            'category_ids' => $request->input('category', []),
            'min' => $request->input('min', 0),
            'max' => $request->input('max', 500),
        ];

        $products = $this->productRepo->getProductsPaginate(filters: $filters, perPage: 9);
        $categories = $this->categoryRepo->all();

        return view('frontend.products.index', compact('products', 'categories'));
    }


    /**
     * Display a single product details page.
     */
    public function show(int $id)
    {
        $product = $this->productRepo->find($id);

        if (!$product) {
            abort(404);
        }

        // ✅ Fetch related products from the same category
        $relatedProducts = $this->productRepo->getProductsPaginate(
            filters: [
                'category_id' => $product->category_id,
                'exclude_id' => $product->id,
            ],
            perPage: 4
        );
        // dd($relatedProducts->first()->images);
        return view('frontend.products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts->getCollection(),
        ]);
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