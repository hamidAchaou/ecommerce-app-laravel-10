<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductImageController extends Controller
{
    public function destroy($productId, $imageId)
    {
        try {
            // Debug logging
            Log::info("Attempting to delete image", [
                'product_id' => $productId,
                'image_id' => $imageId
            ]);

            // Find product first
            $product = Product::find($productId);
            if (!$product) {
                Log::error("Product not found", ['product_id' => $productId]);
                if (request()->ajax()) {
                    return response()->json(['error' => 'Product not found.'], 404);
                }
                abort(404, 'Product not found');
            }

            // Find image
            $image = ProductImage::find($imageId);
            if (!$image) {
                Log::error("Image not found", ['image_id' => $imageId]);
                if (request()->ajax()) {
                    return response()->json(['error' => 'Image not found.'], 404);
                }
                abort(404, 'Image not found');
            }

            // Check if image belongs to product
            if ($image->product_id != $product->id) {
                Log::error("Image doesn't belong to product", [
                    'image_product_id' => $image->product_id,
                    'requested_product_id' => $product->id
                ]);
                if (request()->ajax()) {
                    return response()->json(['error' => 'Image does not belong to this product.'], 403);
                }
                abort(403, 'Unauthorized');
            }

            // Delete file if it exists
            if ($image->image_path && Storage::exists($image->image_path)) {
                Storage::delete($image->image_path);
                Log::info("File deleted", ['path' => $image->image_path]);
            }

            // Delete database record
            $image->delete();
            Log::info("Image record deleted", ['image_id' => $imageId]);

            if (request()->ajax()) {
                return response()->json(['message' => 'Image deleted successfully.']);
            }

            return redirect()->back()->with('success', 'Image deleted successfully.');

        } catch (\Exception $e) {
            Log::error("Error deleting image", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->ajax()) {
                return response()->json(['error' => 'An error occurred while deleting the image.'], 500);
            }

            return redirect()->back()->with('error', 'An error occurred while deleting the image.');
        }
    }

    public function setMain($productId, $imageId)
    {
        try {
            $product = Product::find($productId);
            if (!$product) {
                abort(404, 'Product not found');
            }

            $image = ProductImage::find($imageId);
            if (!$image || $image->product_id != $product->id) {
                abort(404, 'Image not found');
            }

            // Set all images of this product to non-primary
            ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);

            // Set this image as primary
            $image->update(['is_primary' => true]);

            return redirect()->back()->with('success', 'Main image updated successfully.');

        } catch (\Exception $e) {
            Log::error("Error setting main image", [
                'error' => $e->getMessage(),
                'product_id' => $productId,
                'image_id' => $imageId
            ]);

            return redirect()->back()->with('error', 'An error occurred while updating the main image.');
        }
    }
}