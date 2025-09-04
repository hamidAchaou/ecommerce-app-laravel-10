<div class="bg-white rounded-2xl shadow-lg p-6 sticky top-40 hover:shadow-2xl transition-shadow duration-300">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
    <form action="{{ route('products.index') }}" method="GET" class="space-y-2">

        {{-- Preserve search & price --}}
        @php
            use Illuminate\Support\Arr;
            $params = request()->except(['category', 'page']);
        @endphp

        @foreach ($params as $name => $value)
            @foreach (Arr::wrap($value) as $v)
                @if (!is_array($v))
                    <input type="hidden" name="{{ $name }}{{ is_array($value) ? '[]' : '' }}"
                        value="{{ $v }}">
                @endif
            @endforeach
        @endforeach


        @foreach ($categories as $category)
            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-red-50 cursor-pointer transition">
                <input type="checkbox" name="category[]" value="{{ $category->id }}" onchange="this.form.submit()"
                    {{ in_array($category->id, request('category', [])) ? 'checked' : '' }}
                    class="text-red-600 focus:ring-red-500 border-gray-300">
                <span class="text-gray-700 font-medium">{{ $category->name }}</span>
            </label>
        @endforeach

        {{-- All categories option --}}
        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-red-50 cursor-pointer transition">
            <input type="checkbox" name="category[]" value="" onchange="this.form.submit()"
                {{ empty(request('category')) ? 'checked' : '' }}
                class="text-red-600 focus:ring-red-500 border-gray-300">
            <span class="text-gray-700 font-medium">All Categories</span>
        </label>
    </form>
</div>
