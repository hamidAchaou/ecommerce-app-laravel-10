<div 
    x-data="{ open: true }" 
    class="bg-white rounded-2xl shadow p-6 sticky top-40 hover:shadow-xl transition-all duration-300"
>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-4 cursor-pointer" @click="open = !open">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
            <i class="fas fa-layer-group text-morocco-blue"></i>
            Categories
        </h3>
        <button type="button" class="text-gray-500 hover:text-morocco-blue transition">
            <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
        </button>
    </div>

    {{-- Body --}}
    <div x-show="open" x-transition>
        <form action="{{ route('products.index') }}" method="GET" class="space-y-2">
            
            {{-- Preserve other query params except category/page --}}
            @php
                use Illuminate\Support\Arr;
                $params = request()->except(['category', 'page']);
            @endphp

            @foreach ($params as $name => $value)
                @foreach (Arr::wrap($value) as $v)
                    <input type="hidden" name="{{ $name }}{{ is_array($value) ? '[]' : '' }}" value="{{ $v }}">
                @endforeach
            @endforeach

            {{-- All Categories --}}
            <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-morocco-ivory cursor-pointer transition {{ empty(request('category')) ? 'bg-morocco-red/10 border border-morocco-red' : '' }}">
                <input type="radio" name="category[]" value="" onchange="this.form.submit()"
                    {{ empty(request('category')) ? 'checked' : '' }}
                    class="text-morocco-red focus:ring-morocco-red border-gray-300">
                <span class="text-gray-800 font-medium">All Categories</span>
            </label>

            {{-- Individual Categories --}}
            @foreach ($categories as $category)
                @php $isSelected = in_array($category->id, request('category', [])); @endphp
                <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-morocco-ivory cursor-pointer transition {{ $isSelected ? 'bg-morocco-red/10 border border-morocco-red' : '' }}">
                    <input type="checkbox" name="category[]" value="{{ $category->id }}" onchange="this.form.submit()"
                        {{ $isSelected ? 'checked' : '' }}
                        class="text-morocco-red focus:ring-morocco-red border-gray-300 rounded">
                    <span class="text-gray-800 font-medium">{{ $category->name }}</span>
                </label>
            @endforeach

        </form>
    </div>
</div>
