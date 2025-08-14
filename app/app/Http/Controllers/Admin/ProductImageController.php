<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProductImageController extends Controller
{
    public function destroy($productId, $imageId)
    {
        try {
            // Start database transaction for data consistency
            DB::beginTransaction();

            // Debug logging
            Log::info("Attempting to delete image", [
                'product_id' => $productId,
                'image_id' => $imageId
            ]);

            // Find product first with validation
            $product = Product::find($productId);
            if (!$product) {
                Log::error("Product not found", ['product_id' => $productId]);
                return response()->json(['error' => 'Product not found.'], 404);
            }

            // Find image with proper model instantiation - THIS WAS THE MAIN ISSUE
            $image = $imageId;
            if (!$image) {
                Log::error("Image not found", ['image_id' => $imageId]);
                return response()->json(['error' => 'Image not found.'], 404);
            }

            // Verify image belongs to the product
            if ($image->product_id != $product->id) {
                Log::error("Image doesn't belong to product", [
                    'image_product_id' => $image->product_id,
                    'requested_product_id' => $product->id
                ]);
                return response()->json(['error' => 'Image does not belong to this product.'], 403);
            }

            // Store image path for deletion
            $imagePath = $image->image_path;

            // Delete database record first
            $image->delete();
            Log::info("Image record deleted", ['image_id' => $imageId]);

            // Delete file from storage if it exists
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
                Log::info("File deleted from storage", ['path' => $imagePath]);
            }

            // Commit the transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully.',
                'deleted_image_id' => $imageId
            ], 200);

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            Log::error("Error deleting image", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'product_id' => $productId,
                'image_id' => $imageId
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while deleting the image. Please try again.'
            ], 500);
        }
    }

    public function setMain($productId, $imageId)
    {
        try {
            DB::beginTransaction();

            $product = Product::find($productId);
            if (!$product) {
                return response()->json(['error' => 'Product not found.'], 404);
            }

            $image = ProductImage::find($imageId);
            if (!$image || $image->product_id != $product->id) {
                return response()->json(['error' => 'Image not found or does not belong to this product.'], 404);
            }

            // Set all images of this product to non-primary
            ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);

            // Set this image as primary
            $image->update(['is_primary' => true]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Main image updated successfully.',
                'main_image_id' => $imageId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Error setting main image", [
                'error' => $e->getMessage(),
                'product_id' => $productId,
                'image_id' => $imageId
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while updating the main image.'
            ], 500);
        }
    }
}