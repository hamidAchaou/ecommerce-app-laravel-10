@props([
    'route',
    'queryName' => 'search',
    'placeholder' => 'Rechercher...',
])

<form method="GET" action="{{ route($route) }}" {{ $attributes->merge(['class' => 'flex w-full md:w-auto']) }}>
    <input 
        type="text" 
        name="{{ $queryName }}" 
        value="{{ request($queryName) }}" 
        placeholder="{{ $placeholder }}" 
        class="flex-grow border border-gray-300 rounded-l-md p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
    />
    <button 
        type="submit" 
        class="bg-indigo-600 text-white px-5 py-3 rounded-r-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
    >
        Rechercher
    </button>
</form>
