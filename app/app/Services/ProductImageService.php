<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Services\Contracts\ProductImageServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProductImageService implements ProductImageServiceInterface
{
    public function uploadImages(Product $product, array $images): void
    {
        $validImages = array_filter($images, fn($image) =>
            $image instanceof UploadedFile && $image->isValid()
        );

        if (empty($validImages)) {
            throw new Exception('No valid images provided.');
        }

        foreach ($validImages as $index => $image) {
            // Create a unique filename
            $filename = uniqid() . '_' . preg_replace('/\s+/', '_', $image->getClientOriginalName());

            // Store file in storage/app/public/products
            $path = $image->storeAs('products', $filename, 'public');

            if (!$path) {
                throw new Exception('Failed to store image: ' . $image->getClientOriginalName());
            }

            // Save only the relative path for asset() to work
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path, // e.g., "products/filename.jpg"
                'is_primary' => $index === 0,
            ]);

            Log::info('Image uploaded', [
                'product_id' => $product->id,
                'filename'   => $filename,
                'path'       => $path,
                'url'        => asset('storage/' . $path) // Useful for debugging
            ]);
        }
    }

    public function deleteImages(Product $product): void
    {
        foreach ($product->images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }
    }
}