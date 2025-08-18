<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    protected $categoryRepository;
    /**
     * CategoryController constructor.
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Display a listing of the categories.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    //
    public function index(Request $request)
    {
        $categories = $this->categoryRepository->getAllPaginate(
            filters: $request->only('search'),
            with: ['parent', 'subcategories'],
            searchableFields: ['name'],
            perPage: 7,
            orderBy: 'created_at',
            direction: 'desc'
        );

        return view('frontend.categories.index', compact('categories'));
    }
}