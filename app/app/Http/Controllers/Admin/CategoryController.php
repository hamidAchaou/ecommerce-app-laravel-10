<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Repositories\admin\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * @var CategoryRepository
     */
    protected CategoryRepository $categoryRepository;

    /**
     * CategoryController constructor.
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Display a paginated list of categories.
     */
    public function index(Request $request): View
    {
        $categories = $this->categoryRepository->getAllPaginate(
            filters: $request->only('search'),
            with: ['parent', 'subcategories'],
            searchableFields: ['name'],
            perPage: 7,
            orderBy: 'created_at',
            direction: 'desc'
        );

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Display the specified category.
     *
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        $category = $this->categoryRepository->find($id, ['parent', 'subcategories']);

        if (!$category) {
            abort(404, 'Category not found.');
        }

        return view('admin.categories.show', compact('category'));
    }
    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        $categories = $this->categoryRepository->all();

        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(CategoryRequest $categoryRequest): RedirectResponse
    {
        $validated = $categoryRequest->validated();

        $this->categoryRepository->create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing a category.
     */
    public function edit(int $id): View
    {
        $category = $this->categoryRepository->find($id, ['parent', 'subcategories']);
        $categories = $this->categoryRepository->all();

        if (!$category) {
            abort(404, 'Category not found.');
        }

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified category.
     */
    public function update(categoryRequest $request, int $id): RedirectResponse
    {
        $validated = $request->validated();

        $this->categoryRepository->update($validated, $id);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->categoryRepository->delete($id);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}