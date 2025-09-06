@props([
    'route' => 'products.index',
    'name' => 'sort',
])

@php
use Illuminate\Support\Arr;
// Preserve all existing query parameters except sort & page
$params = request()->except([$name, 'page']);
$currentSort = request($name, '');
@endphp

<form method="GET" action="{{ route($route) }}" class="w-full sm:w-48 flex-shrink-0">

    {{-- Preserve existing filters --}}
    @foreach ($params as $key => $value)
        @foreach (Arr::wrap($value) as $v)
            @if (!is_array($v))
                <input type="hidden" name="{{ $key }}{{ is_array($value) ? '[]' : '' }}" value="{{ $v }}">
            @endif
        @endforeach
    @endforeach

    {{-- Label with icon --}}
    <label for="{{ $name }}" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
        <i class="fas fa-sort text-morocco-blue"></i>
        Sort by
    </label>

    {{-- Select dropdown styled like categories --}}
    <div class="relative">
        <select id="{{ $name }}" name="{{ $name }}" onchange="this.form.submit()"
            class="w-full rounded-lg border border-gray-200 focus:ring-2 focus:ring-morocco-blue focus:border-morocco-blue py-2.5 px-3 text-sm font-medium text-gray-800 shadow-sm transition duration-200 appearance-none
                   hover:bg-morocco-ivory">
            <option value="" {{ $currentSort === '' ? 'selected' : '' }}>Default</option>
            <option value="newest" {{ $currentSort === 'newest' ? 'selected' : '' }}>Newest</option>
            <option value="price_asc" {{ $currentSort === 'price_asc' ? 'selected' : '' }}>Price: Low → High</option>
            <option value="price_desc" {{ $currentSort === 'price_desc' ? 'selected' : '' }}>Price: High → Low</option>
        </select>

        {{-- Dropdown arrow --}}
        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
    </div>
</form>
