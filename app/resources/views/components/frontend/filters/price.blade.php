<div x-data="priceFilter({ 
    min: {{ request('min', 0) }}, 
    max: {{ request('max', 500) }}, 
    minLimit: 0, 
    maxLimit: 500 
})"
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

        {{-- Hidden min/max inputs --}}
        <input type="hidden" name="min" x-model="min">
        <input type="hidden" name="max" x-model="max">

        {{-- Range sliders --}}
        <div class="relative pt-2">
            <input type="range" min="0" max="500" x-model.number="min" :max="max"
                   class="w-full h-2 bg-gradient-to-r from-morocco-red to-morocco-blue rounded-lg appearance-none cursor-pointer accent-morocco-red">

            <input type="range" min="0" max="500" x-model.number="max" :min="min"
                   class="w-full h-2 bg-gradient-to-r from-morocco-blue to-morocco-green rounded-lg appearance-none cursor-pointer accent-morocco-red mt-2">
        </div>

        {{-- Labels --}}
        <div class="flex justify-between text-gray-700 font-medium text-sm">
            <span x-text="`$${min}`"></span>
            <span x-text="`$${max}`"></span>
        </div>

        {{-- Submit button using PrimaryButton --}}
        <x-button.primary-button color="primary" type="submit" class="w-full">
            GO
        </x-button.primary-button>        
    </form>
</div>
</div>

{{-- Alpine.js --}}
<script>
function priceFilter({ min, max, minLimit, maxLimit }) {
    return {
        open: true,
        min: min,
        max: max,
        minLimit: minLimit,
        maxLimit: maxLimit,
    }
}
</script>
