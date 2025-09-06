    
    {{-- 🔍 Search --}}
    <form action="{{ route('products.index') }}" method="GET" class="flex-1 flex items-center gap-3">
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

        <div class="relative flex-1">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Search Moroccan crafts..." 
                class="w-full rounded-xl border border-gray-200 focus:ring-2 focus:ring-morocco-red focus:border-morocco-red pl-12 pr-4 py-2.5 text-sm text-gray-700 shadow-sm transition"
            >
        </div>

        <button type="submit"
            class="px-5 py-2.5 bg-gradient-to-r from-morocco-red to-morocco-blue text-white font-semibold rounded-xl shadow hover:opacity-90 transition">
            Search
        </button>
    </form>
