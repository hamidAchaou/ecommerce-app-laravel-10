@props([
    'route' => 'products.index',
    'queryName' => 'search',
    'placeholder' => 'Search Moroccan crafts...'
])

@php
use Illuminate\Support\Arr;
// Preserve all existing query parameters except the current search & page
$params = request()->except([$queryName, 'page']);
@endphp

<form action="{{ route($route) }}" method="GET" class="flex-1 flex items-center gap-3">

    {{-- Hidden fields for filters and pagination --}}
    @foreach ($params as $name => $value)
        @foreach (Arr::wrap($value) as $v)
            @if (!is_array($v))
                <input type="hidden" name="{{ $name }}{{ is_array($value) ? '[]' : '' }}" value="{{ $v }}">
            @endif
        @endforeach
    @endforeach

    {{-- Search input --}}
    <div class="relative flex-1">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input
            type="text"
            name="{{ $queryName }}"
            value="{{ request($queryName) }}"
            placeholder="{{ $placeholder }}"
            class="w-full rounded-xl border border-gray-200 focus:ring-2 focus:ring-morocco-red focus:border-morocco-red pl-12 pr-4 py-2.5 text-sm text-gray-700 shadow-sm transition"
            autocomplete="off"
        >
    </div>

    {{-- Submit button using Primary-button component --}}
    <x-button.primary-button type="submit" color="primary" icon="fas fa-search">
        Search
    </x-button.primary-button>
</form>
