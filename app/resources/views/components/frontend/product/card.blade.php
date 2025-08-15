@props(['product'])

{{-- @dd($product->images) --}}
<div class="group relative bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
    <!-- Product Image -->
    <div class="w-full min-h-80 bg-gray-200 aspect-w-1 aspect-h-1 rounded-md overflow-hidden group-hover:opacity-75 lg:h-80 lg:aspect-none">
        <img src="{{ asset('storage/' . ($product->images->first()->image_path ?? 'placeholder.jpg')) }}"
        alt="{{ $product->name }}"
        class="w-full h-full object-center object-cover lg:w-full lg:h-full">   
    </div>

    <!-- Product Info -->
    <div class="mt-4 flex justify-between items-start">
        <div>
            <h3 class="text-sm text-gray-700">
                <a href="{{ route('products.show', $product->id) }}">
                    <span aria-hidden="true" class="absolute inset-0"></span>
                    {{ $product->name }}
                </a>
            </h3>
            <p class="mt-1 text-sm text-gray-500">{{ $product->short_description ?? '' }}</p>
        </div>
        <p class="text-sm font-medium text-gray-900">${{ number_format($product->price, 2) }}</p>
    </div>

    <!-- Add to Cart Button -->
    <button class="mt-4 w-full bg-red-600 border border-transparent rounded-md py-2 px-4 flex items-center justify-center text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
        Add to cart
    </button>
</div>
