<div class="bg-white rounded-2xl shadow-lg p-6 sticky top-20 hover:shadow-2xl transition-shadow duration-300">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Search</h3>
    <form action="{{ route('products.index') }}" method="GET" class="flex items-center gap-2">

        {{-- Preserve other filters --}}
        @foreach(request()->except(['search','page']) as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach

        <div class="relative w-full">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 px-4 py-2 pl-10 text-sm">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>

        <button type="submit"
            class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition">
            <span class="hidden sm:inline">Search</span>
        </button>
    </form>
</div>
