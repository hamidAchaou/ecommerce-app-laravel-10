@props(['price', 'discountPrice' => null])

@php
    $isDiscounted = !is_null($discountPrice) && $discountPrice < $price;
    $formattedPrice = '$' . number_format($price, 2);
    $formattedDiscount = $isDiscounted ? '$' . number_format($discountPrice, 2) : null;
    $discountPercent = $isDiscounted ? round((1 - $discountPrice / $price) * 100) : null;
@endphp

<div class="mt-4">
    @if ($isDiscounted)
        <div class="flex items-center gap-2">
            {{-- Discount Price --}}
            <span 
                class="text-2xl font-bold text-morocco-green" 
                aria-label="Discounted price"
            >
                {{ $formattedDiscount }}
            </span>

            {{-- Original Price --}}
            <span 
                class="text-gray-400 line-through" 
                aria-label="Original price"
            >
                {{ $formattedPrice }}
            </span>

            {{-- Discount Percentage --}}
            <span 
                class="bg-morocco-blue text-white text-xs px-2 py-1 rounded" 
                aria-label="Discount {{ $discountPercent }} percent"
            >
                -{{ $discountPercent }}%
            </span>
        </div>
    @else
        {{-- Regular Price --}}
        <span 
            class="text-2xl font-bold text-morocco-green" 
            aria-label="Price"
        >
            {{ $formattedPrice }}
        </span>
    @endif
</div>
