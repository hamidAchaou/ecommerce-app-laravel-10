@props(['product'])

<div class="flex items-center gap-2">
    <input type="number"
           name="quantity"
           min="1"
           max="{{ $product->stock }}"
           value="1"
           class="quantity-input w-20 rounded border border-gray-300 px-2 py-1 focus:ring focus:ring-morocco-blue focus:outline-none">
</div>

<button type="button"
        class="add-to-cart-btn w-full flex justify-center items-center gap-2 px-4 py-2 bg-red-600 text-white font-semibold rounded-xl shadow hover:bg-red-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        data-product-id="{{ $product->id }}"
        aria-label="Add {{ $product->title }} to cart">
    
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
         viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.6 8h13.2L17 13M7 13H5.4M17 13l1.6 8M9 21h6" />
    </svg>
    Add to Cart
</button>
