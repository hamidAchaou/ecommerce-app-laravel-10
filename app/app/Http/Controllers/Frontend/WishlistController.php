<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Repositories\Frontend\WishlistRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WishlistController extends Controller
{
    public function __construct(protected WishlistRepository $wishlistRepo)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $wishlist = $this->wishlistRepo->getUserWishlist(Auth::id());
        return view('frontend.wishlist.index', compact('wishlist'));
    }

    public function store(Product $product, Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }
    
        try {
            if (!$this->wishlistRepo->exists(Auth::id(), $product->id)) {
                $this->wishlistRepo->create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                ]);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist',
                'isInWishlist' => true,
            ]);
        } catch (\Exception $e) {
            Log::error("Wishlist add error: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to wishlist',
            ], 500);
        }
    }    

    public function destroy(Product $product): JsonResponse
    {
        try {
            $this->wishlistRepo->remove(Auth::id(), $product->id);

            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist',
                'isInWishlist' => false,
            ]);
        } catch (\Exception $e) {
            Log::error("Wishlist remove error: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove from wishlist',
            ], 500);
        }
    }
}