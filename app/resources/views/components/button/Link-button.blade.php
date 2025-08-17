@props([
    'href',
    'color' => 'gray',
    'outline' => false,
])

@php
    $baseClasses = "inline-flex items-center gap-2 text-sm font-medium px-4 py-2 rounded-md transition focus:outline-none focus:ring-2 focus:ring-offset-2";
    $colors = [
        'gray' => 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500 border-gray-600',
        'red' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 border-red-600',
        'blue' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 border-blue-600',
    ];

    $outlineClasses = $outline 
        ? "border-2 border-{$color}-600 text-{$color}-600 hover:bg-{$color}-600 hover:text-white focus:ring-{$color}-500" 
        : $colors[$color];
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => "$baseClasses $outlineClasses"]) }}>
    {{ $slot }}
</a>
