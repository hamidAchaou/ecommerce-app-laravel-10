<?php

namespace App\Services\Contracts;

use App\Models\Product;
use Illuminate\Http\UploadedFile;

interface ProductImageServiceInterface
{
    /**
     * Handle image uploads and associate them with a product.
     *
     * @param Product $product
     * @param UploadedFile[] $images
     * @return void
     */
    public function uploadImages(Product $product, array $images): void;
}
