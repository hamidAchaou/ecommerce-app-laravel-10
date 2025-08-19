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
        @forelse ($items as $related)
            @php
                $primaryImage = $related->images->where('is_primary', 1)->first() ?? $related->images->first();
            @endphp

            <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                <a href="{{ route('products.show', $related->id) }}" 
                   aria-label="View {{ $related->title }}" 
                   title="{{ $related->title }}">
                    <img src="{{ asset('storage/' . ($primaryImage->image_path ?? 'images/no-image.png')) }}"
                         alt="{{ $primaryImage->alt_text ?? $related->title }}"
                         class="w-full h-48 object-cover"
                         loading="lazy"
                         width="300"
                         height="300">
                </a>

                <div class="p-3">
                    <h3 class="font-semibold text-gray-800 text-sm md:text-base">
                        <a href="{{ route('products.show', $related->id) }}" title="{{ $related->title }}">
                            {{ Str::limit($related->title, 40) }}
                        </a>
                    </h3>

                    <p class="font-bold text-morocco-green mt-1">
                        ${{ number_format($related->price, 2) }}
                    </p>

                    <a href="{{ route('products.show', $related->id) }}"
                       class="block mt-2 text-center text-white bg-morocco-blue py-1 rounded hover:bg-blue-700 transition"
                       title="View {{ $related->title }}">
                        View
                    </a>
                </div>
            </div>

        @empty
            <p class="col-span-2 text-gray-500 md:col-span-4">
                No related products found.
            </p>
        @endforelse
    </div>
</section>
