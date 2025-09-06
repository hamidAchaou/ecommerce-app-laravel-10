<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Repositories\Frontend\WishlistRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    protected WishlistRepository $wishlistRepo;

    public function __construct(WishlistRepository $wishlistRepo)
    {
        $this->wishlistRepo = $wishlistRepo;
        $this->middleware('auth');
    }

    // Show the wishlist
    public function index()
    {
        $wishlist = $this->wishlistRepo->getUserWishlist(Auth::id());

        return view('frontend.wishlist.index', compact('wishlist'));
    }

    // Add product to wishlist
    public function store(Product $product)
    {
        if (!$this->wishlistRepo->exists(Auth::id(), $product->id)) {
            $this->wishlistRepo->create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
            ]);
        }

        return back()->with('success', 'Product added to your wishlist!');
    }

    // Remove product from wishlist
    public function destroy(Product $product)
    {
        $this->wishlistRepo->remove(Auth::id(), $product->id);

        return back()->with('success', 'Product removed from your wishlist!');
    }
}