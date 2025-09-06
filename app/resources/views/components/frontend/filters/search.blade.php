<div 
    x-data="{ open: true }" 
    class="bg-white rounded-2xl shadow-lg p-6 sticky top-20 hover:shadow-xl transition-shadow duration-300"
>
    <div class="flex justify-between items-center mb-4 cursor-pointer" @click="open = !open">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
            <i class="fas fa-search text-morocco-red"></i>
            Search
        </h3>
        <button type="button" class="text-gray-500 hover:text-morocco-red transition">
            <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
        </button>
    </div>

    <div x-show="open" x-transition>
        <form action="{{ route('products.index') }}" method="GET" class="flex items-center gap-2">
            {{-- Preserve other filters --}}
            @php
                use Illuminate\Support\Arr;
                $params = request()->except(['search', 'page']);
            @endphp

            @foreach ($params as $name => $value)
                @foreach (Arr::wrap($value) as $v)
                    @if (!is_array($v))
                        <input type="hidden" name="{{ $name }}{{ is_array($value) ? '[]' : '' }}" value="{{ $v }}">
                    @endif
                @endforeach
            @endforeach

            <div class="relative w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                    class="w-full rounded-xl border-gray-200 focus:ring-morocco-red focus:border-morocco-red px-4 py-2 pl-10 text-sm shadow-sm"
                    aria-label="Search products">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>

            <button type="submit"
                class="px-4 py-2 bg-morocco-red text-white font-medium rounded-xl shadow hover:bg-morocco-blue transition">
                <span class="hidden sm:inline">Search</span>
            </button>
        </form>
    </div>
</div>
