@props(['price', 'discountPrice' => null])

<div class="mt-4">
    @if($discountPrice)
        <div class="flex items-center gap-2">
            <span class="text-2xl font-bold text-morocco-green">
                ${{ number_format($discountPrice, 2) }}
            </span>
            <span class="text-gray-400 line-through">${{ number_format($price, 2) }}</span>
            <span class="bg-morocco-blue text-white text-xs px-2 py-1 rounded">
                -{{ round((1 - $discountPrice / $price) * 100) }}%
            </span>
        </div>
    @else
        <span class="text-2xl font-bold text-morocco-green">
            ${{ number_format($price, 2) }}
        </span>
    @endif
</div>
