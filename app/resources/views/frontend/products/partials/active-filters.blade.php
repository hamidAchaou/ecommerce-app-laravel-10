@if(request('category') || request('search') || request('min') || request('max'))
    <div class="mb-6 flex flex-wrap items-center gap-3">

        {{-- Search --}}
        @if(request('search'))
            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}"
               class="flex items-center bg-morocco-green text-white text-sm px-4 py-2 rounded-full shadow hover:bg-green-700 transition">
                <i class="fas fa-search mr-2"></i>
                "{{ request('search') }}"
                <span class="ml-2">&times;</span>
            </a>
        @endif

        {{-- Categories --}}
        @if(request('category'))
            @php $activeCategories = (array) request('category'); @endphp
            @foreach($activeCategories as $catId)
                @php $cat = $categories->firstWhere('id', $catId); @endphp
                @if($cat)
                    <a href="{{ request()->fullUrlWithQuery(['category' => collect($activeCategories)->reject(fn($id) => $id == $catId)->all() ?: null]) }}"
                       class="flex items-center bg-morocco-blue text-white text-sm px-4 py-2 rounded-full shadow hover:bg-blue-700 transition">
                        <i class="fas fa-layer-group mr-2"></i>
                        {{ $cat->name }}
                        <span class="ml-2">&times;</span>
                    </a>
                @endif
            @endforeach
        @endif

        {{-- Price --}}
        @if(request('min') || request('max'))
            <a href="{{ request()->fullUrlWithQuery(['min' => null, 'max' => null]) }}"
               class="flex items-center bg-morocco-red text-white text-sm px-4 py-2 rounded-full shadow hover:bg-red-700 transition">
                <i class="fas fa-tags mr-2"></i>
                {{ request('min') ? 'From $'.request('min') : '' }}
                {{ request('max') ? ' to $'.request('max') : '' }}
                <span class="ml-2">&times;</span>
            </a>
        @endif

        {{-- Clear All --}}
        <a href="{{ route('products.index') }}"
           class="flex items-center bg-gray-200 text-gray-800 text-sm px-4 py-2 rounded-full shadow hover:bg-gray-300 transition">
            <i class="fas fa-times-circle mr-2"></i>
            Clear All
        </a>
    </div>
@endif
