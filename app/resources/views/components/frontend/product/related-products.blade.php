@props(['products'])

@php
    use Illuminate\Support\Collection;
    use Illuminate\Pagination\LengthAwarePaginator;

    // Normalize $products
    if (is_string($products)) {
        $products = collect();
    } elseif ($products instanceof LengthAwarePaginator) {
        $products = $products->getCollection();
    } elseif (is_array($products)) {
        $products = collect($products);
    } elseif (!$products instanceof Collection) {
        $products = collect($products ?? []);
    }
@endphp

<section class="mt-12" aria-labelledby="related-products">
    <h2 id="related-products" class="text-2xl font-bold text-morocco-red mb-6">
        Customers also bought
    </h2>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
        @forelse ($products as $product)
            <x-frontend.product.card :product="$product" />
        @empty
            <p class="col-span-2 md:col-span-4 text-gray-500">
                No related products found.
            </p>
        @endforelse
    </div>
</section>
