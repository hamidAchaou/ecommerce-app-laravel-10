@props([
    'rating' => 0,
    'reviewsCount' => 0
])

@if($rating > 0)
<section class="flex items-center mt-2" aria-label="Product rating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
    <div class="flex text-yellow-400" itemprop="ratingValue" content="{{ $rating }}">
        @for ($i = 1; $i <= 5; $i++)
            <i class="fa{{ $i <= $rating ? 's' : 'r' }} fa-star" aria-hidden="true"></i>
        @endfor
    </div>

    <span class="ml-2 text-gray-500">
        <span itemprop="reviewCount">{{ $reviewsCount }}</span>
        {{ Str::plural('review', $reviewsCount) }}
    </span>
</section>
@endif
