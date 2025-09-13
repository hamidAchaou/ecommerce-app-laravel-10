<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\ProductRepository;
use App\Repositories\Admin\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepository $productRepo,
        private readonly CategoryRepository $categoryRepo
    ) {}

    /**
     * Display a paginated list of all products with filters & sorting.
     */
    public function index(Request $request): View
    {
        $filters = $this->extractFilters($request);

        $products = $this->productRepo->getProductsPaginate(
            filters: $filters,
            perPage: 9
        );

        $categories = $this->categoryRepo->all();

        return view('frontend.products.index', compact('products', 'categories'));
    }

    /**
     * Display a single product details page with related products.
     */
    public function show(int $id): View
    {
        $product = $this->productRepo->find($id);

        if (!$product) {
            throw new NotFoundHttpException("Product not found");
        }

        $relatedProducts = $this->productRepo->getProductsPaginate(
            filters: [
                'category_ids' => [$product->category_id],
                'exclude_id'   => $product->id,
            ],
            perPage: 4
        );

        return view('frontend.products.show', [
            'product'         => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    /**
     * Display products under a specific category.
     */
    public function categoryProducts(string $categorySlug, Request $request): View
    {
        $category = $this->categoryRepo->getBySlug($categorySlug);

        if (!$category) {
            throw new NotFoundHttpException("Category not found");
        }

        $filters = $this->extractFilters($request);
        $filters['category_ids'] = [$category->id];

        $products = $this->productRepo->getProductsPaginate(
            filters: $filters,
            perPage: 12
        );

        return view('frontend.products.index', compact('products', 'category'));
    }

    /**
     * Extract common filters from request.
     */
    private function extractFilters(Request $request): array
    {
        return [
            'search'       => $request->input('search'),
            'category_ids' => $request->input('category', []),
            'min'          => (float) $request->input('min', 0),
            'max'          => (float) $request->input('max', 500),
            'sort'         => $request->input('sort', 'default'),
        ];
    }
}