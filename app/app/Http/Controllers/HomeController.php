<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Get featured categories
        $categories = Category::take(3)->get();

        // Get featured products
        $products = Product::where('is_featured', true)
            ->take(4)
            ->get();

        return view('home', compact('categories', 'products'));
    }
}