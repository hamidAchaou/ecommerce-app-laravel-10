{{-- 🔽 Sort --}}
<form method="GET" action="{{ route('products.index') }}" class="w-full sm:w-48 flex-shrink-0">
    @php
        $params = request()->except(['sort', 'page']);
    @endphp

    {{-- Preserve existing query params --}}
    @foreach ($params as $name => $value)
        @foreach (Arr::wrap($value) as $v)
            @if (!is_array($v))
                <input type="hidden" name="{{ $name }}{{ is_array($value) ? '[]' : '' }}" value="{{ $v }}">
            @endif
        @endforeach
    @endforeach

    {{-- Label with icon --}}
    <label for="sort" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
        <i class="fas fa-sort text-morocco-blue"></i>
        Sort by
    </label>

    {{-- Select with arrow icon --}}
    <div class="relative">
        <select id="sort" name="sort" onchange="this.form.submit()"
            class="w-full rounded-xl border border-gray-200 focus:ring-2 focus:ring-morocco-red focus:border-morocco-red py-2.5 px-3 text-sm font-medium text-gray-700 shadow-sm transition appearance-none">
            <option value="">Default</option>
            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low → High</option>
            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High → Low</option>
        </select>
        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
    </div>
</form>
