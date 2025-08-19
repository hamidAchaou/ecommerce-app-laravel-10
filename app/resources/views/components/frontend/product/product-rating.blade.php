@props(['rating' => 0, 'reviewsCount' => 0])

@if($rating > 0)
<div class="flex items-center mt-2" aria-label="Product Rating">
    <div class="text-yellow-400">
        @for ($i = 0; $i < 5; $i++)
            <i class="fa{{ $i < $rating ? 's' : 'r' }} fa-star"></i>
        @endfor
    </div>
    <span class="ml-2 text-gray-500">
        ({{ $reviewsCount }} {{ Str::plural('review', $reviewsCount) }})
    </span>
</div>
@endif
