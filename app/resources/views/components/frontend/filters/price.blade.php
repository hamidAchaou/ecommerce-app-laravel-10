<div class="bg-white rounded-2xl shadow-lg p-6 sticky top-60 hover:shadow-2xl transition-shadow duration-300">
    <h3 class="text-xl font-bold text-gray-900 mb-5 border-b pb-3">Filter by Price</h3>

    <form action="{{ route('products.index') }}" method="GET" class="space-y-6">
        {{-- Preserve other query params except price + pagination --}}
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

        {{-- Hidden fields for min/max values --}}
        <input type="hidden" name="min" id="minPrice" value="{{ request('min', 0) }}">
        <input type="hidden" name="max" id="maxPrice" value="{{ request('max', 500) }}">

        {{-- Range sliders --}}
        <div class="relative pt-4">
            <input type="range" id="minSlider" min="0" max="500" value="{{ request('min', 0) }}"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-red-600">
            <input type="range" id="maxSlider" min="0" max="500" value="{{ request('max', 500) }}"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-red-600 mt-2">
        </div>

        {{-- Labels --}}
        <div class="flex justify-between text-gray-700 font-medium text-sm">
            <span id="minLabel">${{ request('min', 0) }}</span>
            <span id="maxLabel">${{ request('max', 500) }}</span>
        </div>

        {{-- Submit button --}}
        <button type="submit"
                class="w-full bg-red-600 text-white font-semibold py-2 rounded-xl shadow hover:bg-red-700 transition">
            Apply Filter
        </button>
    </form>
</div>
