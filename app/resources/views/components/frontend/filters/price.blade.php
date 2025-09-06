<div 
    x-data="{ open: true }" 
    class="bg-white rounded-2xl shadow-lg p-6 sticky top-20 hover:shadow-xl transition-shadow duration-300"
>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-4 cursor-pointer" @click="open = !open">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
            <i class="fas fa-tags text-morocco-green"></i>
            Price Range
        </h3>
        <button type="button" class="text-gray-500 hover:text-morocco-green transition">
            <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
        </button>
    </div>

    {{-- Body --}}
    <div x-show="open" x-transition>
        <form action="{{ route('products.index') }}" method="GET" class="space-y-6">
            {{-- Preserve other query params --}}
            @php
                use Illuminate\Support\Arr;
                $params = request()->except(['min', 'max', 'page']);
            @endphp

            @foreach ($params as $name => $value)
                @foreach (Arr::wrap($value) as $v)
                    @if (!is_array($v))
                        <input type="hidden" name="{{ $name }}{{ is_array($value) ? '[]' : '' }}" value="{{ $v }}">
                    @endif
                @endforeach
            @endforeach

            {{-- Hidden fields for min/max --}}
            <input type="hidden" name="min" id="minPrice" value="{{ request('min', 0) }}">
            <input type="hidden" name="max" id="maxPrice" value="{{ request('max', 500) }}">

            {{-- Range sliders --}}
            <div class="relative pt-2">
                <input type="range" id="minSlider" min="0" max="500" value="{{ request('min', 0) }}"
                       class="w-full h-2 bg-gradient-to-r from-morocco-red to-morocco-blue rounded-lg appearance-none cursor-pointer accent-morocco-red">
                <input type="range" id="maxSlider" min="0" max="500" value="{{ request('max', 500) }}"
                       class="w-full h-2 bg-gradient-to-r from-morocco-blue to-morocco-green rounded-lg appearance-none cursor-pointer accent-morocco-red mt-2">
            </div>

            {{-- Labels --}}
            <div class="flex justify-between text-gray-700 font-medium text-sm">
                <span id="minLabel">${{ request('min', 0) }}</span>
                <span id="maxLabel">${{ request('max', 500) }}</span>
            </div>

            {{-- Submit button --}}
            <button type="submit"
                class="w-full bg-gradient-to-r from-morocco-red to-morocco-blue text-white font-semibold py-2 rounded-xl shadow hover:opacity-90 transition">
                Apply Filter
            </button>
        </form>
    </div>
</div>
