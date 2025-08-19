@props(['product'])

<article class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 max-w-sm mx-auto">

    {{-- Product Image --}}
    <a href="{{ route('products.show', $product->id) }}" 
       class="block focus:outline-none focus:ring-4 focus:ring-morocco-blue/20">
        <figure class="relative w-full h-80 bg-morocco-ivory overflow-hidden">
            <img 
                src="{{ asset('storage/' . ($product->images->first()->image_path ?? 'placeholder.jpg')) }}"
                alt="{{ $product->title ?? 'Moroccan handcrafted product' }}"
                loading="lazy"
                decoding="async"
                class="w-full h-full object-cover object-center transform group-hover:scale-110 transition-transform duration-500 ease-out"
                width="400"
                height="400"
            >

            {{-- Badges (New / Sale) --}}
            <div class="absolute top-4 left-4 flex flex-col gap-2">
                @if($product->is_new ?? false)
                    <span class="bg-morocco-green text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow-md">
                        New
                    </span>
                @endif
                @if($product->old_price ?? false)
                    <span class="bg-morocco-red text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow-md">
                        Sale
                    </span>
                @endif
            </div>

            {{-- Quick View Button (Visible on Hover) --}}
            <button class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white/90 p-2 rounded-full shadow-md hover:bg-morocco-blue hover:text-white focus:outline-none focus:ring-2 focus:ring-morocco-blue"
                    aria-label="Quick view {{ $product->title }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        </figure>
    </a>

    {{-- Product Info --}}
    <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 group-hover:text-morocco-blue transition-colors duration-300 line-clamp-1">
            {{ $product->title }}
        </h2>

        <p class="mt-2 text-sm text-gray-500 line-clamp-2 leading-relaxed">
            {{ $product->short_description ?? Str::limit(strip_tags($product->description), 80, '...') }}
        </p>

        {{-- Price and Rating --}}
        <div class="mt-4 flex items-center justify-between">
            <div class="flex items-baseline gap-2">
                <p class="text-2xl font-bold text-morocco-red">
                    ${{ number_format($product->price, 2) }}
                </p>
                @if($product->old_price ?? false)
                    <p class="text-sm line-through text-gray-400">
                        ${{ number_format($product->old_price, 2) }}
                    </p>
                @endif
            </div>
            <div class="flex items-center gap-1">
                <svg class="h-4 w-4 text-morocco-yellow" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span class="text-sm text-gray-600">4.8</span>
            </div>
        </div>
    </div>

    {{-- Add to Cart Section --}}
    <div class="px-6 pb-6">
        <x-frontend.product.add-to-cart :product="$product" />
    </div>
</article>