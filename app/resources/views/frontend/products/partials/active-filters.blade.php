@if(request()->hasAny(['search','category','min','max']))
    <div class="flex flex-wrap items-center gap-2 mb-6">
        {{-- Search filter --}}
        @if(request('search'))
            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">
                Search: "{{ request('search') }}"
                <a href="{{ route('products.index', request()->except('search')) }}" class="ml-2 text-gray-500">&times;</a>
            </span>
        @endif

        {{-- Category filters --}}
        @if(request('category'))
            @foreach(request('category') as $catId)
                @php $cat = $categories->firstWhere('id', $catId); @endphp
                @if($cat)
                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">
                        {{ $cat->name }}
                        <a href="{{ route('products.index', array_diff_key(request()->all(), ['category'=>[$catId]])) }}" class="ml-2 text-gray-500">&times;</a>
                    </span>
                @endif
            @endforeach
        @endif

        {{-- Price filter --}}
        @if(request('min') || request('max'))
            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">
                Price: ${{ request('min', 0) }} - ${{ request('max', 500) }}
                <a href="{{ route('products.index', request()->except(['min','max'])) }}" class="ml-2 text-gray-500">&times;</a>
            </span>
        @endif
    </div>
@endif
