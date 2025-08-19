<div class="bg-white rounded-2xl shadow-lg p-6 sticky top-60 hover:shadow-2xl transition-shadow duration-300">
    <h3 class="text-xl font-bold text-gray-900 mb-5 border-b pb-3">Filter by Price</h3>

    <form action="{{ route('products.index') }}" method="GET" class="space-y-4">

        {{-- Preserve other filters --}}
        @foreach(request()->except(['min','max','page']) as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach

        <input type="hidden" name="min" id="minPrice" value="{{ request('min', 0) }}">
        <input type="hidden" name="max" id="maxPrice" value="{{ request('max', 500) }}">

        <div class="relative h-2 bg-red-200 rounded-full">
            <input type="range" min="0" max="500" value="{{ request('min', 0) }}"
                id="minSlider" class="absolute w-full h-2 bg-transparent appearance-none pointer-events-none">
            <input type="range" min="0" max="500" value="{{ request('max', 500) }}"
                id="maxSlider" class="absolute w-full h-2 bg-transparent appearance-none pointer-events-none">
            <div id="rangeHighlight" class="absolute h-2 bg-red-600 rounded-full"></div>
        </div>

        <div class="flex justify-between text-gray-700 font-medium text-sm">
            <span id="minLabel">${{ request('min', 0) }}</span>
            <span id="maxLabel">${{ request('max', 500) }}</span>
        </div>

        <button type="submit"
            class="w-full bg-red-600 text-white font-semibold py-2 rounded-xl shadow hover:bg-red-700 transition">
            Apply Filter
        </button>
    </form>

    @push('scripts')
    <script>
        const minSlider = document.getElementById('minSlider');
        const maxSlider = document.getElementById('maxSlider');
        const minPrice = document.getElementById('minPrice');
        const maxPrice = document.getElementById('maxPrice');
        const minLabel = document.getElementById('minLabel');
        const maxLabel = document.getElementById('maxLabel');
        const rangeHighlight = document.getElementById('rangeHighlight');

        function updateSlider() {
            let minVal = parseInt(minSlider.value);
            let maxVal = parseInt(maxSlider.value);

            if (minVal > maxVal - 10) {
                minVal = maxVal - 10;
                minSlider.value = minVal;
            }
            if (maxVal < minVal + 10) {
                maxVal = minVal + 10;
                maxSlider.value = maxVal;
            }

            minPrice.value = minVal;
            maxPrice.value = maxVal;
            minLabel.textContent = "$" + minVal;
            maxLabel.textContent = "$" + maxVal;

            const percent1 = (minVal / minSlider.max) * 100;
            const percent2 = (maxVal / maxSlider.max) * 100;
            rangeHighlight.style.left = percent1 + '%';
            rangeHighlight.style.width = (percent2 - percent1) + '%';
        }

        minSlider.addEventListener('input', updateSlider);
        maxSlider.addEventListener('input', updateSlider);
        window.addEventListener('DOMContentLoaded', updateSlider);
    </script>
    @endpush
</div>
