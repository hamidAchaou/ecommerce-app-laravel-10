@props(['message'])

@if(session('success') || $message)
    <div {{ $attributes->merge(['class' => 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 flex items-center']) }} role="alert">
        <svg class="fill-current w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5-5 1.41-1.41L10 12.17l7.59-7.59L19 6l-9 9z"/></svg>
        <span class="block sm:inline">{{ session('success') ?? $message }}</span>
    </div>
@endif
