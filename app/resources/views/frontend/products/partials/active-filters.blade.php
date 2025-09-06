@if(request('category') || request('search') || request('min_price') || request('max_price'))
    <div class="mb-6 flex flex-wrap items-center gap-3">
        {{-- Search Filter --}}
        @if(request('search'))
            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}"
               class="flex items-center bg-morocco-green text-white text-sm px-3 py-1 rounded-full hover:bg-green-700">
                Search: "{{ request('search') }}"
                <span class="ml-2">&times;</span>
            </a>
        @endif

        {{-- Category Filters --}}
        @if(request('category'))
            @php
                $activeCategories = (array) request('category');
            @endphp
            @foreach($activeCategories as $catId)
                @php
                    $cat = $categories->firstWhere('id', $catId);
                @endphp
                @if($cat)
                    <a href="{{ request()->fullUrlWithQuery(['category' => collect($activeCategories)->reject(fn($id) => $id == $catId)->all() ?: null]) }}"
                       class="flex items-center bg-morocco-blue text-white text-sm px-3 py-1 rounded-full hover:bg-blue-700">
                        {{ $cat->name }}
                        <span class="ml-2">&times;</span>
                    </a>
                @endif
            @endforeach
        @endif

        {{-- Price Filter --}}
        @if(request('min_price') || request('max_price'))
            <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}"
               class="flex items-center bg-morocco-red text-white text-sm px-3 py-1 rounded-full hover:bg-red-700">
                Price: 
                {{ request('min_price') ? 'from ' . request('min_price') : '' }}
                {{ request('max_price') ? ' to ' . request('max_price') : '' }}
                <span class="ml-2">&times;</span>
            </a>
        @endif

        {{-- Clear All --}}
        <a href="{{ route('products.index') }}"
           class="flex items-center bg-gray-300 text-gray-800 text-sm px-3 py-1 rounded-full hover:bg-gray-400">
            Clear All
            <span class="ml-2">&times;</span>
        </a>
    </div>
@endif
