@props(['category'])

<div class="group relative bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
    
    {{-- Category Image --}}
    <div class="aspect-w-3 aspect-h-2 bg-gray-200 sm:aspect-none sm:h-64">
        <img 
            src="{{ $category->image ?? asset('assets/images/placeholder.png') }}" 
            alt="{{ $category->name ?? 'Moroccan handcrafted product category' }}" 
            loading="lazy"
            class="w-full h-40 sm:h-48 md:h-56 lg:h-64 object-center object-cover transition duration-300 ease-in-out group-hover:scale-105"
        >
    </div>

    {{-- Category Content --}}
    <div class="p-4">
        <h2 class="text-lg font-semibold text-gray-900">
            @if($category && $category->slug)
                <a href="{{ route('categories.show', $category->slug) }}" class="hover:text-red-600">
                    <span aria-hidden="true" class="absolute inset-0"></span>
                    {{ $category->name }}
                </a>
            @else
                <span>{{ $category->name ?? 'Category' }}</span>
            @endif
        </h2>
        <p class="mt-2 text-sm text-gray-600 leading-relaxed">
            {{ $category->description ?? 'Explore authentic Moroccan handcrafted goods in this category.' }}
        </p>
    </div>
</div>
