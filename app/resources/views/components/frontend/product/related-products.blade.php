@props(['products'])

@php
    // Ensure we have a Collection to iterate
    $items = $products instanceof \Illuminate\Pagination\LengthAwarePaginator
        ? $products->getCollection()
        : $products;
@endphp

<section class="mt-12" aria-labelledby="related-products">
    <h2 id="related-products" class="text-2xl font-bold text-morocco-red mb-6">
        Customers also bought
    </h2>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
        @forelse ($products as $product)
            @php
                $primaryImage = $product->images->where('is_primary', 1)->first() ?? $product->images->first();
            @endphp

            <x-frontend.product.card :product="$product" />
        @empty
            <p class="col-span-2 text-gray-500 md:col-span-4">
                No related products found.
            </p>
        @endforelse
    </div>
</section>
