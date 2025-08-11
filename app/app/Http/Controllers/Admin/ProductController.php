<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Repositories\admin\ProductRepository;
use App\Models\Category;
use App\Services\Admin\ProductService;
use App\Services\ProductImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductController extends Controller
{
    protected ProductRepository $productRepository;
    protected ProductImageService $productImageService;
    protected $ProductService;
    public function __construct(
        ProductRepository $productRepository,
        ProductImageService $productImageService,
        ProductService $ProductService
    ) {
        $this->productRepository = $productRepository;
        $this->productImageService = $productImageService;
        $this->ProductService = $ProductService;
    }

    public function index(Request $request)
    {
        $products = $this->productRepository->getAllPaginate(
            filters: $request->only('search'),
            with: ['category', 'images'],
            searchableFields: ['title', 'description'],
            perPage: 10
        );

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('type', 'product')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function show(string $id)
    {
        $product = $this->productRepository->find($id, ['category', 'images']);

        // dd($product);
        if (!$product) {
            abort(404, 'Product not found.');
        }

        return view('admin.products.show', [
            'product' => $product
        ]);
    }


    public function store(StoreProductRequest $request)
    {
        if (!$request->hasFile('images')) {
            return back()->with('error', 'You must upload at least one image.')->withInput();
        }

        DB::beginTransaction();
        try {
            $productData = $request->except('images');
            $product = $this->productRepository->create($productData);
            // dd($product);

            if (!$product) {
                throw new Exception('Failed to create product.');
            }

            // Upload and link images
            $this->productImageService->uploadImages(
                $product,
                $request->file('images')
            );

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Product creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'An unexpected error occurred. Please try again.')->withInput();
        }
    }

    public function update(UpdateProductRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->except('images');
            $product = $this->productRepository->update($data, $id);

            if (!$product) {
                throw new Exception('Failed to update product.');
            }

            if ($request->hasFile('images')) {
                if ($request->boolean('replace_images')) {
                    $this->productImageService->deleteImages($product);
                }
                $this->productImageService->uploadImages(
                    $product,
                    $request->file('images')
                );
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Product update failed', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to update product. Please try again.')->withInput();
        }
    }

    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $product = $this->productRepository->find($id);
            if (!$product) {
                throw new Exception('Product not found.');
            }

            $this->productImageService->deleteImages($product);
            $this->productRepository->delete($id);

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Product deletion failed', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to delete product.');
        }
    }

    public function importForm()
    {
        return view('admin.products.import'); // صمم هذه الصفحة مع نموذج رفع الملف
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv'
        ]);

        $this->ProductService->import($request->file('file'));

        return redirect()->back()->with('success', 'Products imported successfully.');
    }

    public function export()
    {
        return $this->ProductService->export();
    }
}