@props([
    'route',
    'queryName' => 'search',
    'placeholder' => 'Rechercher...',
])

<form method="GET" action="{{ route($route) }}" {{ $attributes->merge(['class' => 'flex w-full md:w-auto']) }}>
    <div class="relative w-full">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
            <i class="fas fa-search"></i>
        </span>
        <input 
            type="text" 
            name="{{ $queryName }}" 
            value="{{ request($queryName) }}" 
            placeholder="{{ $placeholder }}" 
            class="pl-10 pr-4 py-3 w-full md:w-64 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
        />
    </div>
    <button 
        type="submit" 
        class="bg-indigo-600 text-white px-5 py-3 rounded-r-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition whitespace-nowrap"
    >
         Rechercher
    </button>
</form>
