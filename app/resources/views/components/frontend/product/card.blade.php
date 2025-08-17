@props(['product'])

<div class="group relative bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
    <!-- Product Image -->
    <a href="{{ route('products.show', $product->id) }}" class="block">
        <div class="relative w-full h-72 bg-gray-100 overflow-hidden">
            <img src="{{ asset('storage/' . ($product->images->first()->image_path ?? 'placeholder.jpg')) }}"
                 alt="{{ $product->title }}"
                 class="w-full h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-300">
        </div>

        <!-- Product Info -->
        <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-red-600 transition-colors">
                {{ $product->title }}
            </h3>
            <p class="mt-1 text-sm text-gray-500 line-clamp-2">
                {{ $product->short_description ?? Str::limit($product->description, 60) }}
            </p>
            <p class="mt-2 text-red-600 font-bold text-lg">
                ${{ number_format($product->price, 2) }}
            </p>
        </div>
    </a>

    <!-- Add to Cart Button -->
    <div class="p-4 pt-0">
        <button class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all">
            Add to Cart
        </button>
    </div>
</div>
