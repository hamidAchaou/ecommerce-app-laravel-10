@props(['product'])

<div class="flex items-center gap-4">
    {{-- Add to Cart Button --}}
    <button type="button"
            class="add-to-cart-btn flex-1 flex justify-center items-center gap-2 px-5 py-3 bg-morocco-red text-white font-semibold rounded-full shadow-md hover:bg-morocco-blue transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-morocco-blue/20"
            data-product-id="{{ $product->id }}"
            data-quantity="0"
            aria-label="Add {{ $product->title }} to cart">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.6 8h13.2L17 13M7 13H5.4M17 13l1.6 8M9 21h6" />
        </svg>
        <span class="cart-text">Add to Cart</span>
    </button>
</div>