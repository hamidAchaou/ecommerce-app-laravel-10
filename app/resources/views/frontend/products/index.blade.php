@extends('layouts.app')

@section('title', 'Our Products')
@section('meta_description', 'Browse our curated selection of Moroccan crafts, pottery, weaving, woodwork, jewelry, and
    leatherwork. Filter by price and category for an easier shopping experience.')

@section('content')
    <div class="container mx-auto px-4 py-12 grid lg:grid-cols-4 gap-8">

        {{-- Sidebar Filters --}}
        <aside class="space-y-6 lg:col-span-1">

            <!-- Search Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-20 hover:shadow-2xl transition-shadow duration-300">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Search</h3>
                <form action="{{ route('products.index') }}" method="GET" class="flex items-center gap-2">
                    <!-- Input -->
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 px-4 py-2 pl-10 text-sm">
                        <!-- Search Icon inside input -->
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>

                    <!-- Button -->
                    <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition">
                        <span class="hidden sm:inline">Search</span>
                    </button>
                </form>
            </div>

            <!-- Category Filter Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-40 hover:shadow-2xl transition-shadow duration-300">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
                <form action="{{ route('products.index') }}" method="GET" class="space-y-2">
                    @foreach ($categories as $category)
                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-red-50 cursor-pointer transition">
                            <input type="radio" name="category" value="{{ $category->id }}" onchange="this.form.submit()"
                                {{ request('category') == $category->id ? 'checked' : '' }}
                                class="text-red-600 focus:ring-red-500 border-gray-300">
                            <span class="text-gray-700 font-medium">{{ $category->name }}</span>
                        </label>
                    @endforeach
                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-red-50 cursor-pointer transition">
                        <input type="radio" name="category" value="" onchange="this.form.submit()"
                            {{ request('category') == '' ? 'checked' : '' }}
                            class="text-red-600 focus:ring-red-500 border-gray-300">
                        <span class="text-gray-700 font-medium">All Categories</span>
                    </label>
                </form>
            </div>

            <!-- Price Filter Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24 hover:shadow-2xl transition-shadow duration-300">
                <h3 class="text-xl font-bold text-gray-900 mb-5 border-b pb-3">Filter by Price</h3>

                <form action="{{ route('products.index') }}" method="GET" class="space-y-4">

                    {{-- Hidden fields to preserve other filters --}}
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="category" value="{{ request('category') }}">

                    <div class="relative h-2 bg-red-200 rounded-full">
                        <input type="range" min="0" max="500" value="{{ request('min') ?? 0 }}"
                            id="minSlider" class="absolute w-full h-2 bg-transparent appearance-none pointer-events-none">
                        <input type="range" min="0" max="500" value="{{ request('max') ?? 500 }}"
                            id="maxSlider" class="absolute w-full h-2 bg-transparent appearance-none pointer-events-none">
                        <div id="rangeHighlight" class="absolute h-2 bg-red-600 rounded-full"></div>
                    </div>

                    <input type="hidden" name="min" id="minPrice" value="{{ request('min') ?? 0 }}">
                    <input type="hidden" name="max" id="maxPrice" value="{{ request('max') ?? 500 }}">

                    <div class="flex justify-between text-gray-700 font-medium text-sm">
                        <span id="minLabel">${{ request('min') ?? 0 }}</span>
                        <span id="maxLabel">${{ request('max') ?? 500 }}</span>
                    </div>

                    <button type="submit"
                        class="w-full bg-red-600 text-white font-semibold py-2 rounded-xl shadow hover:bg-red-700 transition">
                        Apply Filter
                    </button>
                </form>
            </div>

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

                        // Update the values of the hidden inputs
                        minPrice.value = minVal;
                        maxPrice.value = maxVal;

                        // Update the labels
                        minLabel.textContent = "$" + minVal;
                        maxLabel.textContent = "$" + maxVal;

                        // Update the range highlight
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

        </aside>

        {{-- Products Grid --}}
        <div class="lg:col-span-3">
            @if ($products->isEmpty())
                <p class="text-center text-gray-500 mt-10">No products available at the moment.</p>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($products as $product)
                        <x-frontend.product.card :product="$product" />
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-10 flex justify-center">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
