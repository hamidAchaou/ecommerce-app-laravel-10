@props(['category'])
{{-- @dd($category) --}}
<div class="group relative bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
    <div class="aspect-w-3 aspect-h-2 bg-gray-200 group-hover:opacity-75 sm:aspect-none sm:h-64">
        <img src="{{ $category->image ?? 'https://via.placeholder.com/400x300' }}" 
             alt="{{ $category->name }}" 
             class="w-full h-full object-center object-cover sm:w-full sm:h-full">
    </div>
    <div class="p-4">
        <h3 class="text-lg font-medium text-gray-900">
            @if($category && $category->slug)
                <a href="">
                    <span aria-hidden="true" class="absolute inset-0"></span>
                    {{ $category->name }}
                </a>
            @else
                <span>{{ $category->name ?? 'Category' }}</span>
            @endif
        </h3>
        <p class="mt-1 text-sm text-gray-500">
            {{ $category->description ?? 'Browse products in this category' }}
        </p>
    </div>
</div>