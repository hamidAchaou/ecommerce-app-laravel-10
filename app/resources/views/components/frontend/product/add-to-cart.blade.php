@props(['product'])

<div class="flex items-center gap-3">
    {{-- Add to Cart Button --}}
    <button
        type="button"
        class="add-to-cart-btn flex-1 flex justify-center items-center gap-2 px-5 py-3 bg-morocco-red text-white font-semibold rounded-full shadow-md hover:bg-morocco-blue transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-morocco-blue/20"
        data-product-id="{{ $product->id }}"
        data-quantity="0"
        aria-label="Add {{ $product->title }} to cart"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.6 8h13.2L17 13M7 13H5.4M17 13l1.6 8M9 21h6" />
        </svg>
        <span class="cart-text">Add to Cart</span>
    </button>

    {{-- Wishlist Button --}}
    <x-frontend.button.icon-button
        :active="$product->isInWishlist()"
        data-product-id="{{ $product->id }}"
        aria-label="{{ $product->isInWishlist() ? 'Remove' : 'Add' }} {{ $product->title }} to wishlist"
        class="wishlist-btn"
    >
        <svg xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            fill="{{ $product->isInWishlist() ? 'currentColor' : 'none' }}"
            viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 8c0-2.21 1.79-4 4-4 1.657 0 3.14 1.007 3.763 2.42C12.86 5.007 14.343 4 16 4c2.21 0 4 1.79 4 4 0 3.09-4 6.25-8 10-4-3.75-8-6.91-8-10z" />
        </svg>
    </x-frontend.button.icon-button>
</div>
<script>
    window.App = {
        isLoggedIn: true
    };
</script>
